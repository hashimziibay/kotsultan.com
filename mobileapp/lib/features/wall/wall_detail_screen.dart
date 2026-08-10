import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/media_url.dart';
import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/app_network_image.dart';

class WallDetailScreen extends StatefulWidget {
  const WallDetailScreen({super.key, required this.idOrSlug});
  final String idOrSlug;

  @override
  State<WallDetailScreen> createState() => _WallDetailScreenState();
}

class _WallDetailScreenState extends State<WallDetailScreen> {
  bool _loading = true;
  String? _error;
  Map<String, dynamic>? _entry;

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
      final res = await context.read<AppState>().api.get('wall/${widget.idOrSlug}');
      final data = res['data'] as Map<String, dynamic>;
      setState(() => _entry = data['entry'] as Map<String, dynamic>?);
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final photo = mediaUrl('${_entry?['photo'] ?? ''}');
    return Scaffold(
      appBar: AppBar(title: Text(app.t(en: 'Profile', ur: 'پروفائل'))),
      body: _loading
          ? const Center(child: CircularProgressIndicator(color: AppColors.emerald))
          : _error != null
              ? Center(child: Text(_error!))
              : ListView(
                  padding: const EdgeInsets.all(16),
                  children: [
                    Center(
                      child: ClipOval(
                        child: SizedBox(
                          width: 112,
                          height: 112,
                          child: photo.isNotEmpty
                              ? AppNetworkImage(url: photo, placeholderIcon: Icons.person_rounded)
                              : Container(
                                  color: AppColors.tealSoft,
                                  child: const Icon(Icons.person_rounded, size: 48, color: AppColors.emerald),
                                ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    Text(
                      '${_entry?['name'] ?? ''}',
                      textAlign: TextAlign.center,
                      style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w800),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      '${_entry?['profession'] ?? ''}',
                      textAlign: TextAlign.center,
                      style: const TextStyle(color: AppColors.emerald, fontWeight: FontWeight.w700),
                    ),
                    if ('${_entry?['intro'] ?? ''}'.isNotEmpty) ...[
                      const SizedBox(height: 20),
                      Text(app.t(en: 'Biography', ur: 'سوانح'), style: const TextStyle(fontWeight: FontWeight.w800)),
                      const SizedBox(height: 8),
                      Text('${_entry!['intro']}'),
                    ],
                    if ('${_entry?['achievements'] ?? ''}'.isNotEmpty) ...[
                      const SizedBox(height: 16),
                      Text(app.t(en: 'Achievements', ur: 'کارنامے'), style: const TextStyle(fontWeight: FontWeight.w800)),
                      const SizedBox(height: 8),
                      Text('${_entry!['achievements']}'),
                    ],
                  ],
                ),
    );
  }
}
