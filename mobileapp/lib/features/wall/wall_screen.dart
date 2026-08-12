import 'dart:async';

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/app_network_image.dart';
import '../../core/widgets/offline_banner.dart';
import '../shell/app_drawer.dart';
import 'wall_detail_screen.dart';

class WallScreen extends StatefulWidget {
  const WallScreen({super.key});

  @override
  State<WallScreen> createState() => _WallScreenState();
}

class _WallScreenState extends State<WallScreen> {
  static const _featuredCount = 4;

  final _searchCtrl = TextEditingController();
  Timer? _debounce;
  bool _loading = true;
  bool _fromCache = false;
  String? _error;
  List<dynamic> _items = [];
  List<dynamic> _categories = [];
  String? _category;
  int _seenEpoch = -1;

  @override
  void initState() {
    super.initState();
    _searchCtrl.addListener(_onSearchChanged);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final app = context.read<AppState>();
      app.addListener(_onAppChanged);
      _seenEpoch = app.catalogEpoch;
      _load();
    });
  }

  @override
  void dispose() {
    _debounce?.cancel();
    try {
      context.read<AppState>().removeListener(_onAppChanged);
    } catch (_) {}
    _searchCtrl.removeListener(_onSearchChanged);
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

  void _onSearchChanged() {
    setState(() {});
    _debounce?.cancel();
    _debounce = Timer(const Duration(milliseconds: 400), () {
      if (mounted) _load();
    });
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final res = await context.read<AppState>().catalog.getWall(
        q: _searchCtrl.text.trim().isEmpty ? null : _searchCtrl.text.trim(),
        category: _category,
        perPage: 30,
      );
      final data = res.data;
      setState(() {
        _items = (data['items'] as List?) ?? [];
        final cats = data['categories'];
        if (cats is List && cats.isNotEmpty) {
          _categories = List<dynamic>.from(cats);
        }
        _fromCache = res.fromCache;
      });
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _selectCategory(String? key) {
    setState(() => _category = key);
    _load();
  }

  IconData _iconFor(String? raw) {
    final key = (raw ?? '').toLowerCase().trim();
    switch (key) {
      case 'graduation-cap':
      case 'education':
        return Icons.school_rounded;
      case 'landmark':
      case 'politics':
        return Icons.account_balance_rounded;
      case 'heart-handshake':
      case 'social-workers':
        return Icons.volunteer_activism_rounded;
      case 'book-open':
      case 'religious-scholars':
        return Icons.menu_book_rounded;
      case 'stethoscope':
      case 'stethoscopes':
      case 'doctors':
        return Icons.medical_services_rounded;
      case 'briefcase':
      case 'business-personalities':
        return Icons.work_rounded;
      case 'trophy':
      case 'sports':
        return Icons.emoji_events_rounded;
      case 'palette':
      case 'artists':
        return Icons.palette_rounded;
      case 'pen-tool':
      case 'writers-poets':
        return Icons.edit_note_rounded;
      default:
        return Icons.person_rounded;
    }
  }

  Color _colorFor(String? raw, int index) {
    final key = (raw ?? '').toLowerCase().trim();
    switch (key) {
      case 'emerald':
        return AppColors.emerald;
      case 'blue':
      case 'sky':
        return AppColors.sky;
      case 'rose':
        return AppColors.rose;
      case 'amber':
      case 'orange':
        return AppColors.amber;
      case 'teal':
        return const Color(0xFF0D9488);
      case 'indigo':
        return const Color(0xFF6366F1);
      default:
        const palette = [
          AppColors.emerald,
          AppColors.sky,
          AppColors.amber,
          AppColors.rose,
          Color(0xFF6366F1),
          Color(0xFF0D9488),
        ];
        return palette[index % palette.length];
    }
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        leading: const DrawerMenuButton(),
        title: Text(app.t(en: 'Wall of Kot Sultan', ur: 'وال آف کوٹ سلطان')),
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
                hintText: app.t(en: 'Search personalities', ur: 'شخصیات تلاش کریں'),
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
          if (_categories.isNotEmpty)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 8),
              child: _CategoriesBox(
                app: app,
                isDark: isDark,
                categories: _categories,
                selectedKey: _category,
                featuredCount: _featuredCount,
                iconFor: _iconFor,
                colorFor: _colorFor,
                onSelect: _selectCategory,
                onViewMore: () => _openAllCategoriesSheet(app, isDark),
              ),
            ),
          Expanded(
            child: RefreshIndicator(
              color: AppColors.emerald,
              onRefresh: _load,
              child: _loading && _items.isEmpty
                  ? const Center(child: CircularProgressIndicator(color: AppColors.emerald))
                  : _error != null
                      ? ListView(children: [Center(child: Text(_error!))])
                      : _items.isEmpty
                          ? ListView(
                              children: [
                                const SizedBox(height: 80),
                                Center(
                                  child: Text(
                                    app.t(en: 'No personalities found', ur: 'کوئی شخصیت نہیں ملی'),
                                  ),
                                ),
                              ],
                            )
                          : GridView.builder(
                              padding: const EdgeInsets.fromLTRB(16, 4, 16, 24),
                              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                                crossAxisCount: 2,
                                mainAxisSpacing: 12,
                                crossAxisSpacing: 12,
                                childAspectRatio: 0.68,
                              ),
                              itemCount: _items.length,
                              itemBuilder: (context, i) {
                                final e = _items[i] as Map<String, dynamic>;
                                final category = '${e['category'] ?? ''}'.trim();
                                return Card(
                                  clipBehavior: Clip.antiAlias,
                                  child: InkWell(
                                    onTap: () {
                                      Navigator.of(context).push(MaterialPageRoute(
                                        builder: (_) => WallDetailScreen(idOrSlug: '${e['slug'] ?? e['id']}'),
                                      ));
                                    },
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.stretch,
                                      children: [
                                        Expanded(
                                          child: ColoredBox(
                                            color: isDark ? const Color(0xFF1E293B) : const Color(0xFFF1F5F9),
                                            child: AppNetworkImage(
                                              url: '${e['photo'] ?? ''}',
                                              fit: BoxFit.contain,
                                              placeholderIcon: Icons.person_rounded,
                                            ),
                                          ),
                                        ),
                                        Padding(
                                          padding: const EdgeInsets.all(10),
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                '${e['name'] ?? ''}',
                                                maxLines: 2,
                                                overflow: TextOverflow.ellipsis,
                                                style: const TextStyle(fontWeight: FontWeight.w800),
                                              ),
                                              const SizedBox(height: 2),
                                              Text(
                                                '${e['profession'] ?? ''}',
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                                style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                                              ),
                                              if (category.isNotEmpty) ...[
                                                const SizedBox(height: 4),
                                                Text(
                                                  category,
                                                  maxLines: 1,
                                                  overflow: TextOverflow.ellipsis,
                                                  style: const TextStyle(
                                                    fontSize: 11,
                                                    fontWeight: FontWeight.w700,
                                                    color: AppColors.emeraldDark,
                                                  ),
                                                ),
                                              ],
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
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

  Future<void> _openAllCategoriesSheet(AppState app, bool isDark) async {
    await showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      backgroundColor: isDark ? AppColors.slate800 : Colors.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(22)),
      ),
      builder: (ctx) => _WallCategorySearchSheet(
        app: app,
        isDark: isDark,
        categories: _categories,
        selectedKey: _category,
        iconFor: _iconFor,
        colorFor: _colorFor,
        onSelectAll: () {
          Navigator.of(ctx).pop();
          _selectCategory(null);
        },
        onSelect: (key) {
          Navigator.of(ctx).pop();
          _selectCategory(key);
        },
      ),
    );
  }
}

class _CategoriesBox extends StatelessWidget {
  const _CategoriesBox({
    required this.app,
    required this.isDark,
    required this.categories,
    required this.selectedKey,
    required this.featuredCount,
    required this.iconFor,
    required this.colorFor,
    required this.onSelect,
    required this.onViewMore,
  });

  final AppState app;
  final bool isDark;
  final List<dynamic> categories;
  final String? selectedKey;
  final int featuredCount;
  final IconData Function(String?) iconFor;
  final Color Function(String?, int) colorFor;
  final ValueChanged<String?> onSelect;
  final VoidCallback onViewMore;

  @override
  Widget build(BuildContext context) {
    final remaining = categories.length > featuredCount ? categories.length - featuredCount : 0;
    final visible = categories.take(featuredCount).toList();
    final selectedInHidden = selectedKey != null &&
        !visible.any((raw) {
          final c = Map<String, dynamic>.from(raw as Map);
          final id = '${c['id']}';
          final slug = '${c['slug'] ?? ''}'.trim();
          final key = slug.isNotEmpty ? slug : id;
          return selectedKey == key || selectedKey == id;
        });

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
                  app.t(en: 'Browse by category', ur: 'زمرے کے لحاظ سے دیکھیں'),
                  style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 14),
                ),
              ),
              if (selectedKey != null)
                TextButton(
                  onPressed: () => onSelect(null),
                  style: TextButton.styleFrom(
                    foregroundColor: AppColors.emeraldDark,
                    padding: const EdgeInsets.symmetric(horizontal: 8),
                    minimumSize: Size.zero,
                    tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                  ),
                  child: Text(
                    app.t(en: 'Clear', ur: 'صاف کریں'),
                    style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                  ),
                ),
            ],
          ),
          const SizedBox(height: 10),
          _AllTile(
            app: app,
            isDark: isDark,
            selected: selectedKey == null,
            onTap: () => onSelect(null),
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
              final id = '${c['id']}';
              final slug = '${c['slug'] ?? ''}'.trim();
              final key = slug.isNotEmpty ? slug : id;
              final label = app.isUrdu
                  ? '${c['name_ur'] ?? c['name_en'] ?? c['name'] ?? ''}'
                  : '${c['name_en'] ?? c['name_ur'] ?? c['name'] ?? ''}';
              final color = colorFor('${c['color'] ?? ''}', i);
              final selected = selectedKey == key || selectedKey == id;
              return _CategoryTile(
                label: label,
                icon: iconFor('${c['icon'] ?? c['slug'] ?? ''}'),
                color: color,
                selected: selected,
                isDark: isDark,
                onTap: () => onSelect(key),
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
          if (selectedInHidden)
            Padding(
              padding: const EdgeInsets.only(top: 6),
              child: Text(
                app.t(
                  en: 'Selected category is in “view more”.',
                  ur: 'منتخب زمرہ ”مزید دیکھیں“ میں ہے۔',
                ),
                style: TextStyle(fontSize: 11, color: Colors.grey.shade600),
              ),
            ),
        ],
      ),
    );
  }
}

