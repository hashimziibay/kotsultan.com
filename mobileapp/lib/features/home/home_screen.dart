import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/offline_banner.dart';
import '../directory/business_detail_screen.dart';
import '../directory/category_listing_screen.dart';
import '../shared/business_card_tile.dart';
import '../shell/app_drawer.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  static const _featuredCount = 4;
  static const _signatureUr = 'کوٹ سلطان، دلوں میں بستا ایک چھوٹا سا جہان';

  final _searchCtrl = TextEditingController();
  bool _loading = true;
  bool _fromCache = false;
  String? _error;
  Map<String, dynamic>? _data;
  List<dynamic> _categories = [];
  int _seenEpoch = -1;
  int _loadSeq = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final app = context.read<AppState>();
      app.addListener(_onAppChanged);
      _seenEpoch = app.catalogEpoch;
      _load();
    });
  }

  @override
  void dispose() {
    try {
      context.read<AppState>().removeListener(_onAppChanged);
    } catch (_) {}
    _searchCtrl.dispose();
    super.dispose();
  }

  void _onAppChanged() {
    if (!mounted) return;
    final epoch = context.read<AppState>().catalogEpoch;
    if (epoch != _seenEpoch) {
      _seenEpoch = epoch;
      // Background cache refresh finished — update quietly, keep current UI.
      _load(silent: true);
    }
  }

  List<dynamic> _extractCategories(Map<String, dynamic> data) {
    return List<dynamic>.from(
      (data['categories'] as List?) ??
          (data['popular_categories'] as List?) ??
          const [],
    );
  }

  Future<void> _load({bool silent = false}) async {
    final seq = ++_loadSeq;
    final showSpinner = !silent && _data == null;
    if (showSpinner) {
      setState(() {
        _loading = true;
        _error = null;
      });
    } else if (!silent && mounted) {
      setState(() => _error = null);
    }

    try {
      final app = context.read<AppState>();
      if (app.pendingUserSync) {
        // ignore: unawaited_futures
        app.syncPendingUser();
      }
      final res = await app.catalog.getHome();
      if (!mounted || seq != _loadSeq) return;
      setState(() {
        _data = res.data;
        _categories = _extractCategories(res.data);
        _fromCache = res.fromCache;
        _loading = false;
        _error = null;
      });
      app.setOfflineBanner(res.fromCache);
    } catch (e) {
      if (!mounted || seq != _loadSeq) return;
      setState(() {
        if (_data == null) _error = e.toString();
        _loading = false;
      });
    }
  }

  void _openDirectorySearch([String? query]) {
    final q = (query ?? _searchCtrl.text).trim();
    final app = context.read<AppState>();
    if (q.isNotEmpty) {
      app.setPendingDirectoryQuery(q);
    }
    ShellScope.maybeOf(context)?.goToTab(1);
  }

  void _openCategory(Map<String, dynamic> c, AppState app) {
    final id = '${c['id'] ?? c['slug'] ?? ''}'.trim();
    if (id.isEmpty) return;
    final name = app.isUrdu ? '${c['name_ur'] ?? c['name']}' : '${c['name_en'] ?? c['name']}';
    Navigator.of(context).push(MaterialPageRoute(
      builder: (_) => CategoryListingScreen(categoryId: id, categoryName: name),
    ));
  }

  Color _colorFor(int index) {
    const palette = [
      AppColors.emerald,
      AppColors.sky,
      AppColors.amber,
      Color(0xFF8B5CF6),
      AppColors.rose,
      Color(0xFF0D9488),
    ];
    return palette[index % palette.length];
  }

  Future<void> _openAllCategoriesSheet(AppState app, bool isDark, List<dynamic> categories) async {
    await showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      backgroundColor: isDark ? AppColors.slate800 : Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(22)),
      ),
      builder: (ctx) => _HomeCategorySearchSheet(
        app: app,
        isDark: isDark,
        categories: categories,
        colorFor: _colorFor,
        onSelect: (c) {
          Navigator.of(ctx).pop();
          _openCategory(c, app);
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final categories = _categories;

    return Scaffold(
      body: RefreshIndicator(
        color: AppColors.emerald,
        onRefresh: _load,
        child: _loading
            ? const Center(child: CircularProgressIndicator(color: AppColors.emerald))
            : _error != null
                ? ListView(children: [
                    const SizedBox(height: 120),
                    Center(child: Text(_error!)),
                    TextButton(onPressed: _load, child: Text(app.t(en: 'Retry', ur: 'دوبارہ کوشش'))),
                  ])
                : CustomScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    slivers: [
                      SliverToBoxAdapter(child: OfflineBanner(visible: _fromCache)),
                      SliverToBoxAdapter(
                        child: _HomeHeader(
                          app: app,
                          isDark: isDark,
                          searchCtrl: _searchCtrl,
                          onSearch: _openDirectorySearch,
                          signatureUr: _signatureUr,
                        ),
                      ),
                      if (categories.isNotEmpty)
                        SliverToBoxAdapter(
                          child: Padding(
                            padding: const EdgeInsets.fromLTRB(16, 4, 16, 8),
                            child: _HomeCategoriesBox(
                              app: app,
                              isDark: isDark,
                              categories: categories,
                              featuredCount: _featuredCount,
                              colorFor: _colorFor,
                              onOpen: (c) => _openCategory(c, app),
                              onViewMore: () => _openAllCategoriesSheet(app, isDark, categories),
                              onBrowseAll: () => ShellScope.maybeOf(context)?.goToTab(1),
                            ),
                          ),
                        ),
                      SliverToBoxAdapter(
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(16, 16, 16, 10),
                          child: Text(
                            app.t(en: 'Latest listings', ur: 'تازہ فہرستیں'),
                            style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 18),
                          ),
                        ),
                      ),
                      SliverPadding(
                        padding: const EdgeInsets.fromLTRB(16, 0, 16, 28),
                        sliver: SliverList.separated(
                          itemCount: ((_data?['recent_businesses'] as List?) ?? []).length,
                          separatorBuilder: (context, index) => const SizedBox(height: 12),
                          itemBuilder: (context, i) {
                            final b = (_data!['recent_businesses'] as List)[i] as Map<String, dynamic>;
                            return BusinessCardTile(
                              item: b,
                              isUrdu: app.isUrdu,
                              compact: true,
                              onTap: () {
                                Navigator.of(context).push(MaterialPageRoute(
                                  builder: (_) => BusinessDetailScreen(idOrSlug: '${b['id'] ?? b['slug'] ?? ''}'),
                                ));
                              },
                            );
                          },
                        ),
                      ),
                    ],
                  ),
      ),
    );
  }
}

