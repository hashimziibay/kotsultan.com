import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/api/api_client.dart';
import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import 'my_business_form_screen.dart';

class MyBusinessListScreen extends StatefulWidget {
  const MyBusinessListScreen({super.key});

  @override
  State<MyBusinessListScreen> createState() => _MyBusinessListScreenState();
}

class _MyBusinessListScreenState extends State<MyBusinessListScreen> {
  bool _loading = true;
  String? _error;
  List<Map<String, dynamic>> _items = [];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _load());
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final api = context.read<AppState>().api;
      final res = await api.get('my-businesses');
      final data = res['data'] as Map<String, dynamic>? ?? {};
      final raw = (data['items'] as List?) ?? const [];
      setState(() {
        _items = raw.map((e) => Map<String, dynamic>.from(e as Map)).toList();
        _loading = false;
      });
    } catch (e) {
      setState(() {
        _error = e is ApiException ? e.message : e.toString();
        _loading = false;
      });
    }
  }

  Future<void> _openForm({Map<String, dynamic>? business}) async {
    final changed = await Navigator.of(context).push<bool>(
      MaterialPageRoute(
        builder: (_) => MyBusinessFormScreen(business: business),
      ),
    );
    if (changed == true && mounted) {
      await _load();
    }
  }

  Color _statusColor(String status) {
    switch (status) {
      case 'active':
        return AppColors.emerald;
      case 'pending':
        return const Color(0xFFD97706);
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    return Scaffold(
      appBar: AppBar(
        title: Text(app.t(en: 'My Business', ur: 'میرا کاروبار')),
      ),
      floatingActionButton: _items.isEmpty
          ? FloatingActionButton.extended(
              onPressed: () => _openForm(),
              icon: const Icon(Icons.add),
              label: Text(app.t(en: 'Add listing', ur: 'نیا کاروبار')),
            )
          : null,
      body: RefreshIndicator(
        onRefresh: _load,
        child: _loading
            ? ListView(
                children: const [
                  SizedBox(height: 120),
                  Center(child: CircularProgressIndicator()),
                ],
              )
            : _error != null
                ? ListView(
                    padding: const EdgeInsets.all(24),
                    children: [
                      Text(_error!, textAlign: TextAlign.center),
                      const SizedBox(height: 12),
                      Center(
                        child: FilledButton(
                          onPressed: _load,
                          child: Text(app.t(en: 'Retry', ur: 'دوبارہ کوشش')),
                        ),
                      ),
                    ],
                  )
                : _items.isEmpty
                    ? ListView(
                        padding: const EdgeInsets.all(24),
                        children: [
                          const SizedBox(height: 48),
                          Icon(Icons.storefront_outlined, size: 56, color: Colors.grey.shade400),
                          const SizedBox(height: 12),
                          Text(
                            app.t(
                              en: 'No business listing yet. Add your shop details for admin review. Only one business per mobile number.',
                              ur: 'ابھی کوئی کاروبار نہیں۔ تفصیلات شامل کریں۔ ایک موبائل نمبر پر صرف ایک کاروبار۔',
                            ),
                            textAlign: TextAlign.center,
                            style: TextStyle(color: Colors.grey.shade600),
                          ),
                        ],
                      )
                    : ListView.separated(
                        padding: const EdgeInsets.fromLTRB(16, 12, 16, 100),
                        itemCount: _items.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 10),
                        itemBuilder: (context, i) {
                          final b = _items[i];
                          final status = '${b['status'] ?? 'pending'}';
                          final name = app.isUrdu
                              ? ('${b['name_ur'] ?? ''}'.trim().isNotEmpty
                                  ? '${b['name_ur']}'
                                  : '${b['name_en'] ?? ''}')
                              : ('${b['name_en'] ?? ''}'.trim().isNotEmpty
                                  ? '${b['name_en']}'
                                  : '${b['name_ur'] ?? ''}');
                          return Material(
                            color: Theme.of(context).cardColor,
                            borderRadius: BorderRadius.circular(16),
                            child: InkWell(
                              borderRadius: BorderRadius.circular(16),
                              onTap: () => _openForm(business: b),
                              child: Padding(
                                padding: const EdgeInsets.all(14),
                                child: Row(
                                  children: [
                                    Container(
                                      width: 44,
                                      height: 44,
                                      decoration: BoxDecoration(
                                        color: AppColors.emerald.withValues(alpha: 0.12),
                                        borderRadius: BorderRadius.circular(12),
                                      ),
                                      child: const Icon(Icons.storefront_rounded, color: AppColors.emerald),
                                    ),
                                    const SizedBox(width: 12),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            name,
                                            style: const TextStyle(fontWeight: FontWeight.w700),
                                          ),
                                          const SizedBox(height: 4),
                                          Text(
                                            '${b['phone'] ?? ''}'.trim().isEmpty
                                                ? app.t(en: 'No phone', ur: 'فون نہیں')
                                                : '${b['phone']}',
                                            style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                                          ),
                                        ],
                                      ),
                                    ),
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                      decoration: BoxDecoration(
                                        color: _statusColor(status).withValues(alpha: 0.15),
                                        borderRadius: BorderRadius.circular(999),
                                      ),
                                      child: Text(
                                        status.toUpperCase(),
                                        style: TextStyle(
                                          fontSize: 10,
                                          fontWeight: FontWeight.w800,
                                          color: _statusColor(status),
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
      ),
    );
  }
}
