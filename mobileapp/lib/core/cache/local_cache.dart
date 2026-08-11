import 'dart:convert';
import 'dart:io';

import 'package:path_provider/path_provider.dart';

/// Simple JSON file cache for offline directory data.
class LocalCache {
  Directory? _dir;

  Future<Directory> _ensureDir() async {
    if (_dir != null) return _dir!;
    final root = await getApplicationSupportDirectory();
    _dir = Directory('${root.path}${Platform.pathSeparator}offline_cache');
    if (!await _dir!.exists()) {
      await _dir!.create(recursive: true);
    }
    return _dir!;
  }

  Future<File> _file(String key) async {
    final safe = key.replaceAll(RegExp(r'[^a-zA-Z0-9_\-]'), '_');
    final dir = await _ensureDir();
    return File('${dir.path}${Platform.pathSeparator}$safe.json');
  }

  Future<void> putJson(String key, Object value) async {
    final file = await _file(key);
    await file.writeAsString(jsonEncode(value), flush: true);
  }

  Future<Object?> getJson(String key) async {
    final file = await _file(key);
    if (!await file.exists()) return null;
    try {
      return jsonDecode(await file.readAsString());
    } catch (_) {
      return null;
    }
  }

  Future<Map<String, dynamic>?> getMap(String key) async {
    final v = await getJson(key);
    if (v is Map<String, dynamic>) return v;
    if (v is Map) return Map<String, dynamic>.from(v);
    return null;
  }

  Future<List<dynamic>?> getList(String key) async {
    final v = await getJson(key);
    if (v is List) return List<dynamic>.from(v);
    return null;
  }

  Future<bool> has(String key) async {
    return (await _file(key)).exists();
  }
}
