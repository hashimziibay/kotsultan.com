import 'dart:async';

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/app_network_image.dart';
import '../shell/app_drawer.dart';
import 'wall_detail_screen.dart';

class WallScreen extends StatefulWidget {
  const WallScreen({super.key});

  @override
  State<WallScreen> createState() => _WallScreenState();
}

class _WallScreenState extends State<WallScreen> {
  final _searchCtrl = TextEditingController();
  Timer? _debounce;
  bool _loading = true;
  String? _error;
  List<dynamic> _items = [];

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

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final query = <String, String>{
        'per_page': '30',
        if (_searchCtrl.text.trim().isNotEmpty) 'q': _searchCtrl.text.trim(),
      };
      final res = await context.read<AppState>().api.get('wall', query: query);
      final data = res['data'] as Map<String, dynamic>;
      setState(() => _items = (data['items'] as List?) ?? []);
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
        title: Text(app.t(en: 'Wall of Kot Sultan', ur: 'وال آف کوٹ سلطان')),
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
          Expanded(
            child: RefreshIndicator(
              color: AppColors.emerald,
              onRefresh: _load,
              child: _loading && _items.isEmpty
                  ? const Center(child: CircularProgressIndicator(color: AppColors.emerald))
                  : _error != null
                      ? ListView(children: [Center(child: Text(_error!))])
                      : GridView.builder(
                          padding: const EdgeInsets.fromLTRB(16, 4, 16, 24),
                          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 2,
                            mainAxisSpacing: 12,
                            crossAxisSpacing: 12,
                            childAspectRatio: 0.72,
                          ),
                          itemCount: _items.length,
                          itemBuilder: (context, i) {
                            final e = _items[i] as Map<String, dynamic>;
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
                                      child: AppNetworkImage(
                                        url: '${e['photo'] ?? ''}',
                                        placeholderIcon: Icons.person_rounded,
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
}