class _AllTile extends StatelessWidget {
  const _AllTile({
    required this.app,
    required this.isDark,
    required this.selected,
    required this.onTap,
  });

  final AppState app;
  final bool isDark;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: selected
          ? (isDark ? AppColors.emerald.withValues(alpha: 0.22) : AppColors.tealSoft)
          : (isDark ? AppColors.slate700 : AppColors.slate50),
      borderRadius: BorderRadius.circular(12),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          width: double.infinity,
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: selected
                  ? AppColors.emerald.withValues(alpha: 0.45)
                  : (isDark ? AppColors.slate700 : const Color(0xFFE6EEEA)),
            ),
          ),
          child: Row(
            children: [
              Icon(
                Icons.people_alt_rounded,
                size: 18,
                color: selected ? AppColors.emeraldDark : AppColors.slate500,
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  app.t(en: 'All personalities', ur: 'تمام شخصیات'),
                  style: TextStyle(
                    fontWeight: FontWeight.w700,
                    fontSize: 13,
                    color: selected
                        ? (isDark ? AppColors.emeraldLight : AppColors.emeraldDark)
                        : null,
                  ),
                ),
              ),
              if (selected)
                const Icon(Icons.check_circle_rounded, size: 16, color: AppColors.emerald),
            ],
          ),
        ),
      ),
    );
  }
}

