import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'core/api/api_client.dart';
import 'core/state/app_state.dart';
import 'core/theme/app_theme.dart';
import 'features/onboarding/onboarding_screen.dart';
import 'features/shell/main_shell.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const KotSultanApp());
}

class KotSultanApp extends StatelessWidget {
  const KotSultanApp({super.key});

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider(
      create: (_) => AppState(ApiClient())..bootstrap(),
      child: Consumer<AppState>(
        builder: (context, app, _) {
          if (!app.ready) {
            return MaterialApp(
              debugShowCheckedModeBanner: false,
              theme: buildLightTheme(),
              home: const Scaffold(body: Center(child: CircularProgressIndicator())),
            );
          }

          return MaterialApp(
            debugShowCheckedModeBanner: false,
            title: 'KotSultan.com',
            theme: buildLightTheme(),
            darkTheme: buildDarkTheme(),
            themeMode: app.themeMode,
            builder: (context, child) {
              return Directionality(
                textDirection: app.isRtl ? TextDirection.rtl : TextDirection.ltr,
                child: child ?? const SizedBox.shrink(),
              );
            },
            home: app.onboarded ? const MainShell() : const OnboardingScreen(),
          );
        },
      ),
    );
  }
}
