import 'dart:convert';
import 'dart:io';

import 'package:flutter/services.dart';
import 'package:path_provider/path_provider.dart';

/// Simple JSON file cache for offline directory data.
///
/// Falls back to an in-memory map when native path_provider is unavailable
/// (e.g. MissingPluginException before a full app rebuild).
class LocalCache {
  Directory? _dir;
  bool _diskUnavailable = false;
  final Map<String, Object> _memory = {};

  Future<Directory?> _ensureDir() async {
    if (_diskUnavailable) return null;
    if (_dir != null) return _dir!;
    try {
      final root = await getApplicationSupportDirectory();
      _dir = Directory('${root.path}${Platform.pathSeparator}offline_cache');
      if (!await _dir!.exists()) {
        await _dir!.create(recursive: true);
      }
      return _dir!;
    } on MissingPluginException {
      _diskUnavailable = true;
      return null;
    } catch (_) {
      _diskUnavailable = true;
      return null;
    }
  }

  Future<File?> _file(String key) async {
    final dir = await _ensureDir();
    if (dir == null) return null;
    final safe = key.replaceAll(RegExp(r'[^a-zA-Z0-9_\-]'), '_');
    return File('${dir.path}${Platform.pathSeparator}$safe.json');
  }

  Future<void> putJson(String key, Object value) async {
    _memory[key] = value;
    final file = await _file(key);
    if (file == null) return;
    try {
      await file.writeAsString(jsonEncode(value), flush: true);
    } catch (_) {
      // Keep memory cache; ignore disk write failures.
    }
  }

  Future<Object?> getJson(String key) async {
    if (_memory.containsKey(key)) return _memory[key];
    final file = await _file(key);
    if (file == null || !await file.exists()) return null;
    try {
      final decoded = jsonDecode(await file.readAsString());
      if (decoded is Object) _memory[key] = decoded;
      return decoded;
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
    if (_memory.containsKey(key)) return true;
    final file = await _file(key);
    if (file == null) return false;
    try {
      return await file.exists();
    } catch (_) {
      return false;
    }
  }
}
