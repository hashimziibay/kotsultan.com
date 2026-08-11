import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

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
      final res = await context.read<AppState>().catalog.getWallItem(widget.idOrSlug);
      final data = res.data;
      setState(() {
        if (data['entry'] is Map) {
          _entry = Map<String, dynamic>.from(data['entry'] as Map);
        } else {
          _entry = data;
        }
      });
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  List<Map<String, dynamic>> get _attachments {
    final raw = _entry?['attachments'];
    if (raw is! List) return const [];
    return raw.whereType<Map>().map((e) => Map<String, dynamic>.from(e)).toList();
  }

  List<Map<String, dynamic>> get _externalLinks {
    final raw = _entry?['external_links'];
    List<dynamic> list = const [];

    if (raw is List) {
      list = raw;
    } else if (raw is String && raw.trim().isNotEmpty) {
      try {
        final decoded = jsonDecode(raw);
        if (decoded is List) list = decoded;
      } catch (_) {
        list = const [];
      }
    } else {
      return const [];
    }

    final out = <Map<String, dynamic>>[];
    for (final item in list) {
      if (item is String) {
        final url = item.trim();
        if (url.isEmpty) continue;
        out.add({'url': url, 'title': 'Website', 'platform': 'website', 'label': 'Website'});
        continue;
      }
      if (item is! Map) continue;
      final m = Map<String, dynamic>.from(item);
      final url = '${m['url'] ?? m['link'] ?? ''}'.trim();
      if (url.isEmpty) continue;
      final platform = '${m['platform'] ?? ''}'.trim().toLowerCase();
      final label = '${m['label'] ?? ''}'.trim();
      final title = '${m['title'] ?? m['name'] ?? label}'.trim();
      out.add({
        'url': url,
        'title': title.isEmpty ? (label.isEmpty ? url : label) : title,
        'platform': platform.isEmpty ? 'other' : platform,
        'label': label.isEmpty ? title : label,
      });
    }
    return out;
  }

  IconData _platformIcon(String platform) {
    switch (platform) {
      case 'facebook':
        return Icons.facebook_rounded;
      case 'instagram':
        return Icons.camera_alt_rounded;
      case 'x':
      case 'twitter':
        return Icons.alternate_email_rounded;
      case 'youtube':
        return Icons.play_circle_fill_rounded;
      case 'linkedin':
        return Icons.business_center_rounded;
      case 'tiktok':
        return Icons.music_note_rounded;
      case 'whatsapp':
        return Icons.chat_rounded;
      case 'telegram':
        return Icons.send_rounded;
      case 'threads':
        return Icons.forum_rounded;
      case 'snapchat':
        return Icons.flash_on_rounded;
      case 'pinterest':
        return Icons.push_pin_rounded;
      case 'github':
        return Icons.code_rounded;
      case 'website':
        return Icons.language_rounded;
      default:
        return Icons.link_rounded;
    }
  }

  Future<void> _openUrl(String url) async {
    final uri = Uri.tryParse(url);
    if (uri == null) return;
    await launchUrl(uri, mode: LaunchMode.externalApplication);
  }

  void _openImageViewer(List<Map<String, dynamic>> images, int index) {
    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => _AttachmentImageViewer(images: images, initialIndex: index),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final photo = mediaUrl('${_entry?['photo'] ?? ''}');
    final attachments = _attachments;
    final images = attachments.where((a) => '${a['file_type']}' == 'image').toList();
    final docs = attachments.where((a) => '${a['file_type']}' != 'image').toList();
    final links = _externalLinks;

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
                    if ('${_entry?['category'] ?? ''}'.trim().isNotEmpty) ...[
                      const SizedBox(height: 10),
                      Center(
                        child: Chip(
                          backgroundColor: AppColors.tealSoft,
                          side: BorderSide.none,
                          label: Text(
                            '${_entry!['category']}',
                            style: const TextStyle(
                              color: AppColors.emeraldDark,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ),
                    ],
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
                    if ('${_entry?['awards'] ?? ''}'.isNotEmpty) ...[
                      const SizedBox(height: 16),
                      Text(app.t(en: 'Awards', ur: 'اعزازات'), style: const TextStyle(fontWeight: FontWeight.w800)),
                      const SizedBox(height: 8),
                      Text('${_entry!['awards']}'),
                    ],
                    if (links.isNotEmpty) ...[
                      const SizedBox(height: 22),
                      Text(
                        app.t(en: 'Social Links', ur: 'سوشل لنکس'),
                        style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16),
                      ),
                      const SizedBox(height: 10),
                      ...links.map((link) {
                        final url = '${link['url'] ?? ''}'.trim();
                        final platform = '${link['platform'] ?? 'other'}'.trim().toLowerCase();
                        final title = '${link['title'] ?? link['label'] ?? url}'.trim();
                        return Padding(
                          padding: const EdgeInsets.only(bottom: 8),
                          child: Material(
                            color: isDark ? AppColors.slate800 : AppColors.slate50,
                            borderRadius: BorderRadius.circular(12),
                            child: InkWell(
                              borderRadius: BorderRadius.circular(12),
                              onTap: url.isEmpty ? null : () => _openUrl(url),
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
                                      width: 40,
                                      height: 40,
                                      decoration: BoxDecoration(
                                        color: AppColors.emerald.withValues(alpha: isDark ? 0.22 : 0.12),
                                        borderRadius: BorderRadius.circular(10),
                                      ),
                                      child: Icon(_platformIcon(platform), color: AppColors.emeraldDark, size: 20),
                                    ),
                                    const SizedBox(width: 10),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            title.isEmpty ? url : title,
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                            style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13),
                                          ),
                                          Text(
                                            url,
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                            style: TextStyle(
                                              fontSize: 11,
                                              color: isDark ? Colors.white54 : AppColors.slate500,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                    Icon(
                                      Icons.open_in_new_rounded,
                                      color: isDark ? Colors.white54 : AppColors.slate500,
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        );
                      }),
                    ],
                    if (attachments.isNotEmpty) ...[
                      const SizedBox(height: 22),
                      Text(
                        app.t(en: 'Attachments', ur: 'منسلکات'),
                        style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16),
                      ),
                      const SizedBox(height: 10),
                      if (images.isNotEmpty) ...[
                        GridView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: images.length,
                          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 3,
                            mainAxisSpacing: 8,
                            crossAxisSpacing: 8,
                          ),
                          itemBuilder: (context, i) {
                            final img = images[i];
                            final url = mediaUrl('${img['url'] ?? ''}');
                            return Material(
                              color: isDark ? AppColors.slate800 : AppColors.slate50,
                              borderRadius: BorderRadius.circular(12),
                              clipBehavior: Clip.antiAlias,
                              child: InkWell(
                                onTap: url.isEmpty ? null : () => _openImageViewer(images, i),
                                child: url.isEmpty
                                    ? const Icon(Icons.image_not_supported_outlined)
                                    : AppNetworkImage(url: url, fit: BoxFit.cover),
                              ),
                            );
                          },
                        ),
                        if (docs.isNotEmpty) const SizedBox(height: 14),
                      ],
                      ...docs.map((doc) {
                        final url = mediaUrl('${doc['url'] ?? ''}');
                        final name = '${doc['original_name'] ?? doc['file_type'] ?? 'File'}';
                        final type = '${doc['file_type'] ?? 'file'}'.toUpperCase();
                        final sizeKb = ((doc['file_size'] is num ? doc['file_size'] as num : 0) / 1024);
                        final icon = type == 'PDF'
                            ? Icons.picture_as_pdf_rounded
                            : (type == 'WORD' ? Icons.description_rounded : Icons.attach_file_rounded);
                        return Padding(
                          padding: const EdgeInsets.only(bottom: 8),
                          child: Material(
                            color: isDark ? AppColors.slate800 : AppColors.slate50,
                            borderRadius: BorderRadius.circular(12),
                            child: InkWell(
                              borderRadius: BorderRadius.circular(12),
                              onTap: url.isEmpty ? null : () => _openUrl(url),
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
                                      width: 40,
                                      height: 40,
                                      decoration: BoxDecoration(
                                        color: AppColors.emerald.withValues(alpha: isDark ? 0.22 : 0.12),
                                        borderRadius: BorderRadius.circular(10),
                                      ),
                                      child: Icon(icon, color: AppColors.emeraldDark, size: 20),
                                    ),
                                    const SizedBox(width: 10),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            name,
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                            style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13),
                                          ),
                                          Text(
                                            '${type.toLowerCase()} · ${sizeKb.toStringAsFixed(1)} KB',
                                            style: TextStyle(
                                              fontSize: 11,
                                              color: isDark ? Colors.white54 : AppColors.slate500,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                    Icon(
                                      Icons.download_rounded,
                                      color: isDark ? Colors.white54 : AppColors.slate500,
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        );
                      }),
                    ],
                  ],
                ),
    );
  }
}

