import 'dart:io';

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:http/http.dart' as http;
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';

import '../../core/api/api_client.dart';
import '../../core/media_url.dart';
import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../../core/widgets/app_network_image.dart';

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

  List<Map<String, dynamic>> _categories = [];
  List<Map<String, dynamic>> _areas = [];
  List<Map<String, dynamic>> _villages = [];

  int? _categoryId;
  int? _areaId;
  int? _villageId;
  String? _existingImageUrl;
  XFile? _pickedImage;

  bool _loading = true;
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
      _existingImageUrl = '${b['image'] ?? ''}'.trim().isEmpty ? null : '${b['image']}';
      _categoryId = _asInt(b['category_id']);
      _areaId = _asInt(b['area_id']);
      _villageId = _asInt(b['village_id']);
    }
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadOptions());
  }

  int? _asInt(dynamic v) {
    if (v == null || '$v'.trim().isEmpty) return null;
    if (v is int) return v;
    return int.tryParse('$v');
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
    super.dispose();
  }

  Future<void> _loadOptions() async {
    final app = context.read<AppState>();
    try {
      final res = await app.api.get('my-businesses/form-options');
      final data = res['data'] as Map<String, dynamic>? ?? {};
      final cats = ((data['categories'] as List?) ?? const [])
          .map((e) => Map<String, dynamic>.from(e as Map))
          .toList();
      final areas = ((data['areas'] as List?) ?? const [])
          .map((e) => Map<String, dynamic>.from(e as Map))
          .toList();
      final villages = ((data['villages'] as List?) ?? const [])
          .map((e) => Map<String, dynamic>.from(e as Map))
          .toList();
      final accountPhone = '${data['account_phone'] ?? app.user?.phone ?? ''}'.trim();

      if (!mounted) return;
      setState(() {
        _categories = cats;
        _areas = areas;
        _villages = villages;
        if (_phone.text.trim().isEmpty || !_isEdit) {
          if (accountPhone.isNotEmpty) _phone.text = accountPhone;
        }
        if (_categoryId == null && cats.isNotEmpty) {
          _categoryId = _asInt(cats.first['id']);
        }
        if (_categoryId != null && !cats.any((c) => _asInt(c['id']) == _categoryId)) {
          _categoryId = cats.isNotEmpty ? _asInt(cats.first['id']) : null;
        }
        if (_areaId != null && !areas.any((a) => _asInt(a['id']) == _areaId)) {
          _areaId = null;
        }
        if (_villageId != null && !villages.any((v) => _asInt(v['id']) == _villageId)) {
          _villageId = null;
        }
        _loading = false;
      });
    } catch (e) {
      // Fallback: categories from catalog cache
      try {
        final catsRes = await app.catalog.getCategories();
        final cats = catsRes.data.map((e) => Map<String, dynamic>.from(e as Map)).toList();
        if (!mounted) return;
        setState(() {
          _categories = cats;
          if (_phone.text.trim().isEmpty) {
            _phone.text = app.user?.phone ?? '';
          }
          if (_categoryId == null && cats.isNotEmpty) {
            _categoryId = _asInt(cats.first['id']);
          }
          _loading = false;
        });
      } catch (_) {
        if (!mounted) return;
        setState(() => _loading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e is ApiException ? e.message : e.toString())),
        );
      }
    }
  }

  Future<void> _pickImage() async {
    final picker = ImagePicker();
    final file = await picker.pickImage(
      source: ImageSource.gallery,
      maxWidth: 1600,
      maxHeight: 1600,
      imageQuality: 85,
    );
    if (file == null || !mounted) return;
    setState(() => _pickedImage = file);
  }

  String _labelFor(Map<String, dynamic> row, bool isUrdu) {
    if (isUrdu) {
      final ur = '${row['name_ur'] ?? ''}'.trim();
      if (ur.isNotEmpty) return ur;
    }
    final en = '${row['name_en'] ?? row['name'] ?? ''}'.trim();
    if (en.isNotEmpty) return en;
    return '${row['name_ur'] ?? ''}';
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

    final fields = <String, String>{
      'name_en': _nameEn.text.trim(),
      'name_ur': _nameUr.text.trim(),
      'category_id': '$_categoryId',
      'address_en': _addressEn.text.trim(),
      'address_ur': _addressUr.text.trim(),
      'description_en': _descEn.text.trim(),
      'description_ur': _descUr.text.trim(),
      'phone': _phone.text.trim(),
      'whatsapp': _whatsapp.text.trim(),
      'email': _email.text.trim(),
      'website': _website.text.trim(),
      if (_areaId != null) 'area_id': '$_areaId',
      if (_villageId != null) 'village_id': '$_villageId',
    };

    try {
      final files = <http.MultipartFile>[];
      if (_pickedImage != null) {
        final bytes = await _pickedImage!.readAsBytes();
        final name = _pickedImage!.name.isNotEmpty ? _pickedImage!.name : 'business.jpg';
        files.add(http.MultipartFile.fromBytes('image', bytes, filename: name));
      }

      if (_isEdit) {
        final id = widget.business!['id'];
        if (files.isNotEmpty) {
          await app.api.postMultipart('my-businesses/$id', fields: fields, files: files);
        } else {
          await app.api.put('my-businesses/$id', body: {
            ...fields,
            'area_id': _areaId,
            'village_id': _villageId,
            'category_id': _categoryId,
          });
        }
      } else {
        if (files.isNotEmpty) {
          await app.api.postMultipart('my-businesses', fields: fields, files: files);
        } else {
          await app.api.post('my-businesses', body: {
            ...fields,
            'area_id': _areaId,
            'village_id': _villageId,
            'category_id': _categoryId,
          });
        }
      }

      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            app.t(
              en: _isEdit ? 'Business updated' : 'Submitted for admin review',
              ur: _isEdit ? 'کاروبار اپڈیٹ ہو گیا' : 'ایڈمن منظوری کے لیے بھیج دیا گیا',
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

  Widget _sectionTitle(String text) {
    return Padding(
      padding: const EdgeInsets.only(top: 8, bottom: 10),
      child: Text(
        text,
        style: const TextStyle(
          fontWeight: FontWeight.w800,
          fontSize: 13,
          color: AppColors.emerald,
          letterSpacing: 0.3,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final isUrdu = app.isUrdu;

    return Scaffold(
      appBar: AppBar(
        title: Text(
          _isEdit
              ? app.t(en: 'Edit business', ur: 'کاروبار ترمیم')
              : app.t(en: 'Add business', ur: 'کاروبار شامل کریں'),
        ),
      ),
      body: _loading
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

                  _sectionTitle(app.t(en: 'CATEGORY & LOCATION', ur: 'زمرہ اور مقام')),
                  DropdownButtonFormField<int>(
                    value: _categoryId,
                    isExpanded: true,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Category', ur: 'زمرہ'),
                    ),
                    items: _categories.map((c) {
                      final id = _asInt(c['id']) ?? 0;
                      return DropdownMenuItem(value: id, child: Text(_labelFor(c, isUrdu), overflow: TextOverflow.ellipsis));
                    }).toList(),
                    onChanged: (v) => setState(() => _categoryId = v),
                  ),
                  const SizedBox(height: 12),
                  DropdownButtonFormField<int?>(
                    value: _areaId,
                    isExpanded: true,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Area (optional)', ur: 'علاقہ (اختیاری)'),
                    ),
                    items: [
                      DropdownMenuItem<int?>(
                        value: null,
                        child: Text(app.t(en: 'Select area', ur: 'علاقہ منتخب کریں')),
                      ),
                      ..._areas.map((a) {
                        final id = _asInt(a['id']);
                        return DropdownMenuItem<int?>(value: id, child: Text(_labelFor(a, isUrdu), overflow: TextOverflow.ellipsis));
                      }),
                    ],
                    onChanged: (v) => setState(() => _areaId = v),
                  ),
                  const SizedBox(height: 12),
                  DropdownButtonFormField<int?>(
                    value: _villageId,
                    isExpanded: true,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Village (optional)', ur: 'گاؤں (اختیاری)'),
                    ),
                    items: [
                      DropdownMenuItem<int?>(
                        value: null,
                        child: Text(app.t(en: 'Select village', ur: 'گاؤں منتخب کریں')),
                      ),
                      ..._villages.map((v) {
                        final id = _asInt(v['id']);
                        return DropdownMenuItem<int?>(value: id, child: Text(_labelFor(v, isUrdu), overflow: TextOverflow.ellipsis));
                      }),
                    ],
                    onChanged: (v) => setState(() => _villageId = v),
                  ),

                  _sectionTitle(app.t(en: 'ENGLISH DETAILS', ur: 'انگریزی تفصیلات')),
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
                    controller: _addressEn,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Address (English)', ur: 'پتہ (انگریزی)'),
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

                  _sectionTitle(app.t(en: 'URDU DETAILS', ur: 'اردو تفصیلات')),
                  TextFormField(
                    controller: _nameUr,
                    textDirection: TextDirection.rtl,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Business name (Urdu)', ur: 'نام (اردو)'),
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
                    controller: _descUr,
                    maxLines: 3,
                    textDirection: TextDirection.rtl,
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Description (Urdu)', ur: 'تفصیل (اردو)'),
                    ),
                  ),

                  _sectionTitle(app.t(en: 'CONTACT & PHOTO', ur: 'رابطہ اور تصویر')),
                  TextFormField(
                    controller: _phone,
                    enabled: false,
                    keyboardType: TextInputType.phone,
                    inputFormatters: [FilteringTextInputFormatter.allow(RegExp(r'[0-9+\-\s]'))],
                    decoration: InputDecoration(
                      labelText: app.t(en: 'Phone number', ur: 'فون نمبر'),
                      helperText: app.t(
                        en: 'Registered on your account contact number',
                        ur: 'آپ کے اکاؤنٹ کے رابطہ نمبر پر رجسٹر',
                      ),
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
                  const SizedBox(height: 16),
                  Text(
                    app.t(en: 'Business image', ur: 'کاروباری تصویر'),
                    style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      ClipRRect(
                        borderRadius: BorderRadius.circular(12),
                        child: SizedBox(
                          width: 72,
                          height: 72,
                          child: _pickedImage != null
                              ? (kIsWeb
                                  ? Image.network(_pickedImage!.path, fit: BoxFit.cover)
                                  : Image.file(File(_pickedImage!.path), fit: BoxFit.cover))
                              : (_existingImageUrl != null
                                  ? AppNetworkImage(
                                      url: mediaUrl(_existingImageUrl),
                                      fit: BoxFit.cover,
                                      placeholderIcon: Icons.storefront_rounded,
                                    )
                                  : Container(
                                      color: AppColors.tealSoft,
                                      child: const Icon(Icons.storefront_rounded, color: AppColors.emerald),
                                    )),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: _pickImage,
                          icon: const Icon(Icons.photo_library_outlined),
                          label: Text(
                            app.t(en: 'Choose image', ur: 'تصویر منتخب کریں'),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Text(
                    app.t(en: 'Optional. JPG/PNG recommended.', ur: 'اختیاری۔ JPG/PNG بہتر ہے۔'),
                    style: TextStyle(fontSize: 11, color: Colors.grey.shade600),
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
