import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../state/app_state.dart';
import '../theme/app_theme.dart';

class OfflineBanner extends StatelessWidget {
  const OfflineBanner({super.key, required this.visible});

  final bool visible;

  @override
  Widget build(BuildContext context) {
    if (!visible) return const SizedBox.shrink();
    final app = context.watch<AppState>();
    return Material(
      color: AppColors.amber.withValues(alpha: 0.18),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        child: Row(
          children: [
            const Icon(Icons.cloud_off_rounded, size: 18, color: AppColors.amber),
            const SizedBox(width: 8),
            Expanded(
              child: Text(
                app.t(
                  en: 'Offline mode — showing saved directory data',
                  ur: 'آف لائن موڈ — محفوظ شدہ ڈائریکٹری دکھائی جا رہی ہے',
                ),
                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
