import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/state/app_state.dart';
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

class _MainShellState extends State<MainShell> {
  final _scaffoldKey = GlobalKey<ScaffoldState>();
  int _index = 0;

  void _openDrawer() => _scaffoldKey.currentState?.openDrawer();

  void _goToTab(int index) => setState(() => _index = index);

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final pages = const [
      HomeScreen(),
      DirectoryScreen(),
      EmergencyScreen(),
      WallScreen(),
      MoreScreen(),
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
        body: IndexedStack(index: _index, children: pages),
        bottomNavigationBar: NavigationBar(
          selectedIndex: _index,
          onDestinationSelected: _goToTab,
          destinations: [
            NavigationDestination(
              icon: const Icon(Icons.home_outlined),
              selectedIcon: const Icon(Icons.home_rounded),
              label: app.t(en: 'Home', ur: 'ہوم'),
            ),
            NavigationDestination(
              icon: const Icon(Icons.storefront_outlined),
              selectedIcon: const Icon(Icons.storefront_rounded),
              label: app.t(en: 'Directory', ur: 'ڈائریکٹری'),
            ),
            NavigationDestination(
              icon: const Icon(Icons.emergency_outlined),
              selectedIcon: const Icon(Icons.emergency_rounded),
              label: app.t(en: 'Emergency', ur: 'ایمرجنسی'),
            ),
            NavigationDestination(
              icon: const Icon(Icons.people_outline),
              selectedIcon: const Icon(Icons.people_alt_rounded),
              label: app.t(en: 'Wall', ur: 'وال'),
            ),
            NavigationDestination(
              icon: const Icon(Icons.menu_rounded),
              selectedIcon: const Icon(Icons.menu_open_rounded),
              label: app.t(en: 'More', ur: 'مزید'),
            ),
          ],
        ),
      ),
    );
  }
}
