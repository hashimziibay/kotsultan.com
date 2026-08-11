import 'dart:async';

import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../api/api_client.dart';
import '../cache/local_cache.dart';
import '../constants.dart';
import '../data/catalog_repository.dart';

class AppUser {
  AppUser({
    required this.id,
    required this.name,
    required this.phone,
    required this.locale,
    required this.theme,
  });

  factory AppUser.fromJson(Map<String, dynamic> json) {
    return AppUser(
      id: json['id'] is int ? json['id'] as int : int.tryParse('${json['id']}') ?? 0,
      name: '${json['name'] ?? ''}',
      phone: '${json['phone'] ?? ''}',
      locale: '${json['locale'] ?? 'en'}',
      theme: '${json['theme'] ?? 'light'}',
    );
  }

  final int id;
  final String name;
  final String phone;
  final String locale;
  final String theme;

  AppUser copyWith({
    int? id,
    String? name,
    String? phone,
    String? locale,
    String? theme,
  }) {
    return AppUser(
      id: id ?? this.id,
      name: name ?? this.name,
      phone: phone ?? this.phone,
      locale: locale ?? this.locale,
      theme: theme ?? this.theme,
    );
  }
}

class AppState extends ChangeNotifier {
  AppState(this.api) : catalog = CatalogRepository(api, LocalCache());

  final ApiClient api;
  final CatalogRepository catalog;

  bool ready = false;
  bool onboarded = false;
  bool pendingUserSync = false;
  bool offlineMode = false;
  bool isOnline = true;
  bool cacheRefreshing = false;
  /// Bumped after a successful online cache refresh so screens reload fresh data.
  int catalogEpoch = 0;
  AppUser? user;
  String locale = 'en';
  ThemeMode themeMode = ThemeMode.light;
  String? error;
  /// One-shot query from Home search → Directory tab.
  String? pendingDirectoryQuery;

  StreamSubscription<List<ConnectivityResult>>? _connectivitySub;
  bool _wasOffline = false;
  bool _refreshQueued = false;

  bool get isUrdu => locale == 'ur';
  bool get isRtl => isUrdu;

  static bool _hasConnection(List<ConnectivityResult> results) {
    return results.any((r) => r != ConnectivityResult.none);
  }

  Future<void> bootstrap() async {
    final prefs = await SharedPreferences.getInstance();
    onboarded = prefs.getBool(AppConstants.prefOnboarded) ?? false;
    pendingUserSync = prefs.getBool(AppConstants.prefPendingUserSync) ?? false;
    locale = prefs.getString(AppConstants.prefLocale) ?? 'en';
    final theme = prefs.getString(AppConstants.prefTheme) ?? 'light';
    themeMode = theme == 'dark' ? ThemeMode.dark : ThemeMode.light;
    api.locale = locale;
    api.token = prefs.getString(AppConstants.prefToken);

    final name = prefs.getString(AppConstants.prefName);
    final phone = prefs.getString(AppConstants.prefPhone);
    if (onboarded && name != null && phone != null) {
      user = AppUser(
        id: prefs.getInt(AppConstants.prefUserId) ?? 0,
        name: name,
        phone: phone,
        locale: locale,
        theme: theme,
      );
    }

    if (api.token != null && api.token!.isNotEmpty) {
      try {
        final res = await api.get('auth/me');
        final data = res['data'] as Map<String, dynamic>;
        user = AppUser.fromJson(data);
        pendingUserSync = false;
        await prefs.setBool(AppConstants.prefPendingUserSync, false);
        await _persistUser(user!);
      } catch (_) {
        // Keep local session; sync later if needed.
      }
    }

    if (pendingUserSync && user != null) {
      await syncPendingUser();
    }

    await _startConnectivityWatcher();

    if (isOnline) {
      // ignore: unawaited_futures
      refreshOfflineCache();
    }

    ready = true;
    notifyListeners();
  }

  Future<void> _startConnectivityWatcher() async {
    try {
      final current = await Connectivity().checkConnectivity();
      isOnline = _hasConnection(current);
      _wasOffline = !isOnline;
      offlineMode = !isOnline;
    } catch (_) {
      isOnline = true;
      _wasOffline = false;
    }

    await _connectivitySub?.cancel();
    _connectivitySub = Connectivity().onConnectivityChanged.listen((results) {
      final online = _hasConnection(results);
      final cameBackOnline = online && _wasOffline;
      isOnline = online;
      _wasOffline = !online;

      if (!online) {
        offlineMode = true;
        notifyListeners();
        return;
      }

      if (cameBackOnline) {
        // ignore: unawaited_futures
        onBackOnline();
      }
    });
  }

  /// Called when connectivity is restored (or app resumes while online).
  Future<void> onBackOnline() async {
    await syncPendingUser();
    await refreshOfflineCache();
  }

  /// Pull latest directory data from server and replace the offline cache.
  Future<void> refreshOfflineCache() async {
    if (!isOnline) return;
    if (_refreshQueued || cacheRefreshing) return;
    _refreshQueued = true;
    cacheRefreshing = true;
    notifyListeners();
    try {
      final updated = await catalog.refreshCatalog();
      if (updated) {
        offlineMode = false;
        catalogEpoch++;
      }
    } finally {
      cacheRefreshing = false;
      _refreshQueued = false;
      notifyListeners();
    }
  }

