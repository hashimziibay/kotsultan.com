import 'dart:async';

import '../api/api_client.dart';
import '../cache/local_cache.dart';

class CatalogResult<T> {
  CatalogResult(this.data, {this.fromCache = false});
  final T data;
  final bool fromCache;
}

/// Fetches catalog data online and falls back to a local cache when offline.
class CatalogRepository {
  CatalogRepository(this.api, this.cache);

  final ApiClient api;
  final LocalCache cache;

  static const _homeKey = 'home';
  static const _categoriesKey = 'categories';
  static const _businessesKey = 'businesses_all';
  static const _emergencyKey = 'emergency_all';
  static const _wallKey = 'wall_all';
  static const _bizDetailPrefix = 'biz_';
  static const _wallDetailPrefix = 'wall_';

  bool _prefetching = false;

  Future<CatalogResult<Map<String, dynamic>>> getHome() async {
    try {
      final res = await api.get('home');
      final data = Map<String, dynamic>.from(res['data'] as Map);

      // Older live /home builds only return 8 popular categories; merge full list.
      try {
        final cats = await getCategories();
        if (cats.data.length >
            List.from((data['popular_categories'] as List?) ?? const []).length) {
          data['categories'] = cats.data;
          data['popular_categories'] = cats.data;
        }
      } catch (_) {}

      await cache.putJson(_homeKey, data);
      // Background catalog refresh is owned by AppState — avoid nested refreshes here.
      return CatalogResult(data);
    } catch (e) {
      final cached = await cache.getMap(_homeKey);
      if (cached != null) return CatalogResult(cached, fromCache: true);
      rethrow;
    }
  }

  Future<CatalogResult<List<dynamic>>> getCategories() async {
    try {
      final res = await api.get('categories');
      final data = List<dynamic>.from((res['data'] as List?) ?? const []);
      await cache.putJson(_categoriesKey, data);
      return CatalogResult(data);
    } catch (e) {
      final cached = await cache.getList(_categoriesKey);
      if (cached != null) return CatalogResult(cached, fromCache: true);
      rethrow;
    }
  }

  Future<CatalogResult<Map<String, dynamic>>> getBusinesses({
    String? q,
    String? category,
    String? tag,
    int page = 1,
    int perPage = 20,
  }) async {
    try {
      final query = <String, String>{
        'page': '$page',
        'per_page': '$perPage',
        if (q != null && q.trim().isNotEmpty) 'q': q.trim(),
        if (category != null && category.isNotEmpty) 'category': category,
        if (tag != null && tag.isNotEmpty) 'tag': tag,
      };
      final res = await api.get('businesses', query: query);
      final data = Map<String, dynamic>.from(res['data'] as Map);
      final items = List<dynamic>.from((data['items'] as List?) ?? const []);
      final suggestions = List<dynamic>.from((data['suggestions'] as List?) ?? const []);
      await _mergeBusinesses([...items, ...suggestions]);
      unawaited(prefetchCatalog());
      return CatalogResult(data);
    } catch (e) {
      final offline = await _localBusinessPage(
        q: q,
        category: category,
        page: page,
        perPage: perPage,
      );
      if (offline != null) {
        offline['suggestions'] = const [];
        offline['suggested_tags'] = const [];
        return CatalogResult(offline, fromCache: true);
      }
      rethrow;
    }
  }

  Future<CatalogResult<Map<String, dynamic>>> getBusiness(String idOrSlug) async {
    final key = '$_bizDetailPrefix$idOrSlug';
    try {
      final res = await api.get('businesses/$idOrSlug');
      final data = Map<String, dynamic>.from(res['data'] as Map);
      await cache.putJson(key, data);
      return CatalogResult(data);
    } catch (e) {
      final cached = await cache.getMap(key);
      if (cached != null) return CatalogResult(cached, fromCache: true);

      final all = await cache.getList(_businessesKey) ?? const [];
      for (final raw in all) {
        if (raw is! Map) continue;
        final m = Map<String, dynamic>.from(raw);
        if ('${m['id']}' == idOrSlug || '${m['slug']}' == idOrSlug) {
          return CatalogResult(m, fromCache: true);
        }
      }
      rethrow;
    }
  }

  Future<CatalogResult<Map<String, dynamic>>> getEmergency({
    String? q,
    String? category,
  }) async {
    try {
      final query = <String, String>{
        'per_page': '100',
        if (q != null && q.trim().isNotEmpty) 'q': q.trim(),
        if (category != null && category.isNotEmpty) 'category': category,
      };
      final res = await api.get('emergency', query: query);
      final data = Map<String, dynamic>.from((res['data'] as Map?) ?? {});
      // Keep an unfiltered snapshot for offline search.
      if ((q == null || q.trim().isEmpty) && (category == null || category.isEmpty)) {
        await cache.putJson(_emergencyKey, data);
      }
      return CatalogResult(data);
    } catch (e) {
      final cached = await cache.getMap(_emergencyKey);
      if (cached == null) rethrow;
      final filtered = _filterEmergency(cached, q: q, category: category);
      return CatalogResult(filtered, fromCache: true);
    }
  }

