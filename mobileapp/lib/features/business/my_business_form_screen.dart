import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../../core/api/api_client.dart';
import '../../core/state/app_state.dart';

class MyBusinessFormScreen extends StatefulWidget {
  const MyBusinessFormScreen({super.key, this.business});

  final Map<String, dynamic>? business;

  @override
  State<MyBusinessFormScreen> createState() => _MyBusinessFormScreenState();
}

class _MyBusinessFormScreenState extends State<MyBusinessFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameEn = TextEditingController();
  final _nameUr = TextEditingController();
  final _addressEn = TextEditingController();
  final _addressUr = TextEditingController();
  final _descEn = TextEditingController();
  final _descUr = TextEditingController();
  final _phone = TextEditingController();
  final _whatsapp = TextEditingController();
  final _email = TextEditingController();
  final _website = TextEditingController();
  final _hours = TextEditingController();

  List<Map<String, dynamic>> _categories = [];
  int? _categoryId;
  bool _loadingCats = true;
  bool _saving = false;

  bool get _isEdit => widget.business != null;

  @override
  void initState() {
    super.initState();
    final b = widget.business;
    if (b != null) {
      _nameEn.text = '${b['name_en'] ?? ''}';
      _nameUr.text = '${b['name_ur'] ?? ''}';
      _addressEn.text = '${b['address_en'] ?? ''}';
      _addressUr.text = '${b['address_ur'] ?? ''}';
      _descEn.text = '${b['description_en'] ?? ''}';
      _descUr.text = '${b['description_ur'] ?? ''}';
      _phone.text = '${b['phone'] ?? ''}';
      _whatsapp.text = '${b['whatsapp'] ?? ''}';
      _email.text = '${b['email'] ?? ''}';
      _website.text = '${b['website'] ?? ''}';
      _hours.text = '${b['opening_hours'] ?? ''}';
      final cid = b['category_id'];
      if (cid != null) {
        _categoryId = cid is int ? cid : int.tryParse('$cid');
      }
    }
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadCategories());
  }

  @override
  void dispose() {
    _nameEn.dispose();
    _nameUr.dispose();
    _addressEn.dispose();
    _addressUr.dispose();
    _descEn.dispose();
    _descUr.dispose();
    _phone.dispose();
    _whatsapp.dispose();
    _email.dispose();
    _website.dispose();
    _hours.dispose();
    super.dispose();
  }

  Future<void> _loadCategories() async {
    try {
      final res = await context.read<AppState>().catalog.getCategories();
      final list = res.data
          .map((e) => Map<String, dynamic>.from(e as Map))
          .toList();
      if (!mounted) return;
      setState(() {
        _categories = list;
        _loadingCats = false;
        if (_categoryId == null && list.isNotEmpty) {
          final id = list.first['id'];
          _categoryId = id is int ? id : int.tryParse('$id');
        }
      });
    } catch (e) {
      if (!mounted) return;
      setState(() => _loadingCats = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(e.toString())),
      );
    }
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    if (_categoryId == null || _categoryId! < 1) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            context.read<AppState>().t(en: 'Select a category', ur: 'زمرہ منتخب کریں'),
          ),
        ),
      );
      return;
    }

    setState(() => _saving = true);
    final app = context.read<AppState>();
    final body = <String, dynamic>{
      'name_en': _nameEn.text.trim(),
      'name_ur': _nameUr.text.trim(),
      'category_id': _categoryId,
      'address_en': _addressEn.text.trim(),
      'address_ur': _addressUr.text.trim(),
      'description_en': _descEn.text.trim(),
      'description_ur': _descUr.text.trim(),
      'phone': _phone.text.trim(),
      'whatsapp': _whatsapp.text.trim(),
      'email': _email.text.trim(),
      'website': _website.text.trim(),
      'opening_hours': _hours.text.trim(),
    };

    try {
      if (_isEdit) {
        final id = widget.business!['id'];
        await app.api.put('my-businesses/$id', body: body);
      } else {
        await app.api.post('my-businesses', body: body);
      }
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            app.t(
              en: _isEdit
                  ? 'Business updated'
                  : 'Submitted for admin review',
              ur: _isEdit
                  ? 'کاروبار اپڈیٹ ہو گیا'
                  : 'ایڈمن منظوری کے لیے بھیج دیا گیا',
            ),
          ),
        ),
      );
      Navigator.of(context).pop(true);
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(e is ApiException ? e.message : e.toString())),
      );
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    return Scaffold(
      appBar: AppBar(
        title: Text(
          _isEdit
              ? app.t(en: 'Edit business', ur: 'کاروبار ترمیم')
              : app.t(en: 'Add business', ur: 'کاروبار شامل کریں'),
        ),
      ),
      body: _loadingCats
          ? const Center(child: CircularProgressIndicator())
          : Form(
              key: _formKey,
              child: ListView(
                padding: const EdgeInsets.fromLTRB(16, 12, 16, 32),
                children: [
                  if (_isEdit && '${widget.business?['status'] ?? ''}' == 'pending')
                    Container(
                      margin: const EdgeInsets.only(bottom: 12),
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFFF7ED),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: const Color(0xFFFDBA74)),
                      ),
                      child: Text(
                        app.t(
                          en: 'This listing is pending admin approval.',
                          ur: 'یہ فہرست ایڈمن منظوری کا انتظار کر رہی ہے۔',
                        ),
                        style: const TextStyle(fontSize: 13, color: Color(0xFF9A3412)),
                      ),
                    ),
                  DropdownButtonFormField<int>(
                    value: _categoryId,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Category', ur: 'زمرہ'),
                    ),
                    items: _categories.map((c) {
                      final id = c['id'] is int ? c['id'] as int : int.tryParse('${c['id']}') ?? 0;
                      final label = app.isUrdu
                          ? ('${c['name_ur'] ?? ''}'.trim().isNotEmpty
                              ? '${c['name_ur']}'
                              : '${c['name_en'] ?? ''}')
                          : ('${c['name_en'] ?? ''}'.trim().isNotEmpty
                              ? '${c['name_en']}'
                              : '${c['name_ur'] ?? ''}');
                      return DropdownMenuItem(value: id, child: Text(label));
                    }).toList(),
                    onChanged: (v) => setState(() => _categoryId = v),
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _nameEn,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Business name (English)', ur: 'نام (انگریزی)'),
                    ),
                    validator: (v) {
                      if (v == null || v.trim().length < 2) {
                        return app.t(en: 'Name is required', ur: 'نام درکار ہے');
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _nameUr,
                    textDirection: TextDirection.rtl,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Business name (Urdu)', ur: 'نام (اردو)'),
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _phone,
                    keyboardType: TextInputType.phone,
                    inputFormatters: [FilteringTextInputFormatter.allow(RegExp(r'[0-9+\-\s]'))],
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Phone', ur: 'فون'),
                      hintText: '03XXXXXXXXX',
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _whatsapp,
                    keyboardType: TextInputType.phone,
                    inputFormatters: [FilteringTextInputFormatter.allow(RegExp(r'[0-9+\-\s]'))],
                    decoration: InputDecoration(
                      labelText: app.t(en: 'WhatsApp', ur: 'واٹس ایپ'),
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _addressEn,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Address (English)', ur: 'پتہ (انگریزی)'),
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _addressUr,
                    textDirection: TextDirection.rtl,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Address (Urdu)', ur: 'پتہ (اردو)'),
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _descEn,
                    maxLines: 3,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Description (English)', ur: 'تفصیل (انگریزی)'),
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _descUr,
                    maxLines: 3,
                    textDirection: TextDirection.rtl,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Description (Urdu)', ur: 'تفصیل (اردو)'),
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _email,
                    keyboardType: TextInputType.emailAddress,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Email', ur: 'ای میل'),
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _website,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Website', ur: 'ویب سائٹ'),
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _hours,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Opening hours', ur: 'اوقاتِ کار'),
                      hintText: '9am – 9pm',
                    ),
                  ),
                  const SizedBox(height: 24),
                  FilledButton(
                    onPressed: _saving ? null : _save,
                    child: _saving
                        ? const SizedBox(
                            width: 22,
                            height: 22,
                            child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                          )
                        : Text(
                            _isEdit
                                ? app.t(en: 'Save changes', ur: 'تبدیلیاں محفوظ کریں')
                                : app.t(en: 'Submit for review', ur: 'جائزے کے لیے بھیجیں'),
                          ),
                  ),
                ],
              ),
            ),
    );
  }
}
