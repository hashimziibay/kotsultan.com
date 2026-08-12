import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import 'my_business_list_screen.dart';

/// Switch a community user → business account by setting a password.
/// Listing identity stays tied to the same contact/mobile number.
class UpgradeBusinessScreen extends StatefulWidget {
  const UpgradeBusinessScreen({super.key});

  @override
  State<UpgradeBusinessScreen> createState() => _UpgradeBusinessScreenState();
}

class _UpgradeBusinessScreenState extends State<UpgradeBusinessScreen> {
  final _formKey = GlobalKey<FormState>();
  final _passwordCtrl = TextEditingController();
  final _confirmCtrl = TextEditingController();
  bool _loading = false;
  bool _obscure = true;

  @override
  void dispose() {
    _passwordCtrl.dispose();
    _confirmCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);
    final app = context.read<AppState>();
    final ok = await app.upgradeToBusiness(
      password: _passwordCtrl.text,
      confirmPassword: _confirmCtrl.text,
    );
    if (!mounted) return;
    setState(() => _loading = false);

    if (!ok) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(app.error ?? app.t(en: 'Could not switch account', ur: 'اکاؤنٹ تبدیل نہیں ہو سکا'))),
      );
      return;
    }

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          app.t(
            en: 'Switched to business account. Add your listing next.',
            ur: 'کاروباری اکاؤنٹ بن گیا۔ اب لسٹنگ شامل کریں۔',
          ),
        ),
      ),
    );

    Navigator.of(context).pushReplacement(
      MaterialPageRoute(builder: (_) => const MyBusinessListScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final phone = app.user?.phone ?? '';

    return Scaffold(
      appBar: AppBar(
        title: Text(app.t(en: 'Switch to Business', ur: 'کاروباری اکاؤنٹ')),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: AppColors.amber.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      app.t(
                        en: 'List your shop with a business password',
                        ur: 'پاس ورڈ سیٹ کر کے اپنا کاروبار درج کریں',
                      ),
                      style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      app.t(
                        en: 'Your listing is registered on this contact number. Only one business is allowed per number.',
                        ur: 'لسٹنگ اسی رابطہ نمبر پر رجسٹر ہوگی۔ ایک نمبر پر صرف ایک کاروبار۔',
                      ),
                      style: TextStyle(color: Colors.grey.shade700, height: 1.35),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),
              TextFormField(
                initialValue: phone,
                enabled: false,
                decoration: InputDecoration(
                  labelText: app.t(en: 'Contact number', ur: 'رابطہ نمبر'),
                  helperText: app.t(
                    en: 'Business will use this number',
                    ur: 'کاروبار اسی نمبر پر ہوگا',
                  ),
                ),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _passwordCtrl,
                obscureText: _obscure,
                decoration: InputDecoration(
                  labelText: app.t(en: 'Business password', ur: 'کاروباری پاس ورڈ'),
                  suffixIcon: IconButton(
                    onPressed: () => setState(() => _obscure = !_obscure),
                    icon: Icon(_obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                  ),
                ),
                validator: (v) {
                  if (v == null || v.length < 6) {
                    return app.t(en: 'Min 6 characters', ur: 'کم از کم 6 حروف');
                  }
                  return null;
                },
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _confirmCtrl,
                obscureText: _obscure,
                decoration: InputDecoration(
                  labelText: app.t(en: 'Confirm password', ur: 'پاس ورڈ کی تصدیق'),
                ),
                validator: (v) {
                  if (v != _passwordCtrl.text) {
                    return app.t(en: 'Passwords do not match', ur: 'پاس ورڈ مماثل نہیں');
                  }
                  return null;
                },
              ),
              const SizedBox(height: 24),
              FilledButton(
                onPressed: _loading ? null : _submit,
                child: _loading
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : Text(app.t(en: 'Set password & switch', ur: 'پاس ورڈ سیٹ کریں اور تبدیل کریں')),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
