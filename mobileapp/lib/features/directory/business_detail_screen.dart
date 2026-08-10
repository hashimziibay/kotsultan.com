import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/app_network_image.dart';

class BusinessDetailScreen extends StatefulWidget {
  const BusinessDetailScreen({super.key, required this.idOrSlug});
  final String idOrSlug;

  @override
  State<BusinessDetailScreen> createState() => _BusinessDetailScreenState();
}

class _BusinessDetailScreenState extends State<BusinessDetailScreen> {
  bool _loading = true;
  String? _error;
  Map<String, dynamic>? _biz;

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
      final res = await context.read<AppState>().api.get('businesses/${widget.idOrSlug}');
      setState(() => _biz = res['data'] as Map<String, dynamic>);
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _launch(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    return Scaffold(
      appBar: AppBar(title: Text(app.t(en: 'Business', ur: 'کاروبار'))),
      body: _loading
          ? const Center(child: CircularProgressIndicator(color: AppColors.emerald))
          : _error != null
              ? Center(child: Text(_error!))
              : ListView(
                  children: [
                    AspectRatio(
                      aspectRatio: 16 / 10,
                      child: AppNetworkImage(url: '${_biz?['image'] ?? ''}'),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          if ('${_biz?['category'] ?? ''}'.isNotEmpty)
                            Chip(
                              backgroundColor: AppColors.tealSoft,
                              label: Text(
                                '${_biz!['category']}',
                                style: const TextStyle(color: AppColors.emeraldDark, fontWeight: FontWeight.w700),
                              ),
                            ),
                          Text(
                            '${_biz?['name'] ?? ''}',
                            style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w800),
                          ),
                          if ('${_biz?['owner_name'] ?? ''}'.isNotEmpty) ...[
                            const SizedBox(height: 6),
                            Text('${app.t(en: 'Owner', ur: 'مالک')}: ${_biz!['owner_name']}'),
                          ],
                          if ('${_biz?['address'] ?? ''}'.isNotEmpty) ...[
                            const SizedBox(height: 10),
                            Row(children: [
                              const Icon(Icons.place_rounded, color: AppColors.emerald, size: 18),
                              const SizedBox(width: 6),
                              Expanded(child: Text('${_biz!['address']}')),
                            ]),
                          ],
                          if ('${_biz?['description'] ?? ''}'.isNotEmpty) ...[
                            const SizedBox(height: 14),
                            Text('${_biz!['description']}'),
                          ],
                          const SizedBox(height: 20),
                          if ('${_biz?['phone'] ?? ''}'.trim().isNotEmpty)
                            FilledButton.icon(
                              onPressed: () => _launch('tel:${_biz!['phone']}'),
                              icon: const Icon(Icons.call_rounded),
                              label: Text(app.t(en: 'Call now', ur: 'کال کریں')),
                            ),
                          const SizedBox(height: 10),
                          if ('${_biz?['whatsapp'] ?? ''}'.trim().isNotEmpty)
                            OutlinedButton.icon(
                              onPressed: () {
                                final n = '${_biz!['whatsapp']}'.replaceAll(RegExp(r'\D'), '');
                                _launch('https://wa.me/$n');
                              },
                              icon: const Icon(Icons.chat_rounded),
                              label: Text(app.t(en: 'WhatsApp', ur: 'واٹس ایپ')),
                            ),
                        ],
                      ),
                    ),
                  ],
                ),
    );
  }
}