  Future<CatalogResult<Map<String, dynamic>>> getWall({
    String? q,
    String? category,
    int perPage = 30,
  }) async {
    try {
      final query = <String, String>{
        'per_page': '$perPage',
        if (q != null && q.trim().isNotEmpty) 'q': q.trim(),
        if (category != null && category.isNotEmpty) 'category': category,
      };
      final res = await api.get('wall', query: query);
      final data = Map<String, dynamic>.from(res['data'] as Map);
      // Keep an unfiltered snapshot for offline browse/search.
      if ((q == null || q.trim().isEmpty) && (category == null || category.isEmpty)) {
        await cache.putJson(_wallKey, data);
      } else {
        final items = List<dynamic>.from((data['items'] as List?) ?? const []);
        await _mergeWall(items);
      }
      return CatalogResult(data);
    } catch (e) {
      final cached = await cache.getMap(_wallKey);
      if (cached == null) rethrow;
      final filtered = _filterWall(cached, q: q, category: category);
      return CatalogResult(filtered, fromCache: true);
    }
  }

  Future<CatalogResult<Map<String, dynamic>>> getWallItem(String idOrSlug) async {
    final key = '$_wallDetailPrefix${idOrSlug}_v2';
    try {
      final res = await api.get('wall/$idOrSlug');
      final data = Map<String, dynamic>.from(res['data'] as Map);
      await cache.putJson(key, data);
      return CatalogResult(data);
    } catch (e) {
      final cached = await cache.getMap(key);
      if (cached != null) return CatalogResult(cached, fromCache: true);

      final wall = await cache.getMap(_wallKey);
      final items = List<dynamic>.from((wall?['items'] as List?) ?? const []);
      for (final raw in items) {
        if (raw is! Map) continue;
        final m = Map<String, dynamic>.from(raw);
        if ('${m['id']}' == idOrSlug || '${m['slug']}' == idOrSlug) {
          return CatalogResult(m, fromCache: true);
        }
      }
      rethrow;
    }
  }

  /// Full refresh of offline snapshots. Returns true if at least one source updated.
  Future<bool> refreshCatalog() async {
    if (_prefetching) return false;
    _prefetching = true;
    var updated = false;
    try {
      final results = await Future.wait([
        _prefetchHome().then((ok) => ok),
        _prefetchBusinesses().then((ok) => ok),
        _prefetchEmergency().then((ok) => ok),
        _prefetchWall().then((ok) => ok),
        _prefetchCategories().then((ok) => ok),
      ]);
      updated = results.any((ok) => ok);
    } finally {
      _prefetching = false;
    }
    return updated;
  }

  /// Download directory snapshots for offline browsing/search.
  Future<void> prefetchCatalog() async {
    await refreshCatalog();
  }

  Future<bool> _prefetchHome() async {
    try {
      final res = await api.get('home');
      final data = Map<String, dynamic>.from(res['data'] as Map);
      try {
        final catsRes = await api.get('categories');
        final cats = List<dynamic>.from((catsRes['data'] as List?) ?? const []);
        if (cats.length >
            List.from((data['popular_categories'] as List?) ?? const []).length) {
          data['categories'] = cats;
          data['popular_categories'] = cats;
        }
      } catch (_) {}
      await cache.putJson(_homeKey, data);
      return true;
    } catch (_) {
      return false;
    }
  }

  Future<bool> _prefetchCategories() async {
    try {
      final res = await api.get('categories');
      final data = List<dynamic>.from((res['data'] as List?) ?? const []);
      await cache.putJson(_categoriesKey, data);
      return true;
    } catch (_) {
      return false;
    }
  }

  Future<bool> _prefetchBusinesses() async {
    try {
      final all = <dynamic>[];
      var page = 1;
      var totalPages = 1;
      do {
        final res = await api.get('businesses', query: {
          'page': '$page',
          'per_page': '200',
        });
        final data = Map<String, dynamic>.from(res['data'] as Map);
        all.addAll((data['items'] as List?) ?? const []);
        final pages = data['total_pages'];
        totalPages = pages is int ? pages : int.tryParse('$pages') ?? 1;
        page++;
      } while (page <= totalPages && page <= 20);
      await cache.putJson(_businessesKey, all);
      return true;
    } catch (_) {
      return false;
    }
  }

  Future<bool> _prefetchEmergency() async {
    try {
      final res = await api.get('emergency', query: {'per_page': '100'});
      final data = Map<String, dynamic>.from((res['data'] as Map?) ?? {});
      await cache.putJson(_emergencyKey, data);
      return true;
    } catch (_) {
      return false;
    }
  }

  Future<bool> _prefetchWall() async {
    try {
      final res = await api.get('wall', query: {'per_page': '100'});
      final data = Map<String, dynamic>.from(res['data'] as Map);
      await cache.putJson(_wallKey, data);
      return true;
    } catch (_) {
      return false;
    }
  }

