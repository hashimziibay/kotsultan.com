<?php 
    $currentLang = service('request')->getLocale();
    $isRtl = ($currentLang === 'ur');
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>" x-data="themeHandler()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? lang('App.brand_name') . ' - ' . lang('App.brand_tagline') ?></title>
    
    <!-- Favicon (matches navbar brand map-pin icon) -->
    <link rel="icon" href="<?= base_url('favicon.svg') ?>" type="image/svg+xml">
    <link rel="icon" href="<?= base_url('favicon-32.png') ?>" type="image/png" sizes="32x32">
    <link rel="icon" href="<?= base_url('favicon-16.png') ?>" type="image/png" sizes="16x16">
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('apple-touch-icon.png') ?>" sizes="180x180">

    <!-- Google Fonts: Inter (English UI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    
    <!-- Alpine.js Plugins & Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Click Spark Particle Effect -->
    <script defer src="<?= base_url('js/click-spark.js') ?>"></script>

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
    
    <!-- Global Preloader -->
    <div id="global-preloader" class="fixed inset-0 z-[99999] bg-slate-50 dark:bg-slate-900 flex flex-col items-center justify-center transition-all duration-500">
        <div class="relative flex items-center justify-center mb-4">
            <!-- Pulsing outer ring -->
            <div class="absolute inset-0 rounded-full border-4 border-emerald-500/20 animate-ping"></div>
            <!-- Spinning inner ring -->
            <div class="absolute w-16 h-16 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin"></div>
            <!-- Center Icon -->
            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/40 rounded-full flex items-center justify-center text-emerald-600 dark:text-emerald-400 z-10">
                <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
            </div>
        </div>
        <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 animate-pulse tracking-wide">
            <?= lang('App.brand_name') ?? 'KotSultan' ?>
        </p>
    </div>

    <!-- Magic UI Smooth Cursor (Desktop Only) -->
    <?= $this->include('components/smooth_cursor') ?>

    <!-- Global Magic UI Animated Grid Pattern Background -->
    <?= $this->include('components/animated_grid') ?>

    <!-- Navbar -->
    <?= $this->include('components/navbar') ?>

    <!-- Main Content -->
    <main class="flex-grow pt-20">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <?= $this->include('components/footer') ?>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

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

        // Global Preloader Fade-out
        window.addEventListener('load', () => {
            const preloader = document.getElementById('global-preloader');
            if (preloader) {
                preloader.classList.add('opacity-0', 'invisible');
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 500);
            }
        });
    </script>
</body>
</html>


