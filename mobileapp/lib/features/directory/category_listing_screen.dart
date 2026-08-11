import 'dart:async';

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/offline_banner.dart';
import '../shared/business_card_tile.dart';
import 'business_detail_screen.dart';

class CategoryListingScreen extends StatefulWidget {
  const CategoryListingScreen({
    super.key,
    required this.categoryId,
    required this.categoryName,
  });

  final String categoryId;
  final String categoryName;

  @override
  State<CategoryListingScreen> createState() => _CategoryListingScreenState();
}

class _CategoryListingScreenState extends State<CategoryListingScreen> {
  final _searchCtrl = TextEditingController();
  Timer? _debounce;
  bool _loading = true;
  bool _fromCache = false;
  String? _error;
  List<dynamic> _items = [];
  int _page = 1;
  int _totalPages = 1;

  @override
  void initState() {
    super.initState();
    _searchCtrl.addListener(_onSearchChanged);
    WidgetsBinding.instance.addPostFrameCallback((_) => _load());
  }

  @override
  void dispose() {
    _debounce?.cancel();
    _searchCtrl.removeListener(_onSearchChanged);
    _searchCtrl.dispose();
    super.dispose();
  }

  void _onSearchChanged() {
    setState(() {});
    _debounce?.cancel();
    _debounce = Timer(const Duration(milliseconds: 400), () {
      if (mounted) _load();
    });
  }

  Future<void> _load({bool reset = true}) async {
    if (reset) _page = 1;
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final res = await context.read<AppState>().catalog.getBusinesses(
        q: _searchCtrl.text.trim().isEmpty ? null : _searchCtrl.text.trim(),
        category: widget.categoryId,
        page: _page,
        perPage: 20,
      );
      final data = res.data;
      setState(() {
        final next = (data['items'] as List?) ?? [];
        _items = reset ? next : [..._items, ...next];
        final pages = data['total_pages'];
        _totalPages = pages is int ? pages : int.tryParse('$pages') ?? 1;
        _fromCache = res.fromCache;
      });
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.categoryName),
      ),
      body: Column(
        children: [
          OfflineBanner(visible: _fromCache),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 8),
            child: TextField(
              controller: _searchCtrl,
              textInputAction: TextInputAction.search,
              onSubmitted: (_) {
                _debounce?.cancel();
                _load();
              },
              decoration: InputDecoration(
                hintText: app.t(
                  en: 'Search in ${widget.categoryName}...',
                  ur: '${widget.categoryName} میں تلاش کریں...',
                ),
                prefixIcon: const Icon(Icons.search_rounded, color: AppColors.emerald),
                suffixIcon: _searchCtrl.text.isEmpty
                    ? null
                    : IconButton(
                        onPressed: () {
                          _searchCtrl.clear();
                          _load();
                        },
                        icon: const Icon(Icons.close_rounded),
                      ),
              ),
            ),
          ),
          Expanded(
            child: RefreshIndicator(
              color: AppColors.emerald,
              onRefresh: _load,
              child: _loading && _items.isEmpty
                  ? const Center(child: CircularProgressIndicator(color: AppColors.emerald))
                  : _error != null
                      ? ListView(children: [
                          const SizedBox(height: 80),
                          Center(child: Text(_error!)),
                          TextButton(
                            onPressed: _load,
                            child: Text(app.t(en: 'Retry', ur: 'دوبارہ کوشش')),
                          ),
                        ])
                      : _items.isEmpty
                          ? ListView(children: [
                              const SizedBox(height: 80),
                              Center(
                                child: Text(
                                  app.t(en: 'No businesses in this category', ur: 'اس زمرے میں کوئی کاروبار نہیں'),
                                ),
                              ),
                            ])
                          : ListView.builder(
                              padding: const EdgeInsets.fromLTRB(16, 4, 16, 24),
                              itemCount: _items.length + (_page < _totalPages ? 1 : 0),
                              itemBuilder: (context, i) {
                                if (i == _items.length) {
                                  return Padding(
                                    padding: const EdgeInsets.symmetric(vertical: 8),
                                    child: OutlinedButton(
                                      onPressed: () {
                                        setState(() => _page++);
                                        _load(reset: false);
                                      },
                                      child: Text(app.t(en: 'Load more', ur: 'مزید دیکھیں')),
                                    ),
                                  );
                                }
                                final b = _items[i] as Map<String, dynamic>;
                                return Padding(
                                  padding: const EdgeInsets.only(bottom: 12),
                                  child: BusinessCardTile(
                                    item: b,
                                    isUrdu: app.isUrdu,
                                    onTap: () {
                                      Navigator.of(context).push(MaterialPageRoute(
                                        builder: (_) => BusinessDetailScreen(
                                          idOrSlug: '${b['id'] ?? b['slug'] ?? ''}',
                                        ),
                                      ));
                                    },
                                  ),
                                );
                              },
                            ),
            ),
          ),
        ],
      ),
    );
  }
}
