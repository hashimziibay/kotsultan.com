import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../profile/profile_screen.dart';
import '../shell/app_drawer.dart';
import 'about_screen.dart';

class MoreScreen extends StatelessWidget {
  const MoreScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    return Scaffold(
      appBar: AppBar(
        leading: const DrawerMenuButton(),
        title: Text(app.t(en: 'More', ur: 'مزید')),
      ),
      body: ListView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
        children: [
          _SectionLabel(text: app.t(en: 'Account', ur: 'اکاؤنٹ')),
          _MenuCard(
            children: [
              _MenuTile(
                icon: Icons.person_outline_rounded,
                color: AppColors.emerald,
                title: app.t(en: 'Profile & settings', ur: 'پروفائل اور سیٹنگز'),
                subtitle: app.t(en: 'Name, phone, language, theme', ur: 'نام، نمبر، زبان، تھیم'),
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const ProfileScreen())),
              ),
            ],
          ),
          const SizedBox(height: 18),
          _SectionLabel(text: app.t(en: 'Information', ur: 'معلومات')),
          _MenuCard(
            children: [
              _MenuTile(
                icon: Icons.info_outline_rounded,
                color: AppColors.sky,
                title: app.t(en: 'About', ur: 'ہمارے بارے میں'),
                subtitle: app.t(en: 'Kot Sultan directory overview', ur: 'کوٹ سلطان ڈائریکٹری کا تعارف'),
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const AboutPage())),
              ),
              const Divider(height: 1),
              _MenuTile(
                icon: Icons.mail_outline_rounded,
                color: AppColors.amber,
                title: app.t(en: 'Contact', ur: 'رابطہ'),
                subtitle: app.t(en: 'Reach directory administration', ur: 'ڈائریکٹری انتظامیہ سے رابطہ'),
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const ContactPage())),
              ),
              const Divider(height: 1),
              _MenuTile(
                icon: Icons.volunteer_activism_outlined,
                color: const Color(0xFF8B5CF6),
                title: app.t(en: 'Volunteer', ur: 'رضاکار'),
                subtitle: app.t(en: 'Help improve local listings', ur: 'مقامی فہرست بہتر بنانے میں مدد کریں'),
                onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => const VolunteerPage())),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _SectionLabel extends StatelessWidget {
  const _SectionLabel({required this.text});
  final String text;

  @override
  Widget build(BuildContext context) {
    final muted = Theme.of(context).brightness == Brightness.dark
        ? Colors.white60
        : Colors.grey.shade600;
    return Padding(
      padding: const EdgeInsets.only(bottom: 8, left: 4, right: 4),
      child: Text(text, style: TextStyle(color: muted, fontWeight: FontWeight.w700, fontSize: 12)),
    );
  }
}

class _MenuCard extends StatelessWidget {
  const _MenuCard({required this.children});
  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    return Card(clipBehavior: Clip.antiAlias, child: Column(children: children));
  }
}

