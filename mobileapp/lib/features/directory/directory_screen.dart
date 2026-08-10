import 'dart:async';

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../shared/business_card_tile.dart';
import '../shell/app_drawer.dart';
import 'business_detail_screen.dart';

class DirectoryScreen extends StatefulWidget {
  const DirectoryScreen({super.key});

  @override
  State<DirectoryScreen> createState() => _DirectoryScreenState();
}

class _DirectoryScreenState extends State<DirectoryScreen> {
  final _searchCtrl = TextEditingController();
  Timer? _debounce;
  bool _loading = true;
  String? _error;
  List<dynamic> _items = [];
  List<dynamic> _categories = [];
  String? _category;
  int _page = 1;
  int _totalPages = 1;

  @override
  void initState() {
    super.initState();
    _searchCtrl.addListener(_onSearchChanged);
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      context.read<AppState>().addListener(_onAppStateChanged);
      await _loadCategories();
      _consumePendingSearch();
      await _load();
    });
  }

  @override
  void dispose() {
    _debounce?.cancel();
    try {
      context.read<AppState>().removeListener(_onAppStateChanged);
    } catch (_) {}
    _searchCtrl.removeListener(_onSearchChanged);
    _searchCtrl.dispose();
    super.dispose();
  }

  void _onAppStateChanged() {
    if (!mounted) return;
    if (_consumePendingSearch()) _load();
  }

  bool _consumePendingSearch() {
    final pending = context.read<AppState>().takePendingDirectoryQuery();
    if (pending == null || pending.isEmpty) return false;
    _debounce?.cancel();
    _searchCtrl.text = pending;
    return true;
  }

  void _onSearchChanged() {
    setState(() {});
    _debounce?.cancel();
    _debounce = Timer(const Duration(milliseconds: 400), () {
      if (mounted) _load();
    });
  }

  Future<void> _loadCategories() async {
    try {
      final res = await context.read<AppState>().api.get('categories');
      setState(() => _categories = (res['data'] as List?) ?? []);
    } catch (_) {}
  }

  Future<void> _load({bool reset = true}) async {
    if (reset) _page = 1;
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final query = <String, String>{
        'page': '$_page',
        'per_page': '20',
        if (_searchCtrl.text.trim().isNotEmpty) 'q': _searchCtrl.text.trim(),
        if (_category != null && _category!.isNotEmpty) 'category': _category!,
      };
      final res = await context.read<AppState>().api.get('businesses', query: query);
      final data = res['data'] as Map<String, dynamic>;
      setState(() {
        final next = (data['items'] as List?) ?? [];
        _items = reset ? next : [..._items, ...next];
        final pages = data['total_pages'];
        _totalPages = pages is int ? pages : int.tryParse('$pages') ?? 1;
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
        leading: const DrawerMenuButton(),
        title: Text(app.t(en: 'Directory', ur: 'ڈائریکٹری')),
      ),
      body: Column(
        children: [
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
                hintText: app.t(en: 'Search businesses, phone...', ur: 'کاروبار یا نمبر تلاش کریں...'),
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
          SizedBox(
            height: 44,
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              children: [
                Padding(
                  padding: const EdgeInsetsDirectional.only(end: 8),
                  child: ChoiceChip(
                    label: Text(app.t(en: 'All', ur: 'سب')),
                    selected: _category == null,
                    onSelected: (_) {
                      setState(() => _category = null);
                      _load();
                    },
                  ),
                ),
                ..._categories.map((raw) {
                  final c = raw as Map<String, dynamic>;
                  final id = '${c['id']}';
                  final label = app.isUrdu ? '${c['name_ur'] ?? c['name']}' : '${c['name_en'] ?? c['name']}';
                  return Padding(
                    padding: const EdgeInsetsDirectional.only(end: 8),
                    child: ChoiceChip(
                      label: Text(label),
                      selected: _category == id,
                      onSelected: (_) {
                        setState(() => _category = id);
                        _load();
                      },
                    ),
                  );
                }),
              ],
            ),
          ),
          const SizedBox(height: 8),
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
                          TextButton(onPressed: _load, child: Text(app.t(en: 'Retry', ur: 'دوبارہ کوشش'))),
                        ])
                      : _items.isEmpty
                          ? ListView(children: [
                              const SizedBox(height: 80),
                              Center(child: Text(app.t(en: 'No results found', ur: 'کوئی نتیجہ نہیں ملا'))),
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
                                        builder: (_) => BusinessDetailScreen(idOrSlug: '${b['id'] ?? b['slug'] ?? ''}'),
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
