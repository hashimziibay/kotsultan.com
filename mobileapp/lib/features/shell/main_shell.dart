import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../directory/directory_screen.dart';
import '../emergency/emergency_screen.dart';
import '../home/home_screen.dart';
import '../more/more_screen.dart';
import '../wall/wall_screen.dart';
import 'app_drawer.dart';

class MainShell extends StatefulWidget {
  const MainShell({super.key});

  @override
  State<MainShell> createState() => _MainShellState();
}

class _MainShellState extends State<MainShell> with WidgetsBindingObserver {
  final _scaffoldKey = GlobalKey<ScaffoldState>();
  int _index = 0;

  void _openDrawer() => _scaffoldKey.currentState?.openDrawer();

  void _goToTab(int index) => setState(() => _index = index);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      final app = context.read<AppState>();
      if (app.isOnline) {
        // ignore: unawaited_futures
        app.onBackOnline();
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final pages = const [
      HomeScreen(),
      DirectoryScreen(),
      EmergencyScreen(),
      WallScreen(),
      MoreScreen(),
    ];

    final tabs = [
      _NavTab(
        icon: Icons.home_outlined,
        selectedIcon: Icons.home_rounded,
        label: app.t(en: 'Home', ur: 'ہوم'),
      ),
      _NavTab(
        icon: Icons.storefront_outlined,
        selectedIcon: Icons.storefront_rounded,
        label: app.t(en: 'Directory', ur: 'ڈائریکٹری'),
      ),
      _NavTab(
        icon: Icons.emergency_outlined,
        selectedIcon: Icons.emergency_rounded,
        label: app.t(en: 'Emergency', ur: 'ایمرجنسی'),
      ),
      _NavTab(
        icon: Icons.people_outline,
        selectedIcon: Icons.people_alt_rounded,
        label: app.t(en: 'Wall of Kot Sultan', ur: 'وال آف کوٹ سلطان'),
      ),
      _NavTab(
        icon: Icons.menu_rounded,
        selectedIcon: Icons.menu_open_rounded,
        label: app.t(en: 'More', ur: 'مزید'),
      ),
    ];

    return ShellScope(
      openDrawer: _openDrawer,
      goToTab: _goToTab,
      child: Scaffold(
        key: _scaffoldKey,
        drawer: AppDrawer(
          selectedIndex: _index,
          onSelectTab: _goToTab,
        ),
        body: Column(
          children: [
            if (app.cacheRefreshing)
              const LinearProgressIndicator(
                minHeight: 2,
                color: Color(0xFF059669),
                backgroundColor: Color(0x33059669),
              ),
            Expanded(
              child: IndexedStack(index: _index, children: pages),
            ),
          ],
        ),
        bottomNavigationBar: Material(
          color: isDark ? AppColors.slate800 : AppColors.slate50,
          elevation: 8,
          child: SafeArea(
            top: false,
            child: Container(
              height: 68,
              decoration: BoxDecoration(
                color: isDark ? AppColors.slate800 : AppColors.slate50,
                border: Border(
                  top: BorderSide(
                    color: isDark ? AppColors.slate700 : AppColors.slate200,
                  ),
                ),
              ),
              child: Row(
                children: List.generate(tabs.length, (i) {
                  final tab = tabs[i];
                  final selected = _index == i;
                  final color = selected
                      ? (isDark ? AppColors.emeraldLight : AppColors.emeraldDark)
                      : (isDark ? Colors.white70 : AppColors.slate500);
                  return Expanded(
                    child: InkWell(
                      onTap: () => _goToTab(i),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          AnimatedContainer(
                            duration: const Duration(milliseconds: 180),
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                            decoration: BoxDecoration(
                              color: selected
                                  ? (isDark
                                      ? AppColors.emerald.withValues(alpha: 0.25)
                                      : AppColors.tealSoft)
                                  : Colors.transparent,
                              borderRadius: BorderRadius.circular(14),
                            ),
                            child: Icon(
                              selected ? tab.selectedIcon : tab.icon,
                              color: color,
                              size: 22,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            tab.label,
                            textAlign: TextAlign.center,
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(
                              color: color,
                              fontSize: 10,
                              height: 1.1,
                              fontWeight: selected ? FontWeight.w700 : FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                }),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _NavTab {
  const _NavTab({
    required this.icon,
    required this.selectedIcon,
    required this.label,
  });

  final IconData icon;
  final IconData selectedIcon;
  final String label;
}
