<?php 
    $currentLang = service('request')->getLocale();
    $isUrdu = ($currentLang === 'ur');
    
    // Automatic active route detection
    $currentPath = trim(uri_string(), '/');
    if ($currentPath === '' || $currentPath === 'home') {
        $currentPath = '/';
    }

    // Default Fallback Links (in case DB fails)
    $navLinks = [
        ['url' => '/', 'title_en' => lang('App.nav_home', [], 'en'), 'title_ur' => lang('App.nav_home', [], 'ur')],
        ['url' => 'directory', 'title_en' => lang('App.nav_directory', [], 'en'), 'title_ur' => lang('App.nav_directory', [], 'ur')],
        ['url' => 'emergency-numbers', 'title_en' => lang('App.nav_emergency', [], 'en'), 'title_ur' => lang('App.nav_emergency', [], 'ur')],
        ['url' => 'wall-of-kot-sultan', 'title_en' => lang('App.nav_wall', [], 'en'), 'title_ur' => lang('App.nav_wall', [], 'ur')],
        ['url' => 'volunteer', 'title_en' => lang('App.nav_volunteer', [], 'en'), 'title_ur' => lang('App.nav_volunteer', [], 'ur')],
        ['url' => 'about', 'title_en' => lang('App.nav_about', [], 'en'), 'title_ur' => lang('App.nav_about', [], 'ur')],
        ['url' => 'contact', 'title_en' => lang('App.nav_contact', [], 'en'), 'title_ur' => lang('App.nav_contact', [], 'ur')],
    ];

    try {
        $db = \Config\Database::connect();
        if ($db->tableExists('nav_links')) {
            $dbLinks = $db->table('nav_links')
                          ->where('status', 'active')
                          ->orderBy('sort_order', 'ASC')
                          ->get()
                          ->getResultArray();
            if (!empty($dbLinks)) {
                $navLinks = $dbLinks;
            }
        }
    } catch (\Throwable $e) {
        // Silently catch and use fallbacks
    }

    $isLinkActive = function($url) use ($currentPath) {
        $url = trim($url, '/');
        if ($url === '') $url = '/';
        
        if ($currentPath === $url) return true;
        
        if ($url === 'directory' && (str_starts_with($currentPath, 'business/') || str_starts_with($currentPath, 'listing/') || $currentPath === 'listings')) {
            return true;
        }
        if ($url === 'emergency-numbers' && $currentPath === 'emergency') {
            return true;
        }
        if ($url === 'wall-of-kot-sultan' && str_starts_with($currentPath, 'wall-of-kot-sultan/')) {
            return true;
        }
        return false;
    };
?>
<style>
/* Active underline — physical left/right so it stays centered in LTR and RTL */
.nav-link-active::after {
    content: '';
    position: absolute;
    bottom: 4px;
    left: 0;
    right: 0;
    margin-inline: auto;
    width: 40px;
    height: 3px;
    border-radius: 9999px;
    background-color: #F59E0B;
    transition: opacity 200ms ease;
}
/* Navbar controls — keep theme + language buttons aligned in LTR and RTL */
.nav-controls {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    height: 36px !important;
    flex-shrink: 0 !important;
}
nav button.nav-control-btn.btn {
    height: 36px !important;
    min-height: 36px !important;
    max-height: 36px !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    line-height: 1 !important;
    box-sizing: border-box !important;
    flex-direction: row !important;
    overflow: hidden !important;
}
nav button.nav-theme-btn.btn {
    width: 36px !important;
    min-width: 36px !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
}
nav button.nav-lang-btn.btn {
    padding-inline: 10px !important;
}
nav .nav-lang-label,
nav button.nav-control-btn span {
    font-size: 13px !important;
    line-height: 1 !important;
    display: inline-flex !important;
    align-items: center !important;
    padding-block: 0 !important;
}
nav button.nav-control-btn svg,
nav button.nav-control-btn [data-lucide] {
    width: 14px !important;
    height: 14px !important;
    flex-shrink: 0 !important;
}
html[lang="ur"] nav a {
    font-size: 0.95rem !important;
    line-height: 1.35 !important;
}
.nav-bar-row {
    height: 64px !important;
    max-height: 64px !important;
    min-height: 64px !important;
}
html[lang="ur"] .nav-bar-row {
    height: 64px !important;
    max-height: 64px !important;
    min-height: 64px !important;
}
html[lang="ur"] .nav-bar-row a.rounded-lg,
html[lang="ur"] .nav-bar-row [class*="rounded-lg"] {
    padding-block: 0.35rem !important;
}