class _HomeHeader extends StatelessWidget {
  const _HomeHeader({
    required this.app,
    required this.isDark,
    required this.searchCtrl,
    required this.onSearch,
    required this.signatureUr,
  });

  final AppState app;
  final bool isDark;
  final TextEditingController searchCtrl;
  final ValueChanged<String?> onSearch;
  final String signatureUr;

  @override
  Widget build(BuildContext context) {
    final top = MediaQuery.paddingOf(context).top;
    return Column(
      children: [
        Container(
          width: double.infinity,
          padding: EdgeInsets.fromLTRB(20, top + 14, 20, 22),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: isDark
                  ? const [Color(0xFF064E3B), Color(0xFF0F766E), Color(0xFF134E4A)]
                  : const [Color(0xFF047857), Color(0xFF059669), Color(0xFF0D9488)],
            ),
            borderRadius: const BorderRadius.vertical(bottom: Radius.circular(28)),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Material(
                    color: Colors.white.withValues(alpha: 0.18),
                    borderRadius: BorderRadius.circular(14),
                    child: const DrawerMenuButton(color: Colors.white),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          app.t(en: 'KotSultan.com', ur: 'کوٹ سلطان ڈاٹ کام'),
                          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 20),
                        ),
                        Text(
                          app.t(en: 'Your local community directory', ur: 'آپ کی مقامی کمیونٹی ڈائریکٹری'),
                          style: TextStyle(color: Colors.white.withValues(alpha: 0.85), fontSize: 12),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 28),
              Text(
                app.t(
                  en: 'Find shops, services & people in Kot Sultan',
                  ur: 'کوٹ سلطان میں دکانیں، خدمات اور لوگ تلاش کریں',
                ),
                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 22, height: 1.25),
              ),
              const SizedBox(height: 8),
              Text(
                app.t(
                  en: 'Browse trusted local businesses, emergency contacts, and community personalities — all in one place.',
                  ur: 'مقامی کاروبار، ایمرجنسی رابطے اور کمیونٹی شخصیات سب ایک جگہ۔',
                ),
                style: TextStyle(color: Colors.white.withValues(alpha: 0.9), fontSize: 13, height: 1.4),
              ),
            ],
          ),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 14, 16, 0),
          child: Text(
            signatureUr,
            textAlign: TextAlign.center,
            textDirection: TextDirection.rtl,
            style: const TextStyle(
              color: AppColors.emerald,
              fontWeight: FontWeight.w800,
              fontSize: 18,
              height: 1.45,
            ),
          ),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
          child: Material(
            elevation: 2,
            shadowColor: Colors.black26,
            borderRadius: BorderRadius.circular(16),
            color: isDark ? AppColors.slate800 : Colors.white,
            child: TextField(
              controller: searchCtrl,
              textInputAction: TextInputAction.search,
              onSubmitted: (v) => onSearch(v),
              decoration: InputDecoration(
                hintText: app.t(en: 'Search businesses, phone...', ur: 'کاروبار یا نمبر تلاش کریں...'),
                prefixIcon: const Icon(Icons.search_rounded, color: AppColors.emerald),
                suffixIcon: IconButton(
                  onPressed: () => onSearch(searchCtrl.text),
                  icon: const Icon(Icons.arrow_forward_rounded, color: AppColors.emeraldDark),
                ),
                filled: true,
                fillColor: isDark ? AppColors.slate800 : Colors.white,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: BorderSide.none,
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: BorderSide(color: isDark ? AppColors.slate700 : const Color(0xFFE6EEEA)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: const BorderSide(color: AppColors.emerald, width: 1.4),
                ),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
              ),
            ),
          ),
        ),
      ],
    );
  }
}

