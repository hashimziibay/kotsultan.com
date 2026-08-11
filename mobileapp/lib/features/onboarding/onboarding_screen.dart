import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';

class OnboardingScreen extends StatefulWidget {
  const OnboardingScreen({super.key});

  @override
  State<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends State<OnboardingScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  String _locale = 'en';
  String _theme = 'light';
  bool _loading = false;

  @override
  void dispose() {
    _nameCtrl.dispose();
    _phoneCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    final app = context.read<AppState>();
    try {
      await app.completeOnboarding(
        name: _nameCtrl.text.trim(),
        phone: _phoneCtrl.text.trim(),
        localeCode: _locale,
        theme: _theme,
      );
      if (mounted && app.pendingUserSync) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              _locale == 'ur'
                  ? 'آف لائن محفوظ ہو گیا — آن لائن ہونے پر ایڈمن کو بھیج دیا جائے گا'
                  : 'Saved offline — will sync to admin when you are online',
            ),
          ),
        );
      }
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(app.error ?? 'Could not continue')),
        );
      }
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isUrdu = _locale == 'ur';
    return Directionality(
      textDirection: isUrdu ? TextDirection.rtl : TextDirection.ltr,
      child: Scaffold(
        body: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(24, 32, 24, 24),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Center(
                    child: Container(
                      width: 64,
                      height: 64,
                      decoration: BoxDecoration(
                        color: AppColors.emerald,
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: const Icon(Icons.location_on, color: Colors.white, size: 36),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    isUrdu ? 'کوٹ سلطان ڈاٹ کام' : 'KotSultan.com',
                    textAlign: TextAlign.center,
                    style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                          fontWeight: FontWeight.w800,
                        ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    isUrdu
                        ? 'ایک بار اپنی تفصیلات دیں اور ڈائریکٹری استعمال کریں'
                        : 'Share your details once, then browse the directory',
                    textAlign: TextAlign.center,
                    style: TextStyle(color: Colors.grey.shade600),
                  ),
                  const SizedBox(height: 28),
                  TextFormField(
                    controller: _nameCtrl,
                    textInputAction: TextInputAction.next,
                    decoration: InputDecoration(
                      labelText: isUrdu ? 'نام' : 'Full name',
                      prefixIcon: const Icon(Icons.person_outline),
                    ),
                    validator: (v) {
                      if (v == null || v.trim().length < 2) {
                        return isUrdu ? 'نام درکار ہے' : 'Name is required';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 14),
                  TextFormField(
                    controller: _phoneCtrl,
                    keyboardType: TextInputType.phone,
                    inputFormatters: [FilteringTextInputFormatter.allow(RegExp(r'[0-9+\-\s]'))],
                    decoration: InputDecoration(
                      labelText: isUrdu ? 'رابطہ نمبر' : 'Contact number',
                      prefixIcon: const Icon(Icons.phone_outlined),
                      hintText: '03XXXXXXXXX',
                    ),
                    validator: (v) {
                      final digits = (v ?? '').replaceAll(RegExp(r'\D'), '');
                      if (digits.length < 10) {
                        return isUrdu ? 'درست نمبر درج کریں' : 'Enter a valid phone number';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 20),
                  Text(
                    isUrdu ? 'زبان' : 'Language',
                    style: const TextStyle(fontWeight: FontWeight.w700),
                  ),
                  const SizedBox(height: 8),
                  SegmentedButton<String>(
                    segments: const [
                      ButtonSegment(value: 'en', label: Text('English'), icon: Icon(Icons.language)),
                      ButtonSegment(value: 'ur', label: Text('اردو'), icon: Icon(Icons.translate)),
                    ],
                    selected: {_locale},
                    onSelectionChanged: (s) {
                      setState(() => _locale = s.first);
                      context.read<AppState>().setLocaleLocal(_locale);
                    },
                  ),
                  const SizedBox(height: 20),
                  Text(
                    isUrdu ? 'تھیم' : 'Theme',
                    style: const TextStyle(fontWeight: FontWeight.w700),
                  ),
                  const SizedBox(height: 8),
                  SegmentedButton<String>(
                    segments: [
                      ButtonSegment(
                        value: 'light',
                        label: Text(isUrdu ? 'روشن' : 'Light'),
                        icon: const Icon(Icons.light_mode_outlined),
                      ),
                      ButtonSegment(
                        value: 'dark',
                        label: Text(isUrdu ? 'تاریک' : 'Dark'),
                        icon: const Icon(Icons.dark_mode_outlined),
                      ),
                    ],
                    selected: {_theme},
                    onSelectionChanged: (s) {
                      setState(() => _theme = s.first);
                      context.read<AppState>().setThemeLocal(_theme);
                    },
                  ),
                  const SizedBox(height: 28),
                  FilledButton(
                    onPressed: _loading ? null : _submit,
                    child: _loading
                        ? const SizedBox(
                            width: 22,
                            height: 22,
                            child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                          )
                        : Text(isUrdu ? 'جاری رکھیں' : 'Continue'),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
