<!DOCTYPE html>
<html lang="en" x-data="themeHandler()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login - Kot Sultan.com' ?></title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-background text-textMain min-h-screen font-sans transition-colors duration-300">

    <?= $this->renderSection('content') ?>

    <!-- Absolute Dark Mode Toggle -->
    <button @click="darkMode = !darkMode" class="absolute top-6 right-6 z-50 p-3 rounded-full bg-card dark:bg-[#111827] border border-borderBase shadow-lg hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
        <i data-lucide="moon" class="w-5 h-5 text-textMain hidden dark:block"></i>
        <i data-lucide="sun" class="w-5 h-5 text-textMain block dark:hidden"></i>
    </button>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Check local storage for dark mode preference on load
        document.addEventListener('alpine:init', () => {
            Alpine.data('themeHandler', () => ({
                darkMode: false,
                init() {
                    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        this.darkMode = true;
                    }
                    
                    this.$watch('darkMode', value => {
                        localStorage.setItem('theme', value ? 'dark' : 'light');
                    });
                }
            }))
        });
    </script>
</body>
</html>
