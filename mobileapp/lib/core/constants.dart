import 'package:flutter/foundation.dart';

class AppConstants {
  /// Set to true to force live API even in debug builds.
  /// Release builds always use production.
  static const bool forceProductionApi = true;

  static bool get useProductionApi => forceProductionApi || kReleaseMode;

  /// Live site API (no trailing slash).
  /// Requires StackCP document root = `/public` so routes are /api/...
  static const String productionApiBaseUrl = 'https://kotsultan.com/api';

  /// Local WAMP CodeIgniter public API (no trailing slash).
  static String get localApiBaseUrl {
    if (kIsWeb) {
      return 'http://localhost/kts/public/api';
    }
    switch (defaultTargetPlatform) {
      case TargetPlatform.android:
        return 'http://10.0.2.2/kts/public/api';
      default:
        return 'http://localhost/kts/public/api';
    }
  }

  static String get apiBaseUrl =>
      useProductionApi ? productionApiBaseUrl : localApiBaseUrl;

  /// Host used when rewriting local media URLs for emulators.
  static String get mediaHost {
    if (useProductionApi) return 'https://kotsultan.com/';
    if (kIsWeb) return 'http://localhost/';
    switch (defaultTargetPlatform) {
      case TargetPlatform.android:
        return 'http://10.0.2.2/';
      default:
        return 'http://localhost/';
    }
  }

  static const String brandEn = 'KotSultan.com';
  static const String brandUr = 'کوٹ سلطان ڈاٹ کام';
  static const String taglineEn = 'Local Community Directory';
  static const String taglineUr = 'مقامی کمیونٹی ڈائریکٹری';

  static const String prefToken = 'api_token';
  static const String prefName = 'user_name';
  static const String prefPhone = 'user_phone';
  static const String prefLocale = 'user_locale';
  static const String prefTheme = 'user_theme';
  static const String prefOnboarded = 'onboarded';
  static const String prefPendingUserSync = 'pending_user_sync';
  static const String prefUserId = 'user_id';
  static const String prefAccountType = 'account_type';
}
