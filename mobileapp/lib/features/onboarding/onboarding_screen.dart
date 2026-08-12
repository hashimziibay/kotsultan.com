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
  final _passwordCtrl = TextEditingController();
  final _loginPhoneCtrl = TextEditingController();
  final _loginPasswordCtrl = TextEditingController();

  String _locale = 'en';
  String _theme = 'light';
  String _accountType = 'user'; // user | business
  bool _modeLogin = false;
  bool _loading = false;
  bool _obscure = true;

  @override
  void dispose() {
    _nameCtrl.dispose();
    _phoneCtrl.dispose();
    _passwordCtrl.dispose();
    _loginPhoneCtrl.dispose();
    _loginPasswordCtrl.dispose();
    super.dispose();
  }

  Future<void> _submitRegister() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    final app = context.read<AppState>();
    try {
      await app.completeOnboarding(
        name: _nameCtrl.text.trim(),
        phone: _phoneCtrl.text.trim(),
        localeCode: _locale,
        theme: _theme,
        accountType: _accountType,
        password: _accountType == 'business' ? _passwordCtrl.text : null,
      );
      if (mounted && app.pendingUserSync) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              _locale == 'ur'
                  ? 'آف لائن محفوظ ہو گیا — آن لائن ہونے پر سنک ہوگا'
                  : 'Saved offline — will sync when you are online',
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

  Future<void> _submitLogin() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    final app = context.read<AppState>();
    final ok = await app.loginBusiness(
      phone: _loginPhoneCtrl.text.trim(),
      password: _loginPasswordCtrl.text,
    );
    if (!mounted) return;
    setState(() => _loading = false);
    if (!ok) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(app.error ?? (_locale == 'ur' ? 'لاگ ان ناکام' : 'Login failed'))),
      );
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
                        ? (_modeLogin
                            ? 'کاروباری اکاؤنٹ میں لاگ ان کریں'
                            : 'صارف یا کاروبار منتخب کریں')
                        : (_modeLogin
                            ? 'Sign in to your business account'
                            : 'Choose User or Business to get started'),
                    textAlign: TextAlign.center,
                    style: TextStyle(color: Colors.grey.shade600),
                  ),
                  const SizedBox(height: 20),
                  SegmentedButton<bool>(
                    segments: [
                      ButtonSegment(
                        value: false,
                        label: Text(isUrdu ? 'نیا اکاؤنٹ' : 'Sign up'),
                        icon: const Icon(Icons.person_add_alt_1_outlined),
                      ),
                      ButtonSegment(
                        value: true,
                        label: Text(isUrdu ? 'لاگ ان' : 'Login'),
                        icon: const Icon(Icons.login_rounded),
                      ),
                    ],
                    selected: {_modeLogin},
                    onSelectionChanged: (s) => setState(() => _modeLogin = s.first),
                  ),
                  const SizedBox(height: 20),
                  if (!_modeLogin) ...[
                    Text(
                      isUrdu ? 'اکاؤنٹ کی قسم' : 'I am a',
                      style: const TextStyle(fontWeight: FontWeight.w700),
                    ),
                    const SizedBox(height: 8),
                    SegmentedButton<String>(
                      segments: [
                        ButtonSegment(
                          value: 'user',
                          label: Text(isUrdu ? 'صارف' : 'User'),
                          icon: const Icon(Icons.person_outline),
                        ),
                        ButtonSegment(
                          value: 'business',
                          label: Text(isUrdu ? 'کاروبار' : 'Business'),
                          icon: const Icon(Icons.storefront_outlined),
                        ),
                      ],
                      selected: {_accountType},
                      onSelectionChanged: (s) => setState(() => _accountType = s.first),
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _nameCtrl,
                      textInputAction: TextInputAction.next,
                      decoration: InputDecoration(
                        labelText: isUrdu ? 'نام' : 'Full name',
                        prefixIcon: const Icon(Icons.person_outline),
                      ),
                      validator: (v) {
                        if (_modeLogin) return null;
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
                        labelText: isUrdu ? 'موبائل نمبر' : 'Mobile number',
                        prefixIcon: const Icon(Icons.phone_outlined),
                        hintText: '03XXXXXXXXX',
                      ),
                      validator: (v) {
                        if (_modeLogin) return null;
                        final digits = (v ?? '').replaceAll(RegExp(r'\D'), '');
                        if (digits.length < 10) {
                          return isUrdu ? 'درست نمبر درج کریں' : 'Enter a valid phone number';
                        }
                        return null;
                      },
                    ),
                    if (_accountType == 'business') ...[
                      const SizedBox(height: 14),
                      TextFormField(
                        controller: _passwordCtrl,
                        obscureText: _obscure,
                        decoration: InputDecoration(
                          labelText: isUrdu ? 'پاس ورڈ' : 'Password',
                          prefixIcon: const Icon(Icons.lock_outline),
                          suffixIcon: IconButton(
                            onPressed: () => setState(() => _obscure = !_obscure),
                            icon: Icon(_obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                          ),
                          helperText: isUrdu
                              ? 'کاروباری اکاؤنٹ کے لیے پاس ورڈ ضروری ہے'
                              : 'Required for business accounts (min 6)',
                        ),
                        validator: (v) {
                          if (_modeLogin || _accountType != 'business') return null;
                          if (v == null || v.length < 6) {
                            return isUrdu ? 'کم از کم ۶ حروف' : 'At least 6 characters';
                          }
                          return null;
                        },
                      ),
                    ],
                  ] else ...[
                    TextFormField(
                      controller: _loginPhoneCtrl,
                      keyboardType: TextInputType.phone,
                      inputFormatters: [FilteringTextInputFormatter.allow(RegExp(r'[0-9+\-\s]'))],
                      decoration: InputDecoration(
                        labelText: isUrdu ? 'موبائل نمبر' : 'Mobile number',
                        prefixIcon: const Icon(Icons.phone_outlined),
                        hintText: '03XXXXXXXXX',
                      ),
                      validator: (v) {
                        if (!_modeLogin) return null;
                        final digits = (v ?? '').replaceAll(RegExp(r'\D'), '');
                        if (digits.length < 10) {
                          return isUrdu ? 'درست نمبر درج کریں' : 'Enter a valid phone number';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 14),
                    TextFormField(
                      controller: _loginPasswordCtrl,
                      obscureText: _obscure,
                      decoration: InputDecoration(
                        labelText: isUrdu ? 'پاس ورڈ' : 'Password',
                        prefixIcon: const Icon(Icons.lock_outline),
                        suffixIcon: IconButton(
                          onPressed: () => setState(() => _obscure = !_obscure),
                          icon: Icon(_obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                        ),
                      ),
                      validator: (v) {
                        if (!_modeLogin) return null;
                        if (v == null || v.isEmpty) {
                          return isUrdu ? 'پاس ورڈ درکار ہے' : 'Password is required';
                        }
                        return null;
                      },
                    ),
                  ],
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
                    onPressed: _loading ? null : (_modeLogin ? _submitLogin : _submitRegister),
                    child: _loading
                        ? const SizedBox(
                            width: 22,
                            height: 22,
                            child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                          )
                        : Text(
                            _modeLogin
                                ? (isUrdu ? 'لاگ ان' : 'Login')
                                : (isUrdu ? 'جاری رکھیں' : 'Continue'),
                          ),
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
