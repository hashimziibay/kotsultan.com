<?php 
    $currentLang = service('request')->getLocale();
    $isRtl = ($currentLang === 'ur');
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>" x-data="themeHandler()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? lang('App.brand_name') . ' - ' . lang('App.brand_tagline')) ?></title>
    <?php if (! empty($metaDescription)): ?>
    <meta name="description" content="<?= esc($metaDescription) ?>">
    <?php endif; ?>
    <?php if (! empty($canonical)): ?>
    <link rel="canonical" href="<?= esc($canonical) ?>">
    <?php endif; ?>
    <meta property="og:title" content="<?= esc($title ?? lang('App.brand_name')) ?>">
    <?php if (! empty($metaDescription)): ?>
    <meta property="og:description" content="<?= esc($metaDescription) ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">
    
    <!-- Favicon (matches navbar brand map-pin icon) -->
    <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">
    <link rel="icon" href="<?= base_url('favicon-32.png') ?>" type="image/png" sizes="32x32">
    <link rel="icon" href="<?= base_url('favicon-16.png') ?>" type="image/png" sizes="16x16">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('apple-touch-icon.png') ?>" sizes="180x180">

    <!-- Google Fonts: Inter (English UI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"></noscript>
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    
    <!-- Alpine.js Plugins & Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.13.5/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Lucide Icons (pinned + deferred) -->
    <script defer src="https://cdn.jsdelivr.net/npm/lucide@0.468.0/dist/umd/lucide.min.js"></script>

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
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col font-sans transition-colors duration-200 antialiased relative">
    
    <!-- Lightweight boot overlay (hides as soon as DOM is ready) -->
    <div id="global-preloader" class="fixed inset-0 z-[99999] bg-slate-50 dark:bg-slate-900 flex flex-col items-center justify-center transition-opacity duration-200 pointer-events-none">
        <div class="w-10 h-10 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin"></div>
        <p class="mt-3 text-sm font-semibold text-slate-500 dark:text-slate-400 tracking-wide">
            <?= lang('App.brand_name') ?? 'KotSultan' ?>
        </p>
    </div>

    <!-- Navbar -->
    <?= $this->include('components/navbar') ?>

    <!-- Main Content -->
    <main class="flex-grow pt-20">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <?= $this->include('components/footer') ?>

    <script>
        function bootUi() {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
            const preloader = document.getElementById('global-preloader');
            if (preloader) {
                preloader.classList.add('opacity-0', 'invisible');
                setTimeout(() => { preloader.style.display = 'none'; }, 180);
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bootUi);
        } else {
            bootUi();
        }
        // Lucide is deferred — retry briefly until available
        let iconTries = 0;
        const iconTimer = setInterval(() => {
            iconTries += 1;
            if (window.lucide || iconTries > 40) {
                clearInterval(iconTimer);
                if (window.lucide) window.lucide.createIcons();
            }
        }, 50);

        // Theme handler with light mode as default & Circular View Transition
        document.addEventListener('alpine:init', () => {
            Alpine.data('themeHandler', () => ({
                darkMode: false,
                init() {
                    // Reset / Default to Light Mode unless explicitly set to dark
                    if (localStorage.getItem('theme') === 'dark') {
                        this.darkMode = true;
                    } else {
                        this.darkMode = false;
                        localStorage.setItem('theme', 'light');
                    }
                    
                    this.$watch('darkMode', value => {
                        localStorage.setItem('theme', value ? 'dark' : 'light');
                    });
                },
                toggleTheme(event) {
                    const x = event ? event.clientX : window.innerWidth / 2;
                    const y = event ? event.clientY : window.innerHeight / 2;

                    const endRadius = Math.hypot(
                        Math.max(x, window.innerWidth - x),
                        Math.max(y, window.innerHeight - y)
                    );

                    if (!document.startViewTransition || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                        this.darkMode = !this.darkMode;
                        return;
                    }

                    const transition = document.startViewTransition(() => {
                        this.darkMode = !this.darkMode;
                    });

                    transition.ready.then(() => {
                        const clipPath = [
                            `circle(0px at ${x}px ${y}px)`,
                            `circle(${endRadius}px at ${x}px ${y}px)`
                        ];
                        document.documentElement.animate(
                            {
                                clipPath: clipPath
                            },
                            {
                                duration: 450,
                                easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                                pseudoElement: '::view-transition-new(root)'
                            }
                        );
                    });
                }
            }))
        });
        // Blur Reveal Intersection Observer
        document.addEventListener('DOMContentLoaded', () => {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                document.querySelectorAll('.blur-reveal').forEach(el => el.classList.add('revealed'));
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        const delay = target.dataset.blurDelay || 0;
                        setTimeout(() => {
                            target.classList.add('revealed');
                        }, delay);
                    } else {
                        // Reset to initial state when leaving viewport so it replays upon re-entry
                        entry.target.classList.remove('revealed');
                    }
                });
            }, {
                threshold: 0.15
            });

            // Group elements by parent container to calculate natural 100ms stagger for co-visible headings
            const parents = new Set();
            const elements = document.querySelectorAll('.blur-reveal');
            elements.forEach(el => {
                if (el.parentElement) parents.add(el.parentElement);
            });

            parents.forEach(parent => {
                const siblingHeadingGroup = parent.querySelectorAll('.blur-reveal');
                siblingHeadingGroup.forEach((el, index) => {
                    if (!el.dataset.blurDelay) {
                        el.dataset.blurDelay = index * 100;
                    }
                });
            });

            elements.forEach(el => observer.observe(el));

            // Statistics Counter Animation (0 -> Final Number)
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const targetNum = parseInt(el.dataset.countTo, 10);
                        if (!isNaN(targetNum)) {
                            let start = 0;
                            const duration = 1200;
                            const stepTime = Math.max(16, Math.floor(duration / (targetNum || 1)));
                            const timer = setInterval(() => {
                                start += Math.ceil(targetNum / 20);
                                if (start >= targetNum) {
                                    el.textContent = targetNum;
                                    clearInterval(timer);
                                } else {
                                    el.textContent = start;
                                }
                            }, stepTime);
                        }
                        counterObserver.unobserve(el);
                    }
                });
            }, { threshold: 0.3 });

            document.querySelectorAll('[data-count-to]').forEach(el => counterObserver.observe(el));
        });
    </script>
</body>
</html>