class _CategoryTile extends StatelessWidget {
  const _CategoryTile({
    required this.label,
    required this.icon,
    required this.color,
    required this.selected,
    required this.isDark,
    required this.onTap,
  });

  final String label;
  final IconData icon;
  final Color color;
  final bool selected;
  final bool isDark;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: selected
          ? color.withValues(alpha: isDark ? 0.28 : 0.14)
          : (isDark ? AppColors.slate700 : AppColors.slate50),
      borderRadius: BorderRadius.circular(12),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: selected
                  ? color.withValues(alpha: 0.55)
                  : (isDark ? AppColors.slate700 : const Color(0xFFE6EEEA)),
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
                child: Icon(icon, size: 16, color: color),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  label,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    fontWeight: FontWeight.w700,
                    fontSize: 12,
                    height: 1.15,
                    color: selected ? color : null,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _WallCategorySearchSheet extends StatefulWidget {
  const _WallCategorySearchSheet({
    required this.app,
    required this.isDark,
    required this.categories,
    required this.selectedKey,
    required this.iconFor,
    required this.colorFor,
    required this.onSelectAll,
    required this.onSelect,
  });

  final AppState app;
  final bool isDark;
  final List<dynamic> categories;
  final String? selectedKey;
  final IconData Function(String?) iconFor;
  final Color Function(String?, int) colorFor;
  final VoidCallback onSelectAll;
  final ValueChanged<String> onSelect;

  @override
  State<_WallCategorySearchSheet> createState() => _WallCategorySearchSheetState();
}

class _WallCategorySearchSheetState extends State<_WallCategorySearchSheet> {
  final _searchCtrl = TextEditingController();
  String _query = '';

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
  }