class _MenuTile extends StatelessWidget {
  const _MenuTile({
    required this.icon,
    required this.color,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  final IconData icon;
  final Color color;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return ListTile(
      onTap: onTap,
      leading: Container(
        width: 42,
        height: 42,
        decoration: BoxDecoration(
          color: color.withValues(alpha: isDark ? 0.22 : 0.12),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Icon(icon, color: color),
      ),
      title: Text(
        title,
        style: TextStyle(fontWeight: FontWeight.w700, color: scheme.onSurface),
      ),
      subtitle: Text(
        subtitle,
        style: TextStyle(color: isDark ? Colors.white70 : scheme.onSurfaceVariant),
      ),
      trailing: Icon(Icons.chevron_right_rounded, color: isDark ? Colors.white54 : scheme.onSurfaceVariant),
    );
  }
}

class ContactPage extends StatelessWidget {
  const ContactPage({super.key});

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    return Scaffold(
      appBar: AppBar(title: Text(app.t(en: 'Contact', ur: 'رابطہ'))),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _InfoHero(
            icon: Icons.mail_rounded,
            title: app.t(en: 'Directory Administration', ur: 'ڈائریکٹری انتظامیہ'),
            body: app.t(
              en: 'Have a business to register or need a listing update? Reach out to the KotSultan.com team.',
              ur: 'کاروبار شامل کرانا ہے یا فہرست اپ ڈیٹ چاہیے؟ کوٹ سلطان ٹیم سے رابطہ کریں۔',
            ),
          ),
          const SizedBox(height: 12),
          Card(
            child: Column(
              children: [
                ListTile(
                  leading: const Icon(Icons.person_rounded, color: AppColors.emerald),
                  title: const Text('Muhammad Hashim', style: TextStyle(fontWeight: FontWeight.w800)),
                  subtitle: Text(app.t(
                    en: 'Founder of KotSultan.com\nDirector, ZIIBAY SOFT and VERTEX SCHOOL SYSTEM & IT ACADEMY',
                    ur: 'کوٹ سلطان ڈاٹ کام کے بانی\nڈائریکٹر، زیبے سافٹ اور ورٹیکس اسکول سسٹم اینڈ آئی ٹی اکیڈمی',
                  )),
                  isThreeLine: true,
                ),
                const Divider(height: 1),
                ListTile(
                  leading: const Icon(Icons.place_rounded, color: AppColors.emerald),
                  title: Text(app.t(en: 'Office location', ur: 'دفتر کا پتہ')),
                  subtitle: Text(app.t(
                    en: 'Near Union Council, Vertex School System & IT Academy Street, Kot Sultan',
                    ur: 'یونین کونسل کے قریب، ورٹیکس اسکول سسٹم اینڈ آئی ٹی اکیڈمی سٹریٹ، کوٹ سلطان',
                  )),
                ),
                const Divider(height: 1),
                ListTile(
                  leading: const Icon(Icons.call_rounded, color: AppColors.emerald),
                  title: Text(app.t(en: 'Phone / Helpline', ur: 'فون / ہیلپ لائن')),
                  subtitle: const Text('0313 6350169', textDirection: TextDirection.ltr),
                  trailing: const Icon(Icons.open_in_new_rounded),
                  onTap: () => launchUrl(Uri.parse('tel:03136350169')),
                ),
                const Divider(height: 1),
                ListTile(
                  leading: const Icon(Icons.chat_rounded, color: AppColors.emerald),
                  title: Text(app.t(en: 'WhatsApp', ur: 'واٹس ایپ')),
                  subtitle: const Text('0313 6350169', textDirection: TextDirection.ltr),
                  trailing: const Icon(Icons.open_in_new_rounded),
                  onTap: () => launchUrl(Uri.parse('https://wa.me/923136350169')),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class VolunteerPage extends StatelessWidget {
  const VolunteerPage({super.key});

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    return Scaffold(
      appBar: AppBar(title: Text(app.t(en: 'Volunteer', ur: 'رضاکار'))),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _InfoHero(
            icon: Icons.volunteer_activism_rounded,
            title: app.t(en: 'Become a Volunteer', ur: 'رضاکار بنیں'),
            body: app.t(
              en: 'Anyone can help improve the Kot Sultan directory by contributing accurate local information about businesses, services, and community places.',
              ur: 'آپ درست مقامی معلومات دے کر کوٹ سلطان ڈائریکٹری بہتر بنانے میں مدد کر سکتے ہیں۔',
            ),
          ),
          const SizedBox(height: 12),
          _InfoCard(
            icon: Icons.handshake_rounded,
            title: app.t(en: 'Why volunteer?', ur: 'رضاکاری کیوں؟'),
            body: app.t(
              en: 'Your small contributions can make a big difference in building the most complete and accurate directory for Kot Sultan.',
              ur: 'آپ کی چھوٹی سی مدد کوٹ سلطان کی مکمل اور درست ڈائریکٹری بنانے میں بڑا فرق لا سکتی ہے۔',
            ),
          ),
          const SizedBox(height: 16),
          FilledButton.icon(
            onPressed: () => launchUrl(Uri.parse('https://wa.me/923136350169?text=I%20want%20to%20volunteer%20for%20KotSultan.com')),
            icon: const Icon(Icons.chat_rounded),
            label: Text(app.t(en: 'Message us on WhatsApp', ur: 'واٹس ایپ پر پیغام بھیجیں')),
          ),
        ],
      ),
    );
  }
}

class _InfoHero extends StatelessWidget {
  const _InfoHero({required this.icon, required this.title, required this.body});
  final IconData icon;
  final String title;
  final String body;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF047857), Color(0xFF0D9488)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: Colors.white, size: 28),
          const SizedBox(height: 12),
          Text(title, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 20)),
          const SizedBox(height: 8),
          Text(body, style: TextStyle(color: Colors.white.withValues(alpha: 0.92), height: 1.45)),
        ],
      ),
    );
  }
}

class _InfoCard extends StatelessWidget {
  const _InfoCard({required this.icon, required this.title, required this.body});
  final IconData icon;
  final String title;
  final String body;

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final onSurface = Theme.of(context).colorScheme.onSurface;
    final bodyMuted = isDark ? const Color(0xFFCBD5E1) : Colors.grey.shade700;

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
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
                    style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: onSurface),
                  ),
                  const SizedBox(height: 6),
                  Text(body, style: TextStyle(color: bodyMuted, height: 1.4, fontSize: 14)),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