/* Language dropdown — compact in both EN and UR */
.nav-lang-menu {
    width: 8rem !important;
    min-width: 8rem !important;
    max-width: 8rem !important;
    padding-block: 0.25rem !important;
}
html[lang="ur"] .nav-lang-menu,
html[lang="ur"] .nav-lang-menu[class*="rounded-xl"] {
    padding-block: 0.25rem !important;
}
.nav-lang-menu a.nav-lang-option {
    min-height: 36px !important;
    height: 36px !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    padding-inline: 0.75rem !important;
    display: flex !important;
    align-items: center !important;
    line-height: 1 !important;
}
html[lang="ur"] .nav-lang-menu a.nav-lang-option,
html[lang="ur"] .nav-lang-menu a.nav-lang-option span,
html[lang="ur"] .nav-lang-menu .nav-lang-option-label {
    font-size: 13px !important;
    line-height: 1.2 !important;
    padding-block: 0 !important;
    min-height: 0 !important;
}
.nav-lang-option-label {
    font-size: 13px !important;
    line-height: 1.2 !important;
}
/* English label: smaller Latin size so it matches Urdu visual weight */
.nav-lang-menu a[lang="en"],
.nav-lang-menu a[lang="en"] span,
.nav-lang-menu a[lang="en"] .nav-lang-option-label,
html[lang="ur"] .nav-lang-menu a[lang="en"],
html[lang="ur"] .nav-lang-menu a[lang="en"] span,
html[lang="ur"] .nav-lang-menu a[lang="en"] .nav-lang-option-label {
    font-family: Inter, system-ui, sans-serif !important;
    font-size: 12px !important;
    line-height: 1.2 !important;
    font-weight: 600 !important;
}
/* Urdu label keeps Nastaleeq at the comfortable size */
.nav-lang-menu a[lang="ur"],
.nav-lang-menu a[lang="ur"] span,
.nav-lang-menu a[lang="ur"] .nav-lang-option-label,
html[lang="ur"] .nav-lang-menu a[lang="ur"],
html[lang="ur"] .nav-lang-menu a[lang="ur"] span,
html[lang="ur"] .nav-lang-menu a[lang="ur"] .nav-lang-option-label {
    font-size: 13px !important;
    line-height: 1.25 !important;
}
</style>
<nav class="fixed top-0 inset-x-0 z-50 bg-white/95 dark:bg-slate-900/95 border-b border-slate-200/80 dark:border-slate-800 shadow-xs transition-all duration-200"
     x-data="{
        mobileMenuOpen: false,
        x: 50,
        y: 50,
        isHovered: false,
        onMouseMove(e) {
            if ('ontouchstart' in window || navigator.maxTouchPoints > 0) return;
            const rect = $el.getBoundingClientRect();
            this.x = ((e.clientX - rect.left) / rect.width) * 100;
            this.y = ((e.clientY - rect.top) / rect.height) * 100;
        }
     }"
     @mousemove="onMouseMove($event)"
     @mouseenter="isHovered = true"
     @mouseleave="isHovered = false"
     :style="`--nav-x: ${x}%; --nav-y: ${y}%;`">

    <!-- Mouse Proximity BorderGlow Bottom Edge Mask -->
    <div class="absolute bottom-0 inset-x-0 h-[2px] pointer-events-none transition-opacity duration-300 z-10"
         :class="isHovered ? 'opacity-100' : 'opacity-30 md:opacity-0'"
         :style="`background: radial-gradient(400px circle at var(--nav-x) 100%, ${darkMode ? 'rgba(56, 189, 248, 0.6), rgba(59, 130, 246, 0.4)' : 'rgba(37, 99, 235, 0.5), rgba(14, 165, 233, 0.4)'}, transparent 80%);`">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
        <div class="flex justify-between items-center gap-3 nav-bar-row" style="height:64px;max-height:64px;">
            
            <!-- Logo -->
            <a href="<?= base_url('/') ?>" class="flex items-center gap-2.5 group shrink-0 min-w-0">
                <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center text-white shadow-sm group-hover:bg-emerald-700 transition-colors shrink-0">
                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                </div>
                <div class="flex flex-col text-start min-w-0">
                    <span class="brand-name font-bold text-lg leading-none tracking-tight text-slate-900 dark:text-white">
                        <?= lang('App.brand_name') ?>
                    </span>
                    <span class="brand-tagline text-[11px] font-medium leading-snug text-slate-500 dark:text-slate-400">
                        <?= lang('App.brand_tagline') ?>
                    </span>
                </div>
            </a>

            <!-- Desktop Nav Links with Automatic Active Indicator -->
            <div class="hidden md:flex items-center justify-center flex-1 gap-0.5 lg:gap-1 min-w-0">
                <?php foreach ($navLinks as $link): ?>
                    <?php 
                        $isActive = $isLinkActive($link['url']);
                        $linkTitle = $isUrdu ? $link['title_ur'] : $link['title_en'];
                        $href = base_url($link['url'] === '/' ? '' : ltrim($link['url'], '/'));
                    ?>
                    <a href="<?= $href ?>" class="relative px-2.5 lg:px-3.5 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all duration-200 <?= $isActive ? 'nav-link-active text-orange-500 dark:text-orange-400 font-bold' : 'text-slate-700 dark:text-slate-200 hover:text-emerald-600 hover:bg-slate-100 dark:hover:bg-slate-800' ?>">
                        <span><?= esc($linkTitle) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Controls: Language Switcher & Dark Mode Toggle -->
            <div class="nav-controls flex items-center justify-center gap-1.5 shrink-0 h-9">
                
                <!-- Magic UI Style Animated Theme Toggler -->
                <button @click="toggleTheme($event)" 
                        type="button"
                        class="nav-control-btn nav-theme-btn btn btn-sm btn-secondary relative overflow-hidden transition-all duration-300 transform active:scale-95" 
                        aria-label="Toggle Dark Mode">
                    <span class="relative w-3.5 h-3.5 block leading-none">
                        <span class="absolute inset-0 flex items-center justify-center transition-all duration-500 ease-out transform leading-none"
                              :class="darkMode ? 'rotate-0 scale-100 opacity-100' : '-rotate-90 scale-0 opacity-0'">
                            <i data-lucide="sun" class="w-3.5 h-3.5 text-amber-400"></i>
                        </span>
                        <span class="absolute inset-0 flex items-center justify-center transition-all duration-500 ease-out transform leading-none"
                              :class="!darkMode ? 'rotate-0 scale-100 opacity-100' : 'rotate-90 scale-0 opacity-0'">
                            <i data-lucide="moon" class="w-3.5 h-3.5 text-slate-700 dark:text-slate-200"></i>
                        </span>
                    </span>
                </button>
                
                <!-- Language Switcher Dropdown -->
                <div class="relative h-9 flex items-center" x-data="{ open: false }">
                    <button @click="open = !open" 
                            @click.outside="open = false"
                            type="button"
                            class="nav-control-btn nav-lang-btn btn btn-sm btn-secondary">
                        <i data-lucide="globe" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                        <span class="nav-lang-label leading-none"><?= $isUrdu ? 'اردو' : 'EN' ?></span>
                        <i data-lucide="chevron-down" class="w-3 h-3 shrink-0"></i>
                    </button>

                    <!-- end-0 aligns right in LTR, left in RTL -->
                    <div x-show="open" 
                         x-transition
                         class="nav-lang-menu absolute end-0 top-full mt-2 w-32 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-lg py-1 z-50">
                        <a href="<?= base_url('lang/en') ?>" class="nav-lang-option flex items-center gap-2 px-3 py-2 font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 text-start" lang="en">
                            <span class="nav-lang-option-label">English</span>
                        </a>
                        <a href="<?= base_url('lang/ur') ?>" class="nav-lang-option flex items-center gap-2 px-3 py-2 font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 text-start" lang="ur">
                            <span class="nav-lang-option-label">اردو</span>
                        </a>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="md:hidden btn btn-sm btn-ghost p-2 h-9 w-9 inline-flex items-center justify-center">
                    <i data-lucide="menu" class="w-5 h-5" x-show="!mobileMenuOpen"></i>
                    <i data-lucide="x" class="w-5 h-5" x-show="mobileMenuOpen"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <!-- origin-top makes it slide down from the header naturally in both LTR/RTL -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200 transform origin-top"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150 transform origin-top"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 pt-2 pb-4 space-y-1 relative z-20 text-start">
        
        <?php foreach ($navLinks as $link): ?>
            <?php 
                $isActive = $isLinkActive($link['url']);
                $linkTitle = $isUrdu ? $link['title_ur'] : $link['title_en'];
                $href = base_url($link['url'] === '/' ? '' : ltrim($link['url'], '/'));
            ?>
            <a href="<?= $href ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-base font-semibold transition-all duration-200 <?= $isActive ? 'bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 font-bold border-s-4 border-orange-500 dark:border-orange-400' : 'text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800' ?>">
                <span><?= esc($linkTitle) ?></span>
                <?php if ($isActive): ?>
                    <span class="w-2 h-2 rounded-full bg-orange-500 dark:bg-orange-400 flex-shrink-0"></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</nav>
