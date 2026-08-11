import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../core/theme/app_theme.dart';
import '../../core/widgets/app_network_image.dart';

class BusinessCardTile extends StatelessWidget {
  const BusinessCardTile({
    super.key,
    required this.item,
    required this.onTap,
    this.isUrdu = false,
    this.compact = false,
  });

  final Map<String, dynamic> item;
  final VoidCallback onTap;
  final bool isUrdu;
  final bool compact;

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final image = '${item['image'] ?? ''}';
    final name = '${item['name'] ?? ''}';
    final category = '${item['category'] ?? ''}';
    final phone = '${item['phone'] ?? ''}'.trim();
    final owner = '${item['owner_name'] ?? ''}';
    final address = '${item['address'] ?? ''}';
    final muted = isDark ? const Color(0xFF94A3B8) : Colors.grey.shade600;
    final mutedSoft = isDark ? const Color(0xFF64748B) : Colors.grey.shade500;

    return Card(
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(10),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              AppNetworkImage(
                url: image,
                width: compact ? 78 : 96,
                height: compact ? 78 : 96,
                borderRadius: BorderRadius.circular(14),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (category.isNotEmpty)
                      Container(
                        margin: const EdgeInsets.only(bottom: 6),
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: isDark ? AppColors.emerald.withValues(alpha: 0.22) : AppColors.tealSoft,
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          category,
                          style: TextStyle(
                            color: isDark ? AppColors.emeraldLight : AppColors.emeraldDark,
                            fontSize: 11,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                      ),
                    Text(
                      name,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, height: 1.25),
                    ),
                    if (owner.isNotEmpty) ...[
                      const SizedBox(height: 4),
                      Text(owner, maxLines: 1, overflow: TextOverflow.ellipsis, style: TextStyle(color: muted, fontSize: 12)),
                    ],
                    if (address.isNotEmpty) ...[
                      const SizedBox(height: 4),
                      Text(address, maxLines: 1, overflow: TextOverflow.ellipsis, style: TextStyle(color: mutedSoft, fontSize: 12)),
                    ],
                    if (phone.isNotEmpty) ...[
                      const SizedBox(height: 8),
                      // Keep phone row LTR so icon + number stay aligned in Urdu/RTL.
                      Row(
                        textDirection: TextDirection.ltr,
                        children: [
                          const Icon(Icons.phone_rounded, size: 14, color: AppColors.emerald),
                          const SizedBox(width: 6),
                          Expanded(
                            child: Text(
                              phone,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
                              textDirection: TextDirection.ltr,
                              textAlign: TextAlign.left,
                            ),
                          ),
                          const SizedBox(width: 8),
                          Material(
                            color: isDark ? AppColors.emerald.withValues(alpha: 0.22) : AppColors.tealSoft,
                            borderRadius: BorderRadius.circular(10),
                            child: InkWell(
                              borderRadius: BorderRadius.circular(10),
                              onTap: () => launchUrl(Uri.parse('tel:$phone')),
                              child: Padding(
                                padding: const EdgeInsets.all(8),
                                child: Icon(
                                  Icons.call_rounded,
                                  size: 16,
                                  color: isDark ? AppColors.emeraldLight : AppColors.emeraldDark,
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
