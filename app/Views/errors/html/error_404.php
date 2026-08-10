<?php
// Resolve the active language so the 404 page ALWAYS follows the user's
// selection (GET > session > cookie > default), even when rendered by the
// exception handler for unmatched URLs (where global filters do not run).
$session   = session();
$getLang   = service('request')->getGet('lang');
$sesLang   = $session->get('lang');
$cookLang  = service('request')->getCookie('lang');
$locale404 = $getLang ?? $sesLang ?? $cookLang ?? 'en';
if (!in_array($locale404, ['en', 'ur'], true)) {
    $locale404 = 'en';
}
$session->set('lang', $locale404);
service('request')->setLocale($locale404);
service('language')->setLocale($locale404);
$isRtl404 = ($locale404 === 'ur');
?>
<!DOCTYPE html>
<html lang="<?= $locale404 ?>" dir="<?= $isRtl404 ? 'rtl' : 'ltr' ?>" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - <?= lang('App.page_not_found') ?> | KotSultan.com</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    
    <!-- Alpine.js & Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Apply saved theme before first paint to avoid theme flash/inconsistency -->
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
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col justify-between font-sans antialiased relative selection:bg-emerald-500 selection:text-white"
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
      :class="{ 'dark': darkMode }">

    <!-- Top Simplified Bar -->
    <header class="w-full border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md sticky top-0 z-50 py-3.5 px-4 sm:px-8">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="<?= base_url('/') ?>" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center text-white shadow-sm group-hover:bg-emerald-700 transition-colors">
                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-lg leading-none tracking-tight text-slate-900 dark:text-white">
                        <?= lang('App.brand_name') ?>
                    </span>
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                        <?= lang('App.brand_tagline') ?>
                    </span>
                </div>
            </a>

            <a href="<?= base_url('/') ?>" class="btn btn-sm btn-outline">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span><?= lang('App.back_to_home') ?></span>
            </a>
        </div>
    </header>

    <!-- 404 Main Section -->
    <main class="flex-grow flex items-center justify-center py-16 px-4 relative z-10">
        <div class="max-w-xl w-full text-center space-y-6">
            
            <!-- Graphic badge -->
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 shadow-inner mb-2 animate-bounce">
                <i data-lucide="map-pin-off" class="w-12 h-12"></i>
            </div>

            <div class="space-y-2">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400"><?= lang('App.error_404') ?></span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    <?= lang('App.page_not_found') ?>
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                    <?= lang('App.page_not_found_sub') ?>
                </p>
            </div>

            <!-- Search Form -->
            <form action="<?= base_url('directory') ?>" method="GET" class="max-w-md mx-auto pt-2">
                <div class="relative flex items-center">
                    <input type="text" 
                           name="q" 
                           placeholder="<?= lang('App.search_placeholder') ?>" 
                           class="w-full pl-10 pr-24 rtl:pl-24 rtl:pr-10 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 text-sm font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 shadow-xs">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 rtl:left-auto rtl:right-3.5"></i>
                    <button type="submit" class="absolute right-1.5 rtl:right-auto rtl:left-1.5 btn btn-sm btn-primary">
                        <?= lang('App.search_button') ?>
                    </button>
                </div>
            </form>

            <div class="pt-4 flex items-center justify-center gap-3">
                <a href="<?= base_url('/') ?>" class="btn btn-md btn-primary">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    <span><?= lang('App.return_to_home') ?></span>
                </a>
                <a href="<?= base_url('directory') ?>" class="btn btn-md btn-secondary">
                    <i data-lucide="list" class="w-4 h-4"></i>
                    <span><?= lang('App.browse_listings') ?></span>
                </a>
            </div>

        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="py-6 border-t border-slate-200 dark:border-slate-800 text-center text-xs font-medium text-slate-500 dark:text-slate-400">
        &copy; <?= date('Y') ?> <?= lang('App.brand_name') ?> — <?= lang('App.brand_tagline') ?>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