class _HomeCategoriesBox extends StatelessWidget {
  const _HomeCategoriesBox({
    required this.app,
    required this.isDark,
    required this.categories,
    required this.featuredCount,
    required this.colorFor,
    required this.onOpen,
    required this.onViewMore,
    required this.onBrowseAll,
  });

  final AppState app;
  final bool isDark;
  final List<dynamic> categories;
  final int featuredCount;
  final Color Function(int) colorFor;
  final ValueChanged<Map<String, dynamic>> onOpen;
  final VoidCallback onViewMore;
  final VoidCallback onBrowseAll;

  @override
  Widget build(BuildContext context) {
    final remaining = categories.length > featuredCount ? categories.length - featuredCount : 0;
    final visible = categories.take(featuredCount).toList();

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(12, 12, 12, 10),
      decoration: BoxDecoration(
        color: isDark ? AppColors.slate800 : Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: isDark ? AppColors.slate700 : const Color(0xFFE6EEEA)),
        boxShadow: isDark
            ? null
            : [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.04),
                  blurRadius: 10,
                  offset: const Offset(0, 3),
                ),
              ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisSize: MainAxisSize.min,
        children: [
          Row(
            children: [
              Container(
                width: 28,
                height: 28,
                decoration: BoxDecoration(
                  color: isDark ? AppColors.emerald.withValues(alpha: 0.2) : AppColors.tealSoft,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Icon(Icons.category_rounded, size: 16, color: AppColors.emeraldDark),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  app.t(en: 'Browse categories', ur: 'زمرے دیکھیں'),
                  style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Material(
            color: isDark ? AppColors.emerald.withValues(alpha: 0.18) : AppColors.tealSoft,
            borderRadius: BorderRadius.circular(12),
            child: InkWell(
              onTap: onBrowseAll,
              borderRadius: BorderRadius.circular(12),
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppColors.emerald.withValues(alpha: 0.35)),
                ),
                child: Row(
                  children: [
                    Icon(
                      Icons.storefront_rounded,
                      size: 18,
                      color: isDark ? AppColors.emeraldLight : AppColors.emeraldDark,
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        app.t(en: 'All directory categories', ur: 'ڈائریکٹری کے تمام زمرے'),
                        style: TextStyle(
                          fontWeight: FontWeight.w700,
                          fontSize: 13,
                          color: isDark ? AppColors.emeraldLight : AppColors.emeraldDark,
                        ),
                      ),
                    ),
                    Icon(
                      Icons.arrow_forward_rounded,
                      size: 16,
                      color: isDark ? AppColors.emeraldLight : AppColors.emeraldDark,
                    ),
                  ],
                ),
              ),
            ),
          ),
          const SizedBox(height: 8),
          GridView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: visible.length,
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 2,
              mainAxisSpacing: 8,
              crossAxisSpacing: 8,
              childAspectRatio: 2.55,
            ),
            itemBuilder: (context, i) {
              final c = Map<String, dynamic>.from(visible[i] as Map);
              final name = app.isUrdu
                  ? '${c['name_ur'] ?? c['name'] ?? ''}'
                  : '${c['name_en'] ?? c['name'] ?? ''}';
              final color = colorFor(i);
              return Material(
                color: isDark ? AppColors.slate700 : AppColors.slate50,
                borderRadius: BorderRadius.circular(12),
                child: InkWell(
                  onTap: () => onOpen(c),
                  borderRadius: BorderRadius.circular(12),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: isDark ? AppColors.slate700 : const Color(0xFFE6EEEA),
                      ),
                    ),
                    child: Row(
                      children: [
                        Container(
                          width: 30,
                          height: 30,
                          decoration: BoxDecoration(
                            color: color.withValues(alpha: isDark ? 0.25 : 0.15),
                            borderRadius: BorderRadius.circular(9),
                          ),
                          child: Icon(Icons.grid_view_rounded, size: 16, color: color),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            name,
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                              fontWeight: FontWeight.w700,
                              fontSize: 12,
                              height: 1.15,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
          if (remaining > 0) ...[
            const SizedBox(height: 8),
            SizedBox(
              width: double.infinity,
              child: OutlinedButton.icon(
                onPressed: onViewMore,
                icon: const Icon(Icons.grid_view_rounded, size: 18),
                label: Text(
                  app.t(
                    en: 'Click to view more ($remaining)',
                    ur: 'مزید دیکھنے کے لیے کلک کریں ($remaining)',
                  ),
                  style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13),
                ),
                style: OutlinedButton.styleFrom(
                  foregroundColor: AppColors.emeraldDark,
                  side: BorderSide(color: isDark ? AppColors.slate700 : const Color(0xFFD1E7DD)),
                  backgroundColor: isDark ? AppColors.emerald.withValues(alpha: 0.12) : AppColors.tealSoft,
                  padding: const EdgeInsets.symmetric(vertical: 10),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }
}

class _HomeCategorySearchSheet extends StatefulWidget {
  const _HomeCategorySearchSheet({
    required this.app,
    required this.isDark,
    required this.categories,
    required this.colorFor,
    required this.onSelect,
  });

  final AppState app;
  final bool isDark;
  final List<dynamic> categories;
  final Color Function(int) colorFor;
  final ValueChanged<Map<String, dynamic>> onSelect;

  @override
  State<_HomeCategorySearchSheet> createState() => _HomeCategorySearchSheetState();
}

class _HomeCategorySearchSheetState extends State<_HomeCategorySearchSheet> {
  final _searchCtrl = TextEditingController();
  String _query = '';

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
  }

  List<Map<String, dynamic>> get _filtered {
    final q = _query.trim().toLowerCase();
    final out = <Map<String, dynamic>>[];
    for (final raw in widget.categories) {
      if (raw is! Map) continue;
      final c = Map<String, dynamic>.from(raw);
      if (q.isEmpty) {
        out.add(c);
        continue;
      }
      final en = '${c['name_en'] ?? ''}'.toLowerCase();
      final ur = '${c['name_ur'] ?? ''}'.toLowerCase();
      final name = '${c['name'] ?? ''}'.toLowerCase();
      final slug = '${c['slug'] ?? ''}'.toLowerCase();
      if (en.contains(q) || ur.contains(q) || name.contains(q) || slug.contains(q)) {
        out.add(c);
      }
    }
    return out;
  }

  @override
  Widget build(BuildContext context) {
    final app = widget.app;
    final isDark = widget.isDark;
    final filtered = _filtered;
    final maxH = MediaQuery.sizeOf(context).height * 0.78;

    return SafeArea(
      child: SizedBox(
        height: maxH,
        child: Column(
          children: [
            const SizedBox(height: 10),
            Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: isDark ? AppColors.slate700 : AppColors.slate200,
                borderRadius: BorderRadius.circular(999),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 14, 8, 8),
              child: Row(
                children: [
                  Expanded(
                    child: Text(
                      app.t(en: 'All categories', ur: 'تمام زمرے'),
                      style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 17),
                    ),
                  ),
                  IconButton(
                    onPressed: () => Navigator.of(context).pop(),
                    icon: const Icon(Icons.close_rounded),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 10),
              child: TextField(
                controller: _searchCtrl,
                autofocus: true,
                textInputAction: TextInputAction.search,
                onChanged: (v) => setState(() => _query = v),
                decoration: InputDecoration(
                  hintText: app.t(en: 'Search categories...', ur: 'زمرے تلاش کریں...'),
                  prefixIcon: const Icon(Icons.search_rounded, color: AppColors.emerald),
                  suffixIcon: _query.isEmpty
                      ? null
                      : IconButton(
                          onPressed: () {
                            _searchCtrl.clear();
                            setState(() => _query = '');
                          },
                          icon: const Icon(Icons.close_rounded),
                        ),
                  filled: true,
                  fillColor: isDark ? AppColors.slate700 : AppColors.slate50,
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: BorderSide.none,
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: BorderSide(color: isDark ? AppColors.slate700 : const Color(0xFFE6EEEA)),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: AppColors.emerald, width: 1.4),
                  ),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 8),
              child: Align(
                alignment: AlignmentDirectional.centerStart,
                child: Text(
                  app.t(
                    en: '${filtered.length} categories',
                    ur: '${filtered.length} زمرے',
                  ),
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: isDark ? Colors.white54 : AppColors.slate500,
                  ),
                ),
              ),
            ),
            const Divider(height: 1),
            Expanded(
              child: filtered.isEmpty
                  ? Center(
                      child: Text(
                        app.t(en: 'No categories found', ur: 'کوئی زمرہ نہیں ملا'),
                        style: TextStyle(color: isDark ? Colors.white54 : AppColors.slate500),
                      ),
                    )
                  : ListView.separated(
                      padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
                      itemCount: filtered.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 8),
                      itemBuilder: (context, i) {
                        final c = filtered[i];
                        final name = app.isUrdu
                            ? '${c['name_ur'] ?? c['name'] ?? ''}'
                            : '${c['name_en'] ?? c['name'] ?? ''}';
                        final color = widget.colorFor(i);
                        return Material(
                          color: isDark ? AppColors.slate700 : AppColors.slate50,
                          borderRadius: BorderRadius.circular(12),
                          child: InkWell(
                            borderRadius: BorderRadius.circular(12),
                            onTap: () => widget.onSelect(c),
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                border: Border.all(
                                  color: isDark ? AppColors.slate700 : const Color(0xFFE6EEEA),
                                ),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    width: 34,
                                    height: 34,
                                    decoration: BoxDecoration(
                                      color: color.withValues(alpha: isDark ? 0.25 : 0.14),
                                      borderRadius: BorderRadius.circular(10),
                                    ),
                                    child: Icon(Icons.grid_view_rounded, size: 18, color: color),
                                  ),
                                  const SizedBox(width: 10),
                                  Expanded(
                                    child: Text(
                                      name,
                                      style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14),
                                    ),
                                  ),
                                  Icon(
                                    Icons.chevron_right_rounded,
                                    color: isDark ? Colors.white54 : AppColors.slate500,
                                  ),
                                ],
                              ),
                            ),
                          ),
                        );
                      },
                    ),
            ),
          ],
        ),
      ),
    );
  }
}
