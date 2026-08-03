<?php 
    $currentLang = session('lang') ?? 'en';
    $isRtl = ($currentLang === 'ur');
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>" x-data="themeHandler()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? lang('App.brand_name') . ' - ' . lang('App.brand_tagline') ?></title>
    
    <!-- Preload Local Urdu Font (Jameel Noori Nastaleeq) to prevent flickering -->
    <?php if ($isRtl): ?>
        <link rel="preload" href="<?= base_url('fonts/JameelNooriNastaleeq.woff') ?>" as="font" type="font/woff" crossorigin="anonymous">
    <?php endif; ?>

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
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col font-sans transition-colors duration-200 antialiased relative">

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
    </script>
</body>
</html>


