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
  final _searchCtrl = TextEditingController();
  bool _loading = true;
  bool _fromCache = false;
  String? _error;
  Map<String, dynamic>? _data;
  int _seenEpoch = -1;

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
      _load();
    }
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final app = context.read<AppState>();
      // ignore: unawaited_futures
      app.syncPendingUser();
      final res = await app.catalog.getHome();
      setState(() {
        _data = res.data;
        _fromCache = res.fromCache;
      });
      app.setOfflineBanner(res.fromCache);
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      if (mounted) setState(() => _loading = false);
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

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final isDark = Theme.of(context).brightness == Brightness.dark;

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
                        ),
                      ),
                      SliverToBoxAdapter(
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(16, 8, 16, 8),
                          child: Text(
                            app.t(en: 'Browse categories', ur: 'زمرے دیکھیں'),
                            style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 18),
                          ),
                        ),
                      ),
                      SliverToBoxAdapter(
                        child: SizedBox(
                          height: 108,
                          child: ListView.separated(
                            padding: const EdgeInsets.symmetric(horizontal: 16),
                            scrollDirection: Axis.horizontal,
                            itemCount: ((_data?['popular_categories'] as List?) ?? []).length,
                            separatorBuilder: (context, index) => const SizedBox(width: 10),
                            itemBuilder: (context, i) {
                              final c = (_data!['popular_categories'] as List)[i] as Map<String, dynamic>;
                              final name = app.isUrdu ? '${c['name_ur'] ?? c['name']}' : '${c['name_en'] ?? c['name']}';
                              final colors = [
                                (AppColors.emerald, AppColors.tealSoft),
                                (AppColors.sky, const Color(0xFFE0F2FE)),
                                (AppColors.amber, const Color(0xFFFEF3C7)),
                                (const Color(0xFF8B5CF6), const Color(0xFFEDE9FE)),
                              ];
                              final pair = colors[i % colors.length];
                              return Material(
                                color: isDark ? AppColors.slate800 : Colors.white,
                                borderRadius: BorderRadius.circular(18),
                                child: InkWell(
                                  borderRadius: BorderRadius.circular(18),
                                  onTap: () {
                                    final id = '${c['id'] ?? c['slug'] ?? ''}';
                                    if (id.isEmpty) return;
                                    Navigator.of(context).push(MaterialPageRoute(
                                      builder: (_) => CategoryListingScreen(
                                        categoryId: id,
                                        categoryName: name,
                                      ),
                                    ));
                                  },
                                  child: Container(
                                    width: 132,
                                    padding: const EdgeInsets.all(14),
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(18),
                                      border: Border.all(
                                        color: isDark ? AppColors.slate700 : const Color(0xFFE6EEEA),
                                      ),
                                    ),
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Container(
                                          width: 36,
                                          height: 36,
                                          decoration: BoxDecoration(
                                            color: isDark ? pair.$1.withValues(alpha: 0.2) : pair.$2,
                                            borderRadius: BorderRadius.circular(12),
                                          ),
                                          child: Icon(Icons.grid_view_rounded, color: pair.$1, size: 18),
                                        ),
                                        const Spacer(),
                                        Text(
                                          name,
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                          style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13, height: 1.2),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              );
                            },
                          ),
                        ),
                      ),
                      SliverToBoxAdapter(
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(16, 24, 16, 10),
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
  });

  final AppState app;
  final bool isDark;
  final TextEditingController searchCtrl;
  final ValueChanged<String?> onSearch;

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
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
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