  /// First-time setup: save locally and register with admin when online.
  /// App stays usable even if registration fails (no internet).
  Future<void> completeOnboarding({
    required String name,
    required String phone,
    required String localeCode,
    required String theme,
  }) async {
    error = null;
    notifyListeners();

    locale = localeCode;
    api.locale = localeCode;
    themeMode = theme == 'dark' ? ThemeMode.dark : ThemeMode.light;
    user = AppUser(id: 0, name: name, phone: phone, locale: localeCode, theme: theme);
    onboarded = true;

    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(AppConstants.prefOnboarded, true);
    await _persistUser(user!);

    final synced = await _registerWithServer(
      name: name,
      phone: phone,
      localeCode: localeCode,
      theme: theme,
    );
    pendingUserSync = !synced;
    await prefs.setBool(AppConstants.prefPendingUserSync, pendingUserSync);
    notifyListeners();
  }

  Future<bool> syncPendingUser() async {
    if (user == null) return false;
    final synced = await _registerWithServer(
      name: user!.name,
      phone: user!.phone,
      localeCode: user!.locale,
      theme: user!.theme,
    );
    if (synced) {
      pendingUserSync = false;
      final prefs = await SharedPreferences.getInstance();
      await prefs.setBool(AppConstants.prefPendingUserSync, false);
      notifyListeners();
    }
    return synced;
  }

  Future<bool> _registerWithServer({
    required String name,
    required String phone,
    required String localeCode,
    required String theme,
  }) async {
    try {
      final res = await api.post('auth/register', body: {
        'name': name,
        'phone': phone,
        'locale': localeCode,
        'theme': theme,
      });
      final data = res['data'] as Map<String, dynamic>;
      api.token = data['token'] as String?;
      user = AppUser.fromJson(data['user'] as Map<String, dynamic>);
      locale = user!.locale;
      api.locale = locale;
      themeMode = user!.theme == 'dark' ? ThemeMode.dark : ThemeMode.light;

      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(AppConstants.prefToken, api.token ?? '');
      await _persistUser(user!);
      // ignore: unawaited_futures
      refreshOfflineCache();
      return true;
    } catch (e) {
      error = e.toString();
      return false;
    }
  }

  Future<void> updateProfile({
    String? name,
    String? phone,
    String? localeCode,
    String? theme,
  }) async {
    error = null;
    notifyListeners();

    final next = (user ?? AppUser(id: 0, name: '', phone: '', locale: locale, theme: themeMode == ThemeMode.dark ? 'dark' : 'light')).copyWith(
      name: name,
      phone: phone,
      locale: localeCode,
      theme: theme,
    );
    user = next;
    if (localeCode != null) {
      locale = localeCode;
      api.locale = localeCode;
    }
    if (theme != null) {
      themeMode = theme == 'dark' ? ThemeMode.dark : ThemeMode.light;
    }
    await _persistUser(next);

    final body = <String, dynamic>{};
    if (name != null) body['name'] = name;
    if (phone != null) body['phone'] = phone;
    if (localeCode != null) body['locale'] = localeCode;
    if (theme != null) body['theme'] = theme;

    try {
      if (api.token != null && api.token!.isNotEmpty) {
        final res = await api.put('auth/me', body: body);
        final data = res['data'] as Map<String, dynamic>;
        user = AppUser.fromJson(data);
        locale = user!.locale;
        api.locale = locale;
        themeMode = user!.theme == 'dark' ? ThemeMode.dark : ThemeMode.light;
        await _persistUser(user!);
        pendingUserSync = false;
      } else {
        final synced = await _registerWithServer(
          name: next.name,
          phone: next.phone,
          localeCode: next.locale,
          theme: next.theme,
        );
        pendingUserSync = !synced;
      }
      final prefs = await SharedPreferences.getInstance();
      await prefs.setBool(AppConstants.prefPendingUserSync, pendingUserSync);
      notifyListeners();
    } catch (e) {
      pendingUserSync = true;
      final prefs = await SharedPreferences.getInstance();
      await prefs.setBool(AppConstants.prefPendingUserSync, true);
      error = e.toString();
      notifyListeners();
    }
  }

  Future<void> setLocaleLocal(String code) async {
    locale = code;
    api.locale = code;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.prefLocale, code);
    notifyListeners();
  }

  Future<void> setThemeLocal(String theme) async {
    themeMode = theme == 'dark' ? ThemeMode.dark : ThemeMode.light;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.prefTheme, theme);
    notifyListeners();
  }

  Future<void> _persistUser(AppUser u) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt(AppConstants.prefUserId, u.id);
    await prefs.setString(AppConstants.prefName, u.name);
    await prefs.setString(AppConstants.prefPhone, u.phone);
    await prefs.setString(AppConstants.prefLocale, u.locale);
    await prefs.setString(AppConstants.prefTheme, u.theme);
  }

  String t({required String en, required String ur}) => isUrdu ? ur : en;

  void setPendingDirectoryQuery(String query) {
    pendingDirectoryQuery = query;
    notifyListeners();
  }

  String? takePendingDirectoryQuery() {
    final q = pendingDirectoryQuery;
    pendingDirectoryQuery = null;
    return q;
  }

  void setOfflineBanner(bool value) {
    if (offlineMode == value) return;
    offlineMode = value;
    notifyListeners();
  }

  @override
  void dispose() {
    _connectivitySub?.cancel();
    super.dispose();
  }
}