  List<MapEntry<int, Map<String, dynamic>>> get _filtered {
    final q = _query.trim().toLowerCase();
    final out = <MapEntry<int, Map<String, dynamic>>>[];
    for (var i = 0; i < widget.categories.length; i++) {
      final raw = widget.categories[i];
      if (raw is! Map) continue;
      final c = Map<String, dynamic>.from(raw);
      if (q.isEmpty) {
        out.add(MapEntry(i, c));
        continue;
      }
      final en = '${c['name_en'] ?? ''}'.toLowerCase();
      final ur = '${c['name_ur'] ?? ''}'.toLowerCase();
      final name = '${c['name'] ?? ''}'.toLowerCase();
      final slug = '${c['slug'] ?? ''}'.toLowerCase();
      if (en.contains(q) || ur.contains(q) || name.contains(q) || slug.contains(q)) {
        out.add(MapEntry(i, c));
      }
    }
    return out;
  }

  @override
  Widget build(BuildContext context) {
    final app = widget.app;
    final isDark = widget.isDark;
    final filtered = _filtered;
    final showAllTile = _query.trim().isEmpty;
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
              child: (!showAllTile && filtered.isEmpty)
                  ? Center(
                      child: Text(
                        app.t(en: 'No categories found', ur: 'کوئی زمرہ نہیں ملا'),
                        style: TextStyle(color: isDark ? Colors.white54 : AppColors.slate500),
                      ),
                    )
                  : ListView.separated(
                      padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
                      itemCount: filtered.length + (showAllTile ? 1 : 0),
                      separatorBuilder: (_, __) => const SizedBox(height: 8),
                      itemBuilder: (context, i) {
                        if (showAllTile && i == 0) {
                          return _AllTile(
                            app: app,
                            isDark: isDark,
                            selected: widget.selectedKey == null,
                            onTap: widget.onSelectAll,
                          );
                        }
                        final entry = filtered[i - (showAllTile ? 1 : 0)];
                        final c = entry.value;
                        final id = '${c['id']}';
                        final slug = '${c['slug'] ?? ''}'.trim();
                        final key = slug.isNotEmpty ? slug : id;
                        final label = app.isUrdu
                            ? '${c['name_ur'] ?? c['name_en'] ?? c['name'] ?? ''}'
                            : '${c['name_en'] ?? c['name_ur'] ?? c['name'] ?? ''}';
                        final color = widget.colorFor('${c['color'] ?? ''}', entry.key);
                        final selected = widget.selectedKey == key || widget.selectedKey == id;
                        return _CategoryTile(
                          label: label,
                          icon: widget.iconFor('${c['icon'] ?? c['slug'] ?? ''}'),
                          color: color,
                          selected: selected,
                          isDark: isDark,
                          onTap: () => widget.onSelect(key),
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