class _AttachmentImageViewer extends StatefulWidget {
  const _AttachmentImageViewer({required this.images, required this.initialIndex});

  final List<Map<String, dynamic>> images;
  final int initialIndex;

  @override
  State<_AttachmentImageViewer> createState() => _AttachmentImageViewerState();
}

class _AttachmentImageViewerState extends State<_AttachmentImageViewer> {
  late final PageController _controller;
  late int _index;

  @override
  void initState() {
    super.initState();
    _index = widget.initialIndex.clamp(0, widget.images.length - 1);
    _controller = PageController(initialPage: _index);
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        backgroundColor: Colors.black,
        foregroundColor: Colors.white,
        title: Text('${_index + 1} / ${widget.images.length}'),
      ),
      body: PageView.builder(
        controller: _controller,
        itemCount: widget.images.length,
        onPageChanged: (i) => setState(() => _index = i),
        itemBuilder: (context, i) {
          final url = mediaUrl('${widget.images[i]['url'] ?? ''}');
          return InteractiveViewer(
            child: Center(
              child: url.isEmpty
                  ? const Icon(Icons.broken_image_outlined, color: Colors.white54, size: 48)
                  : AppNetworkImage(url: url, fit: BoxFit.contain),
            ),
          );
        },
      ),
    );
  }
}
