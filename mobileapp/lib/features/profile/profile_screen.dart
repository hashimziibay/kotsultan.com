import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  late final TextEditingController _nameCtrl;
  late final TextEditingController _phoneCtrl;
  late String _locale;
  late String _theme;
  bool _loading = false;
  bool _initialized = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_initialized) return;
    final user = context.read<AppState>().user;
    _nameCtrl = TextEditingController(text: user?.name ?? '');
    _phoneCtrl = TextEditingController(text: user?.phone ?? '');
    _locale = user?.locale ?? context.read<AppState>().locale;
    _theme = user?.theme ?? (context.read<AppState>().themeMode == ThemeMode.dark ? 'dark' : 'light');
    _initialized = true;
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _phoneCtrl.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    final app = context.read<AppState>();
    try {
      await app.updateProfile(
        name: _nameCtrl.text.trim(),
        phone: _phoneCtrl.text.trim(),
        localeCode: _locale,
        theme: _theme,
      );
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(app.t(en: 'Profile updated', ur: 'پروفائل اپ ڈیٹ ہو گیا'))),
        );
      }
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(app.error ?? 'Error')),
        );
      }
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    return Scaffold(
      appBar: AppBar(
        title: Text(app.t(en: 'Profile', ur: 'پروفائل')),
        actions: [
          TextButton(
            onPressed: () async {
              await app.logout();
            },
            child: Text(app.t(en: 'Logout', ur: 'لاگ آؤٹ')),
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Center(
                child: CircleAvatar(
                  radius: 40,
                  backgroundColor: AppColors.emerald.withValues(alpha: 0.15),
                  child: Text(
                    (app.user?.name.isNotEmpty == true) ? app.user!.name.characters.first : '?',
                    style: const TextStyle(fontSize: 28, fontWeight: FontWeight.w800, color: AppColors.emerald),
                  ),
                ),
              ),
              const SizedBox(height: 20),
              TextFormField(
                controller: _nameCtrl,
                decoration: InputDecoration(labelText: app.t(en: 'Full name', ur: 'نام')),
                validator: (v) => (v == null || v.trim().length < 2)
                    ? app.t(en: 'Name is required', ur: 'نام درکار ہے')
                    : null,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _phoneCtrl,
                keyboardType: TextInputType.phone,
                inputFormatters: [FilteringTextInputFormatter.allow(RegExp(r'[0-9+\-\s]'))],
                decoration: InputDecoration(labelText: app.t(en: 'Contact number', ur: 'رابطہ نمبر')),
                validator: (v) {
                  final digits = (v ?? '').replaceAll(RegExp(r'\D'), '');
                  if (digits.length < 10) {
                    return app.t(en: 'Enter a valid phone number', ur: 'درست نمبر درج کریں');
                  }
                  return null;
                },
              ),
              const SizedBox(height: 18),
              Text(app.t(en: 'Language', ur: 'زبان'), style: const TextStyle(fontWeight: FontWeight.w700)),
              const SizedBox(height: 8),
              SegmentedButton<String>(
                segments: const [
                  ButtonSegment(value: 'en', label: Text('English')),
                  ButtonSegment(value: 'ur', label: Text('اردو')),
                ],
                selected: {_locale},
                onSelectionChanged: (s) => setState(() => _locale = s.first),
              ),
              const SizedBox(height: 18),
              Text(app.t(en: 'Theme', ur: 'تھیم'), style: const TextStyle(fontWeight: FontWeight.w700)),
              const SizedBox(height: 8),
              SegmentedButton<String>(
                segments: [
                  ButtonSegment(value: 'light', label: Text(app.t(en: 'Light', ur: 'روشن'))),
                  ButtonSegment(value: 'dark', label: Text(app.t(en: 'Dark', ur: 'تاریک'))),
                ],
                selected: {_theme},
                onSelectionChanged: (s) => setState(() => _theme = s.first),
              ),
              const SizedBox(height: 24),
              FilledButton(
                onPressed: _loading ? null : _save,
                child: _loading
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : Text(app.t(en: 'Save changes', ur: 'تبدیلیاں محفوظ کریں')),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
