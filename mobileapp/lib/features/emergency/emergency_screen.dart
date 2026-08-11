import 'dart:async';

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/offline_banner.dart';
import '../shell/app_drawer.dart';

class EmergencyScreen extends StatefulWidget {
  const EmergencyScreen({super.key});

  @override
  State<EmergencyScreen> createState() => _EmergencyScreenState();
}

class _EmergencyScreenState extends State<EmergencyScreen> {
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
      final res = await context.read<AppState>().catalog.getEmergency(
        q: _searchCtrl.text.trim().isEmpty ? null : _searchCtrl.text.trim(),
        category: _category,
      );
      final data = res.data;

      setState(() {
        _items = List<dynamic>.from((data['items'] as List?) ?? const []);
        final cats = data['categories'];
        if (cats is List && cats.isNotEmpty) {
          _categories = List<dynamic>.from(cats);
        }
        _fromCache = res.fromCache;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _items = [];
      });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _search(String _) {
    _debounce?.cancel();
    _debounce = Timer(const Duration(milliseconds: 400), _load);
  }

  Future<void> _call(String phone) async {
    final cleaned = phone.replaceAll(RegExp(r'[^\d+]'), '');
    if (cleaned.isEmpty) return;
    await launchUrl(Uri.parse('tel:$cleaned'));
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();

    return Scaffold(
      appBar: AppBar(
        leading: const DrawerMenuButton(),
        title: Text(app.t(en: 'Emergency', ur: 'ایمرجنسی')),
      ),
      body: Column(
        children: [
          OfflineBanner(visible: _fromCache),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
            child: TextField(
              controller: _searchCtrl,
              onChanged: _search,
              onSubmitted: (_) => _load(),
              textInputAction: TextInputAction.search,
              decoration: InputDecoration(
                hintText: app.t(en: 'Search contacts...', ur: 'رابطے تلاش کریں...'),
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _searchCtrl.text.isEmpty
                    ? null
                    : IconButton(
                        icon: const Icon(Icons.clear),
                        onPressed: () {
                          _searchCtrl.clear();
                          _load();
                        },
                      ),
              ),
            ),
          ),
          if (_categories.isNotEmpty)
            SizedBox(
              height: 42,
              child: ListView(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 16),
                children: [
                  _chip(
                    label: app.t(en: 'All', ur: 'سب'),
                    selected: _category == null,
                    onTap: () {
                      setState(() => _category = null);
                      _load();
                    },
                  ),
                  ..._categories.map((raw) {
                    final c = Map<String, dynamic>.from(raw as Map);
                    final key = '${c['category_en'] ?? c['category'] ?? ''}'.trim();
                    final label = app.isUrdu
                        ? '${c['category_ur'] ?? c['category'] ?? key}'
                        : '${c['category_en'] ?? c['category'] ?? key}';
                    if (key.isEmpty) return const SizedBox.shrink();
                    return _chip(
                      label: label,
                      selected: _category == key,
                      onTap: () {
                        setState(() => _category = key);
                        _load();
                      },
                    );
                  }),
                ],
              ),
            ),
          const SizedBox(height: 8),
          Expanded(
            child: _buildList(app),
          ),
        ],
      ),
    );
  }

  Widget _chip({
    required String label,
    required bool selected,
    required VoidCallback onTap,
  }) {
    return Padding(
      padding: const EdgeInsetsDirectional.only(end: 8),
      child: ChoiceChip(
        label: Text(label),
        selected: selected,
        onSelected: (_) => onTap(),
      ),
    );
  }

  Widget _buildList(AppState app) {
    if (_loading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(_error!, textAlign: TextAlign.center),
              const SizedBox(height: 12),
              FilledButton(onPressed: _load, child: Text(app.t(en: 'Retry', ur: 'دوبارہ کوشش'))),
            ],
          ),
        ),
      );
    }

    if (_items.isEmpty) {
      return Center(child: Text(app.t(en: 'No contacts found', ur: 'کوئی رابطہ نہیں ملا')));
    }

    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.builder(
        padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
        itemCount: _items.length,
        itemBuilder: (context, index) {
          final c = Map<String, dynamic>.from(_items[index] as Map);
          final name = app.isUrdu
              ? '${c['department_ur'] ?? c['department'] ?? c['department_en'] ?? ''}'.trim()
              : '${c['department_en'] ?? c['department'] ?? ''}'.trim();
          final category = app.isUrdu
              ? '${c['category_ur'] ?? c['category'] ?? c['category_en'] ?? ''}'.trim()
              : '${c['category_en'] ?? c['category'] ?? ''}'.trim();
          final phone = '${c['phone_primary'] ?? ''}'.trim();

          return Card(
            margin: const EdgeInsets.only(bottom: 10),
            child: ListTile(
              contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
              leading: CircleAvatar(
                backgroundColor: AppColors.tealSoft,
                child: const Icon(Icons.phone_in_talk, color: AppColors.emeraldDark),
              ),
              title: Text(
                name.isEmpty ? app.t(en: 'Emergency contact', ur: 'ایمرجنسی رابطہ') : name,
                style: const TextStyle(fontWeight: FontWeight.w700),
              ),
              subtitle: Text(
                [
                  if (category.isNotEmpty) category,
                  if (phone.isNotEmpty) phone,
                ].join('\n'),
                textDirection: phone.isNotEmpty ? TextDirection.ltr : null,
              ),
              isThreeLine: category.isNotEmpty && phone.isNotEmpty,
              trailing: phone.isEmpty
                  ? null
                  : IconButton(
                      icon: const Icon(Icons.call, color: AppColors.emerald),
                      onPressed: () => _call(phone),
                    ),
            ),
          );
        },
      ),
    );
  }
}
