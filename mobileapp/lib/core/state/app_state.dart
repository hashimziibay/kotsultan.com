import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../api/api_client.dart';
import '../constants.dart';

class AppUser {
  AppUser({
    required this.id,
    required this.name,
    required this.phone,
    required this.locale,
    required this.theme,
  });

  factory AppUser.fromJson(Map<String, dynamic> json) {
    return AppUser(
      id: json['id'] is int ? json['id'] as int : int.tryParse('${json['id']}') ?? 0,
      name: '${json['name'] ?? ''}',
      phone: '${json['phone'] ?? ''}',
      locale: '${json['locale'] ?? 'en'}',
      theme: '${json['theme'] ?? 'light'}',
    );
  }

  final int id;
  final String name;
  final String phone;
  final String locale;
  final String theme;

  AppUser copyWith({
    String? name,
    String? phone,
    String? locale,
    String? theme,
  }) {
    return AppUser(
      id: id,
      name: name ?? this.name,
      phone: phone ?? this.phone,
      locale: locale ?? this.locale,
      theme: theme ?? this.theme,
    );
  }
}

class AppState extends ChangeNotifier {
  AppState(this.api);

  final ApiClient api;

  bool ready = false;
  bool onboarded = false;
  AppUser? user;
  String locale = 'en';
  ThemeMode themeMode = ThemeMode.light;
  String? error;
  /// One-shot query from Home search → Directory tab.
  String? pendingDirectoryQuery;

  bool get isUrdu => locale == 'ur';
  bool get isRtl => isUrdu;

  Future<void> bootstrap() async {
    final prefs = await SharedPreferences.getInstance();
    onboarded = prefs.getBool(AppConstants.prefOnboarded) ?? false;
    locale = prefs.getString(AppConstants.prefLocale) ?? 'en';
    final theme = prefs.getString(AppConstants.prefTheme) ?? 'light';
    themeMode = theme == 'dark' ? ThemeMode.dark : ThemeMode.light;
    api.locale = locale;
    api.token = prefs.getString(AppConstants.prefToken);

    final name = prefs.getString(AppConstants.prefName);
    final phone = prefs.getString(AppConstants.prefPhone);
    if (onboarded && name != null && phone != null) {
      user = AppUser(
        id: 0,
        name: name,
        phone: phone,
        locale: locale,
        theme: theme,
      );
    }

    if (api.token != null && api.token!.isNotEmpty) {
      try {
        final res = await api.get('auth/me');
        final data = res['data'] as Map<String, dynamic>;
        user = AppUser.fromJson(data);
        await _persistUser(user!);
      } catch (_) {
        // Keep local session; token may refresh on next register/login.
      }
    }

    ready = true;
    notifyListeners();
  }

  Future<void> completeOnboarding({
    required String name,
    required String phone,
    required String localeCode,
    required String theme,
  }) async {
    error = null;
    notifyListeners();
    try {
      final res = await api.post('auth/register', body: {
        'name': name,
        'phone': phone,
        'locale': localeCode,
        'theme': theme,
      });
      final data = res['data'] as Map<String, dynamic>;
      api.token = data['token'] as String?;
      user = AppUser.fromJson(data['user'] as Map<String, dynamic>);
      locale = user!.locale;
      api.locale = locale;
      themeMode = user!.theme == 'dark' ? ThemeMode.dark : ThemeMode.light;
      onboarded = true;

      final prefs = await SharedPreferences.getInstance();
      await prefs.setBool(AppConstants.prefOnboarded, true);
      await prefs.setString(AppConstants.prefToken, api.token ?? '');
      await _persistUser(user!);
      notifyListeners();
    } catch (e) {
      error = e.toString();
      notifyListeners();
      rethrow;
    }
  }

  Future<void> updateProfile({
    String? name,
    String? phone,
    String? localeCode,
    String? theme,
  }) async {
    error = null;
    notifyListeners();
    final body = <String, dynamic>{};
    if (name != null) body['name'] = name;
    if (phone != null) body['phone'] = phone;
    if (localeCode != null) body['locale'] = localeCode;
    if (theme != null) body['theme'] = theme;

    try {
      final res = await api.put('auth/me', body: body);
      final data = res['data'] as Map<String, dynamic>;
      user = AppUser.fromJson(data);
      locale = user!.locale;
      api.locale = locale;
      themeMode = user!.theme == 'dark' ? ThemeMode.dark : ThemeMode.light;
      await _persistUser(user!);
      notifyListeners();
    } catch (e) {
      error = e.toString();
      notifyListeners();
      rethrow;
    }
  }

  Future<void> setLocaleLocal(String code) async {
    locale = code;
    api.locale = code;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.prefLocale, code);
    notifyListeners();
  }

  Future<void> setThemeLocal(String theme) async {
    themeMode = theme == 'dark' ? ThemeMode.dark : ThemeMode.light;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.prefTheme, theme);
    notifyListeners();
  }

  Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
    api.token = null;
    user = null;
    onboarded = false;
    locale = 'en';
    themeMode = ThemeMode.light;
    notifyListeners();
  }

  Future<void> _persistUser(AppUser u) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.prefName, u.name);
    await prefs.setString(AppConstants.prefPhone, u.phone);
    await prefs.setString(AppConstants.prefLocale, u.locale);
    await prefs.setString(AppConstants.prefTheme, u.theme);
  }

  String t({required String en, required String ur}) => isUrdu ? ur : en;

  void setPendingDirectoryQuery(String query) {
    pendingDirectoryQuery = query;
    notifyListeners();
  }

  String? takePendingDirectoryQuery() {
    final q = pendingDirectoryQuery;
    pendingDirectoryQuery = null;
    return q;
  }
}
