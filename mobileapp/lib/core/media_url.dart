import 'constants.dart';

/// Normalize media URLs for the current environment.
String mediaUrl(String? url) {
  if (url == null || url.isEmpty) return '';

  var out = url.trim();

  // Upgrade accidental http:// live links.
  out = out.replaceFirst('http://kotsultan.com/', 'https://kotsultan.com/');
  out = out.replaceFirst('http://www.kotsultan.com/', 'https://kotsultan.com/');

  if (AppConstants.useProductionApi) {
    // Map old local paths onto the live host.
    out = out
        .replaceFirst('http://localhost/kts/public/', 'https://kotsultan.com/')
        .replaceFirst('http://127.0.0.1/kts/public/', 'https://kotsultan.com/')
        .replaceFirst('http://10.0.2.2/kts/public/', 'https://kotsultan.com/')
        .replaceFirst('http://localhost/', 'https://kotsultan.com/')
        .replaceFirst('http://127.0.0.1/', 'https://kotsultan.com/')
        .replaceFirst('http://10.0.2.2/', 'https://kotsultan.com/');
    return out;
  }

  final host = AppConstants.mediaHost;
  return out
      .replaceFirst('http://localhost/', host)
      .replaceFirst('http://127.0.0.1/', host)
      .replaceFirst('http://10.0.2.2/', host);
}
