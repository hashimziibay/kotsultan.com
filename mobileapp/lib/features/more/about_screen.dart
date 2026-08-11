import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';

/// Standalone About screen (light + dark, LTR + RTL).
class AboutPage extends StatelessWidget {
  const AboutPage({super.key});

  static const _phone = '03136350169';
  static const _phoneDisplay = '0313 6350169';

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final bg = isDark ? AppColors.slate900 : const Color(0xFFF4F7F6);
    final card = isDark ? AppColors.slate800 : Colors.white;
    final title = isDark ? Colors.white : AppColors.slate900;
    final body = isDark ? const Color(0xFFCBD5E1) : AppColors.slate700;
    final border = isDark ? AppColors.slate700 : const Color(0xFFE6EEEA);

    return Scaffold(
      backgroundColor: bg,
      appBar: AppBar(
        title: Text(app.t(en: 'About', ur: 'ہمارے بارے میں')),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 28),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF047857), Color(0xFF0D9488)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Column(
                  children: [
                    Container(
                      width: 56,
                      height: 56,
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.18),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: const Icon(Icons.info_rounded, color: Colors.white, size: 30),
                    ),
                    const SizedBox(height: 14),
                    Text(
                      app.t(en: 'About Kot Sultan Directory', ur: 'کوٹ سلطان ڈائریکٹری کے بارے میں'),
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.w900,
                        fontSize: 22,
                        height: 1.25,
                      ),
                    ),
                    const SizedBox(height: 10),
                    Text(
                      app.t(
                        en: 'KotSultan.com is the digital directory for Kot Sultan, Pakistan. It helps residents and visitors find local businesses, schools, clinics, and essential services.',
                        ur: 'کوٹ سلطان ڈاٹ کام مقامی کاروبار، اسکولز، کلینکس اور ضروری خدمات تلاش کرنے کے لیے بنائی گئی ڈیجیٹل ڈائریکٹری ہے۔',
                      ),
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.92),
                        height: 1.45,
                        fontSize: 14,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 14),
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: card,
                  borderRadius: BorderRadius.circular(18),
                  border: Border.all(color: border),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Container(
                          width: 52,
                          height: 52,
                          decoration: BoxDecoration(
                            color: isDark ? AppColors.emerald.withValues(alpha: 0.22) : AppColors.tealSoft,
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Icon(
                            Icons.person_rounded,
                            color: isDark ? AppColors.emeraldLight : AppColors.emeraldDark,
                            size: 28,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                app.t(en: 'Founder', ur: 'بانی'),
                                style: TextStyle(
                                  color: isDark ? Colors.white60 : Colors.grey.shade600,
                                  fontSize: 12,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                              Text(
                                'Muhammad Hashim',
                                style: TextStyle(
                                  color: title,
                                  fontWeight: FontWeight.w900,
                                  fontSize: 18,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    Text(
                      app.t(en: 'Founder of KotSultan.com', ur: 'کوٹ سلطان ڈاٹ کام کے بانی'),
                      style: TextStyle(
                        color: isDark ? AppColors.emeraldLight : AppColors.emeraldDark,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      app.t(
                        en: 'Director, ZIIBAY SOFT and VERTEX SCHOOL SYSTEM & IT ACADEMY',
                        ur: 'ڈائریکٹر، زیبے سافٹ اور ورٹیکس اسکول سسٹم اینڈ آئی ٹی اکیڈمی',
                      ),
                      style: TextStyle(color: body, height: 1.4),
                    ),
                    const SizedBox(height: 14),
                    Text(
                      _phoneDisplay,
                      textDirection: TextDirection.ltr,
                      style: TextStyle(color: title, fontWeight: FontWeight.w800, fontSize: 16),
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(
                          child: FilledButton.icon(
                            onPressed: () => launchUrl(Uri.parse('tel:$_phone')),
                            icon: const Icon(Icons.call_rounded, size: 18),
                            label: Text(app.t(en: 'Call', ur: 'کال')),
                          ),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: OutlinedButton.icon(
                            onPressed: () => launchUrl(Uri.parse('https://wa.me/923136350169')),
                            icon: const Icon(Icons.chat_rounded, size: 18),
                            label: const Text('WhatsApp'),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 14),
              _AboutBlock(
                icon: Icons.flag_rounded,
                title: app.t(en: 'Our Mission', ur: 'ہمارا مشن'),
                body: app.t(
                  en: 'To empower residents, shopkeepers, and visitors with a clean, fast directory that connects people with local businesses and community helpers.',
                  ur: 'مقامی رہائشیوں، دکانداروں اور زائرین کو تیز اور صاف ڈائریکٹری فراہم کرنا جو انہیں مقامی کاروبار اور کمیونٹی سے جوڑے۔',
                ),
                card: card,
                titleColor: title,
                bodyColor: body,
                border: border,
                isDark: isDark,
              ),
              const SizedBox(height: 12),
              _AboutBlock(
                icon: Icons.visibility_rounded,
                title: app.t(en: 'Our Vision', ur: 'ہماری ویژن'),
                body: app.t(
                  en: 'To make Kot Sultan one of the most digitally connected towns in Punjab while preserving community heritage.',
                  ur: 'کوٹ سلطان کو پنجاب کے سب سے زیادہ ڈیجیٹل طور پر منسلک قصبوں میں شامل کرنا اور کمیونٹی ورثے کو محفوظ رکھنا۔',
                ),
                card: card,
                titleColor: title,
                bodyColor: body,
                border: border,
                isDark: isDark,
              ),
              const SizedBox(height: 12),
              _AboutBlock(
                icon: Icons.map_rounded,
                title: app.t(en: 'Location', ur: 'مقام'),
                body: app.t(
                  en: 'District Layyah, Punjab — along the Indus corridor with connections to Layyah, Chowk Azam, and Dera Ghazi Khan.',
                  ur: 'ضلع لیہ، پنجاب — لیہ، چوک اعظم اور ڈیرہ غازی خان سے منسلک۔',
                ),
                card: card,
                titleColor: title,
                bodyColor: body,
                border: border,
                isDark: isDark,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _AboutBlock extends StatelessWidget {
  const _AboutBlock({
    required this.icon,
    required this.title,
    required this.body,
    required this.card,
    required this.titleColor,
    required this.bodyColor,
    required this.border,
    required this.isDark,
  });

  final IconData icon;
  final String title;
  final String body;
  final Color card;
  final Color titleColor;
  final Color bodyColor;
  final Color border;
  final bool isDark;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: card,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: border),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              color: isDark ? AppColors.emerald.withValues(alpha: 0.22) : AppColors.tealSoft,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: isDark ? AppColors.emeraldLight : AppColors.emeraldDark),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: TextStyle(color: titleColor, fontWeight: FontWeight.w800, fontSize: 16),
                ),
                const SizedBox(height: 6),
                Text(body, style: TextStyle(color: bodyColor, height: 1.45, fontSize: 14)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
