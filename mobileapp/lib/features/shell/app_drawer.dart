import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../core/state/app_state.dart';
import '../../core/theme/app_theme.dart';
import '../more/more_screen.dart';
import '../profile/profile_screen.dart';

class ShellScope extends InheritedWidget {
  const ShellScope({
    super.key,
    required this.openDrawer,
    required this.goToTab,
    required super.child,
  });

  final VoidCallback openDrawer;
  final ValueChanged<int> goToTab;

  static ShellScope of(BuildContext context) {
    final scope = context.dependOnInheritedWidgetOfExactType<ShellScope>();
    assert(scope != null, 'ShellScope not found');
    return scope!;
  }

  static ShellScope? maybeOf(BuildContext context) {
    return context.dependOnInheritedWidgetOfExactType<ShellScope>();
  }

  @override
  bool updateShouldNotify(ShellScope oldWidget) => false;
}

class AppDrawer extends StatelessWidget {
  const AppDrawer({
    super.key,
    required this.selectedIndex,
    required this.onSelectTab,
  });

  final int selectedIndex;
  final ValueChanged<int> onSelectTab;

  void _goTab(BuildContext context, int index) {
    Navigator.of(context).pop();
    onSelectTab(index);
  }

  void _push(BuildContext context, Widget page) {
    Navigator.of(context).pop();
    Navigator.of(context).push(MaterialPageRoute(builder: (_) => page));
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final user = app.user;
    final isRtl = app.isRtl;

    return Drawer(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      child: SafeArea(
        child: Column(
          children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.fromLTRB(20, 20, 20, 22),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [Color(0xFF047857), Color(0xFF0D9488)],
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        width: 48,
                        height: 48,
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.18),
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: const Icon(Icons.location_on_rounded, color: Colors.white),
                      ),
                      const Spacer(),
                      IconButton(
                        onPressed: () => Navigator.of(context).pop(),
                        icon: const Icon(Icons.close_rounded, color: Colors.white),
                      ),
                    ],
                  ),
                  const SizedBox(height: 14),
                  Text(
                    app.t(en: 'KotSultan.com', ur: 'کوٹ سلطان ڈاٹ کام'),
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 20),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    user?.name.isNotEmpty == true
                        ? user!.name
                        : app.t(en: 'Local community directory', ur: 'مقامی کمیونٹی ڈائریکٹری'),
                    style: TextStyle(color: Colors.white.withValues(alpha: 0.9), fontSize: 13),
                  ),
                  if (user?.phone.isNotEmpty == true) ...[
                    const SizedBox(height: 2),
                    Text(
                      user!.phone,
                      style: TextStyle(color: Colors.white.withValues(alpha: 0.75), fontSize: 12),
                      textDirection: TextDirection.ltr,
                    ),
                  ],
                ],
              ),
            ),
            Expanded(
              child: ListView(
                padding: const EdgeInsets.symmetric(vertical: 10),
                children: [
                  _DrawerLabel(text: app.t(en: 'Browse', ur: 'براؤز')),
                  _DrawerTile(
                    selected: selectedIndex == 0,
                    icon: Icons.home_rounded,
                    label: app.t(en: 'Home', ur: 'ہوم'),
                    onTap: () => _goTab(context, 0),
                  ),
                  _DrawerTile(
                    selected: selectedIndex == 1,
                    icon: Icons.storefront_rounded,
                    label: app.t(en: 'Directory', ur: 'ڈائریکٹری'),
                    onTap: () => _goTab(context, 1),
                  ),
                  _DrawerTile(
                    selected: selectedIndex == 2,
                    icon: Icons.emergency_rounded,
                    label: app.t(en: 'Emergency', ur: 'ایمرجنسی'),
                    onTap: () => _goTab(context, 2),
                  ),
                  _DrawerTile(
                    selected: selectedIndex == 3,
                    icon: Icons.people_alt_rounded,
                    label: app.t(en: 'Wall of Kot Sultan', ur: 'وال آف کوٹ سلطان'),
                    onTap: () => _goTab(context, 3),
                  ),
                  _DrawerTile(
                    selected: selectedIndex == 4,
                    icon: Icons.grid_view_rounded,
                    label: app.t(en: 'More', ur: 'مزید'),
                    onTap: () => _goTab(context, 4),
                  ),
                  const Padding(
                    padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    child: Divider(height: 1),
                  ),
                  _DrawerLabel(text: app.t(en: 'Pages', ur: 'صفحات')),
                  _DrawerTile(
                    icon: Icons.person_rounded,
                    label: app.t(en: 'Profile', ur: 'پروفائل'),
                    onTap: () => _push(context, const ProfileScreen()),
                  ),
                  _DrawerTile(
                    icon: Icons.info_rounded,
                    label: app.t(en: 'About', ur: 'ہمارے بارے میں'),
                    onTap: () => _push(context, const AboutPage()),
                  ),
                  _DrawerTile(
                    icon: Icons.mail_rounded,
                    label: app.t(en: 'Contact', ur: 'رابطہ'),
                    onTap: () => _push(context, const ContactPage()),
                  ),
                  _DrawerTile(
                    icon: Icons.volunteer_activism_rounded,
                    label: app.t(en: 'Volunteer', ur: 'رضاکار'),
                    onTap: () => _push(context, const VolunteerPage()),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(12, 0, 12, 12),
              child: ListTile(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                leading: Icon(isRtl ? Icons.logout : Icons.logout_rounded, color: AppColors.rose),
                title: Text(
                  app.t(en: 'Logout', ur: 'لاگ آؤٹ'),
                  style: const TextStyle(color: AppColors.rose, fontWeight: FontWeight.w700),
                ),
                onTap: () async {
                  Navigator.of(context).pop();
                  await app.logout();
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _DrawerLabel extends StatelessWidget {
  const _DrawerLabel({required this.text});
  final String text;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 10, 20, 4),
      child: Text(
        text,
        style: TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.w700,
          color: Colors.grey.shade600,
          letterSpacing: 0.3,
        ),
      ),
    );
  }
}

class _DrawerTile extends StatelessWidget {
  const _DrawerTile({
    required this.icon,
    required this.label,
    required this.onTap,
    this.selected = false,
  });

  final IconData icon;
  final String label;
  final VoidCallback onTap;
  final bool selected;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 2),
      child: ListTile(
        selected: selected,
        selectedTileColor: AppColors.tealSoft,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        leading: Icon(icon, color: selected ? AppColors.emeraldDark : AppColors.slate700),
        title: Text(
          label,
          style: TextStyle(
            fontWeight: selected ? FontWeight.w800 : FontWeight.w600,
            color: selected ? AppColors.emeraldDark : AppColors.slate900,
          ),
        ),
        onTap: onTap,
      ),
    );
  }
}

/// Menu button that opens the shell drawer.
class DrawerMenuButton extends StatelessWidget {
  const DrawerMenuButton({super.key, this.color});

  final Color? color;

  @override
  Widget build(BuildContext context) {
    return IconButton(
      tooltip: 'Menu',
      onPressed: () => ShellScope.maybeOf(context)?.openDrawer(),
      icon: Icon(Icons.menu_rounded, color: color ?? AppColors.slate900),
    );
  }
}
