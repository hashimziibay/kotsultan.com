<?php 
    $currentLang = service('request')->getLocale();
    $isRtl = ($currentLang === 'ur');
    $adminUsername = session('admin_username') ?? 'Admin';
    $segments = service('request')->getUri()->getSegments();
    $currentSegment = $segments[1] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>" x-data="adminThemeHandler()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? lang('App.admin_page_dashboard_title')) ?> - <?= lang('App.admin_console_title') ?></title>
    
    <!-- Favicon (matches navbar brand map-pin icon) -->
    <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">
    <link rel="icon" href="<?= base_url('favicon-32.png') ?>" type="image/png" sizes="32x32">
    <link rel="icon" href="<?= base_url('favicon-16.png') ?>" type="image/png" sizes="16x16">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('apple-touch-icon.png') ?>" sizes="180x180">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <style>
      .wall-years-html p { margin: 0 0 0.35em; }
      .wall-years-html p:last-child { margin-bottom: 0; }
      .wall-years-html ul, .wall-years-html ol { margin: 0.25em 0 0.25em 1.1em; padding: 0; }
      .wall-years-html a { color: #059669; text-decoration: underline; }
      .wall-years-html h3, .wall-years-html h4 { font-weight: 800; margin: 0.2em 0; font-size: 1em; }
    </style>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Apply saved theme before first paint (single shared 'theme' key across site) -->
    <script>
        (function () {
            try {
                if (localStorage.getItem('theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col font-sans transition-colors duration-200 antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen">
        
        <!-- SIDEBAR NAVIGATION -->
        <aside class="fixed inset-y-0 left-0 rtl:left-auto rtl:right-0 z-50 w-64 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-r rtl:border-r-0 rtl:border-l border-slate-200 dark:border-slate-800 transform transition-transform duration-200 lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen flex flex-col justify-between shadow-xl"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full rtl:translate-x-full lg:translate-x-0 rtl:lg:translate-x-0'">
            
            <div>
                <!-- Brand Header -->
                <div class="h-16 px-6 bg-slate-50 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-slate-950 font-black text-lg shadow-sm">
                            K
                        </div>
                        <div>
                            <span class="font-extrabold text-sm text-slate-900 dark:text-white tracking-tight block"><?= lang('App.brand_name') ?></span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold tracking-wider rtl:tracking-normal uppercase block"><?= lang('App.admin_management_console') ?></span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="p-3 space-y-0.5 text-xs font-semibold overflow-y-auto max-h-[calc(100vh-8rem)] hide-scrollbar">
                    
                    <div class="px-3 py-1.5 text-[10px] uppercase font-bold text-slate-500 tracking-wider rtl:tracking-normal"><?= lang('App.admin_sec_main') ?></div>
                    
                    <a href="<?= base_url('admin/dashboard') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= in_array($currentSegment, ['', 'dashboard']) ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_dashboard') ?></span>
                    </a>

                    <div class="px-3 py-1.5 text-[10px] uppercase font-bold text-slate-500 tracking-wider rtl:tracking-normal pt-3"><?= lang('App.admin_sec_directory') ?></div>

                    <a href="<?= base_url('admin/businesses') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'businesses' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="store" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_businesses') ?></span>
                    </a>

                    <a href="<?= base_url('admin/app-users') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'app-users' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="smartphone" class="w-4 h-4"></i>
                        <span>App Users</span>
                    </a>

                    <a href="<?= base_url('admin/duplicates') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'duplicates' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="copy" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_duplicates') ?></span>
                    </a>

                    <a href="<?= base_url('admin/categories') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'categories' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="folder-tree" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_categories') ?></span>
                    </a>

                    <a href="<?= base_url('admin/areas') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'areas' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="map" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_areas') ?></span>
                    </a>

                    <a href="<?= base_url('admin/villages') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'villages' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_villages') ?></span>
                    </a>

                    <div class="px-3 py-1.5 text-[10px] uppercase font-bold text-slate-500 tracking-wider rtl:tracking-normal pt-3"><?= lang('App.admin_sec_modules') ?></div>

                    <a href="<?= base_url('admin/wall-of-kot-sultan') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'wall-of-kot-sultan' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="award" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_wall') ?></span>
                    </a>

                    <a href="<?= base_url('admin/wall-categories') ?>"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'wall-categories' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="tags" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_wall_categories_nav') ?></span>
                    </a>

                    <a href="<?= base_url('admin/emergency-numbers') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'emergency-numbers' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="phone-call" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_emergency') ?></span>
                    </a>

                    <a href="<?= base_url('admin/images') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'images' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="image" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_images') ?></span>
                    </a>

                    <a href="<?= base_url('admin/database') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'database' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="database" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_database') ?></span>
                    </a>

                    <div class="px-3 py-1.5 text-[10px] uppercase font-bold text-slate-500 tracking-wider rtl:tracking-normal pt-3"><?= lang('App.admin_sec_system') ?></div>

                    <a href="<?= base_url('admin/nav-links') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'nav-links' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="menu" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_menu_management') ?></span>
                    </a>

                    <a href="<?= base_url('admin/activity-logs') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'activity-logs' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_logs') ?></span>
                    </a>

                    <a href="<?= base_url('admin/settings') ?>" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentSegment === 'settings' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' ?>">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                        <span><?= lang('App.admin_settings') ?></span>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Footer Logout -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40">
                <a href="<?= base_url('admin/logout') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950/40 hover:text-rose-700 dark:hover:text-rose-300 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span><?= lang('App.log_out') ?></span>
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <!-- TOP BAR HEADER -->
            <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-40 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-xs">
                
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 dark:text-slate-300 hover:text-emerald-600">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <h1 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">
                        <?= esc($pageHeading ?? $title ?? lang('App.admin_dashboard')) ?>
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    
                    <!-- Language Switcher -->
                    <a href="<?= base_url('lang/' . ($isRtl ? 'en' : 'ur')) ?>" class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 transition-colors flex items-center gap-1.5 border border-slate-200 dark:border-slate-700">
                        <i data-lucide="globe" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                        <span><?= $isRtl ? 'English' : 'اردو' ?></span>
                    </a>

                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()" class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-700">
                        <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4 text-emerald-600"></i>
                        <i x-show="darkMode" data-lucide="sun" class="w-4 h-4 text-amber-400"></i>
                    </button>

                    <!-- View Public Website -->
                    <a href="<?= base_url('/') ?>" target="_blank" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-xs">
                        <span><?= lang('App.view_website') ?></span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>

                    <!-- Admin User Avatar -->
                    <div class="flex items-center gap-2 pl-2 rtl:pl-0 rtl:pr-2 border-l rtl:border-l-0 rtl:border-r border-slate-200 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-xs">
                            <?= strtoupper(substr($adminUsername, 0, 1)) ?>
                        </div>
                        <span class="hidden md:inline text-xs font-bold text-slate-800 dark:text-slate-200"><?= esc($adminUsername) ?></span>
                    </div>
                </div>
            </header>

            <!-- FLASH NOTIFICATIONS -->
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                <?php if (session('success')): ?>
                <div class="p-4 mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-sm font-semibold flex items-center gap-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                    <span><?= esc(session('success')) ?></span>
                </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                <div class="p-4 mb-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-300 text-sm font-semibold flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
                    <span><?= esc(session('error')) ?></span>
                </div>
                <?php endif; ?>

                <?php if (session('warning')): ?>
                <div class="p-4 mb-4 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/60 text-amber-800 dark:text-amber-300 text-sm font-semibold flex items-center gap-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0"></i>
                    <span><?= esc(session('warning')) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- PAGE CONTENT RENDER -->
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
                <?= $this->renderSection('content') ?>
            </main>

            <!-- FOOTER -->
            <footer class="py-4 px-6 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
                <div><?= lang('App.admin_console_footer') ?></div>
                <div><?= lang('App.admin_server_time') ?>: <?= date('Y-m-d H:i:s') ?></div>
            </footer>
        </div>
    </div>

    <!-- Theme & Icon Scripts -->
    <script>
        // Single shared theme key across the whole site (public + admin + 404),
        // defaulting to LIGHT so the admin panel can never drift into a
        // half-dark state when the OS/browser prefers dark.
        function adminThemeHandler() {
            return {
                darkMode: localStorage.getItem('theme') === 'dark',
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>
</body>
</html>