  Future<void> _mergeBusinesses(List<dynamic> items) async {
    if (items.isEmpty) return;
    final existing = await cache.getList(_businessesKey) ?? <dynamic>[];
    final byId = <String, Map<String, dynamic>>{};
    for (final raw in existing) {
      if (raw is Map) {
        final m = Map<String, dynamic>.from(raw);
        byId['${m['id']}'] = m;
      }
    }
    for (final raw in items) {
      if (raw is Map) {
        final m = Map<String, dynamic>.from(raw);
        byId['${m['id']}'] = m;
      }
    }
    await cache.putJson(_businessesKey, byId.values.toList());
  }

  Future<void> _mergeWall(List<dynamic> items) async {
    if (items.isEmpty) return;
    final cached = await cache.getMap(_wallKey) ?? <String, dynamic>{'items': <dynamic>[]};
    final existing = List<dynamic>.from((cached['items'] as List?) ?? const []);
    final byId = <String, Map<String, dynamic>>{};
    for (final raw in existing) {
      if (raw is Map) {
        final m = Map<String, dynamic>.from(raw);
        byId['${m['id']}'] = m;
      }
    }
    for (final raw in items) {
      if (raw is Map) {
        final m = Map<String, dynamic>.from(raw);
        byId['${m['id']}'] = m;
      }
    }
    cached['items'] = byId.values.toList();
    await cache.putJson(_wallKey, cached);
  }

  Future<Map<String, dynamic>?> _localBusinessPage({
    String? q,
    String? category,
    required int page,
    required int perPage,
  }) async {
    final all = await cache.getList(_businessesKey);
    if (all == null || all.isEmpty) return null;

    final filtered = all.whereType<Map>().map((e) => Map<String, dynamic>.from(e)).where((b) {
      if (category != null && category.isNotEmpty) {
        final idMatch = '${b['category_id']}' == category;
        final nameMatch = '${b['category']}'.toLowerCase() == category.toLowerCase();
        if (!idMatch && !nameMatch) return false;
      }
      final query = (q ?? '').trim().toLowerCase();
      if (query.isEmpty) return true;
      final hay = [
        b['name'],
        b['name_en'],
        b['name_ur'],
        b['owner_name'],
        b['phone'],
        b['whatsapp'],
        b['address'],
        b['category'],
      ].map((e) => '$e'.toLowerCase()).join(' ');
      return hay.contains(query);
    }).toList();

    final total = filtered.length;
    final totalPages = total == 0 ? 1 : ((total + perPage - 1) / perPage).floor();
    final start = (page - 1) * perPage;
    final items = start >= total ? <dynamic>[] : filtered.skip(start).take(perPage).toList();

    return {
      'items': items,
      'total': total,
      'page': page,
      'per_page': perPage,
      'total_pages': totalPages,
    };
  }

  Map<String, dynamic> _filterEmergency(
    Map<String, dynamic> cached, {
    String? q,
    String? category,
  }) {
    final items = List<dynamic>.from((cached['items'] as List?) ?? const []);
    final filtered = items.whereType<Map>().map((e) => Map<String, dynamic>.from(e)).where((c) {
      if (category != null && category.isNotEmpty) {
        final en = '${c['category_en'] ?? c['category'] ?? ''}'.trim();
        if (en != category) return false;
      }
      final query = (q ?? '').trim().toLowerCase();
      if (query.isEmpty) return true;
      final hay = [
        c['department'],
        c['department_en'],
        c['department_ur'],
        c['category'],
        c['category_en'],
        c['category_ur'],
        c['phone_primary'],
      ].map((e) => '$e'.toLowerCase()).join(' ');
      return hay.contains(query);
    }).toList();

    return {
      ...cached,
      'items': filtered,
    };
  }

  Map<String, dynamic> _filterWall(
    Map<String, dynamic> cached, {
    String? q,
    String? category,
  }) {
    final items = List<dynamic>.from((cached['items'] as List?) ?? const []);
    final query = (q ?? '').trim().toLowerCase();
    final cat = (category ?? '').trim().toLowerCase();
    final filtered = items.whereType<Map>().map((e) => Map<String, dynamic>.from(e)).where((p) {
      if (cat.isNotEmpty) {
        final idMatch = '${p['category_id']}'.toLowerCase() == cat;
        final slugMatch = '${p['category_slug'] ?? ''}'.toLowerCase() == cat;
        final nameMatch = '${p['category'] ?? ''}'.toLowerCase() == cat;
        if (!idMatch && !slugMatch && !nameMatch) return false;
      }
      if (query.isEmpty) return true;
      final hay = [
        p['name'],
        p['name_en'],
        p['name_ur'],
        p['profession'],
        p['category'],
        p['title'],
        p['title_en'],
        p['title_ur'],
        p['bio'],
      ].map((e) => '$e'.toLowerCase()).join(' ');
      return hay.contains(query);
    }).toList();
    return {
      ...cached,
      'items': filtered,
    };
  }
}
