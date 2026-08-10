import 'package:flutter/material.dart';

import '../theme/app_theme.dart';
import '../media_url.dart';

/// Network image that works on Flutter web (uses browser img, no CORS fetch).
class AppNetworkImage extends StatelessWidget {
  const AppNetworkImage({
    super.key,
    required this.url,
    this.fit = BoxFit.cover,
    this.width,
    this.height,
    this.borderRadius,
    this.placeholderIcon = Icons.storefront_rounded,
  });

  final String? url;
  final BoxFit fit;
  final double? width;
  final double? height;
  final BorderRadius? borderRadius;
  final IconData placeholderIcon;

  @override
  Widget build(BuildContext context) {
    final resolved = mediaUrl(url);
    final placeholder = Container(
      width: width,
      height: height,
      color: AppColors.tealSoft,
      alignment: Alignment.center,
      child: Icon(placeholderIcon, color: AppColors.emerald, size: 36),
    );

    if (resolved.isEmpty) {
      return clip(placeholder);
    }

    return clip(
      Image.network(
        resolved,
        width: width,
        height: height,
        fit: fit,
        gaplessPlayback: true,
        filterQuality: FilterQuality.medium,
        errorBuilder: (context, error, stackTrace) => placeholder,
        loadingBuilder: (context, child, progress) {
          if (progress == null) return child;
          return Container(
            width: width,
            height: height,
            color: AppColors.tealSoft,
            alignment: Alignment.center,
            child: const SizedBox(
              width: 22,
              height: 22,
              child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.emerald),
            ),
          );
        },
      ),
    );
  }

  Widget clip(Widget child) {
    if (borderRadius == null) return child;
    return ClipRRect(borderRadius: borderRadius!, child: child);
  }
}
