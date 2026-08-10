import 'dart:convert';

import 'package:http/http.dart' as http;

import '../constants.dart';

class ApiException implements Exception {
  ApiException(this.message, {this.statusCode});
  final String message;
  final int? statusCode;

  @override
  String toString() => message;
}

class ApiClient {
  ApiClient({http.Client? client, String? baseUrl})
      : _client = client ?? http.Client(),
        baseUrl = baseUrl ?? AppConstants.apiBaseUrl;

  final http.Client _client;
  final String baseUrl;
  String? token;
  String locale = 'en';

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'X-App-Locale': locale,
        if (token != null && token!.isNotEmpty) 'Authorization': 'Bearer $token',
      };

  Map<String, String> get _jsonHeaders => {
        ..._headers,
        'Content-Type': 'application/json',
      };

  Uri _uri(String path, [Map<String, String>? query]) {
    final normalized = path.startsWith('/') ? path.substring(1) : path;
    return Uri.parse('$baseUrl/$normalized').replace(
      queryParameters: {
        'lang': locale,
        ...?query,
      },
    );
  }

  Future<Map<String, dynamic>> _send(Future<http.Response> Function() call) async {
    try {
      return _decode(await call());
    } on http.ClientException catch (e) {
      throw ApiException(
        'Cannot reach server (${e.uri ?? baseUrl}). Is WAMP running?',
      );
    } catch (e) {
      if (e is ApiException) rethrow;
      throw ApiException(e.toString());
    }
  }

  Future<Map<String, dynamic>> get(
    String path, {
    Map<String, String>? query,
  }) {
    return _send(() => _client.get(_uri(path, query), headers: _headers));
  }

  Future<Map<String, dynamic>> post(
    String path, {
    Map<String, dynamic>? body,
  }) {
    return _send(
      () => _client.post(
        _uri(path),
        headers: _jsonHeaders,
        body: jsonEncode(body ?? {}),
      ),
    );
  }

  Future<Map<String, dynamic>> put(
    String path, {
    Map<String, dynamic>? body,
  }) {
    return _send(
      () => _client.put(
        _uri(path),
        headers: _jsonHeaders,
        body: jsonEncode(body ?? {}),
      ),
    );
  }

  Map<String, dynamic> _decode(http.Response res) {
    Map<String, dynamic> json;
    try {
      json = jsonDecode(res.body) as Map<String, dynamic>;
    } catch (_) {
      throw ApiException('Invalid server response', statusCode: res.statusCode);
    }
    if (res.statusCode >= 400 || json['success'] == false) {
      throw ApiException(
        (json['message'] as String?) ?? 'Request failed',
        statusCode: res.statusCode,
      );
    }
    return json;
  }
}
