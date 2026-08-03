<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
    $isUrdu = ($lang === 'ur');
?>

<!-- 1. HERO SECTION WITH SEARCH -->
<section class="relative bg-gradient-to-b from-emerald-50/50 to-slate-50 dark:from-slate-900 dark:to-slate-900 py-12 md:py-20 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200 overflow-hidden">
    
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 text-center">
        
        <!-- Headline -->
        <h1 class="blur-reveal text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-4">
            <?= lang('App.hero_title') ?>
        </h1>
        
        <!-- Subtitle Container (Fixed Urdu Tagline + Subtitle) -->
        <div class="blur-reveal max-w-2xl mx-auto mb-8 space-y-2">
            <!-- Line 1: Fixed Urdu Tagline (Jameel Noori Nastaleeq — always shows in Urdu) -->
            <p dir="rtl" class="font-urdu text-xl sm:text-2xl text-emerald-600 dark:text-emerald-400 text-center leading-[2.2] py-2">
                کوٹ سلطان، دلوں میں بستا ایک چھوٹا سا جہان
            </p>
            <!-- Line 2: Subtitle -->
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed">
                <?= lang('App.hero_subtitle') ?>
            </p>
        </div>

        <!-- Large Search Box with Alpine.js BorderGlow & Micro-interactions -->
        <form action="<?= base_url('directory') ?>" method="GET" class="relative max-w-3xl mx-auto mb-8">
            <div x-data="{
                    x: 50,
                    y: 50,
                    isHovered: false,
                    onMouseMove(e) {
                        const rect = $el.getBoundingClientRect();
                        this.x = ((e.clientX - rect.left) / rect.width) * 100;
                        this.y = ((e.clientY - rect.top) / rect.height) * 100;
                    }
                 }"
                 @mousemove="onMouseMove($event)"
                 @mouseenter="isHovered = true"
                 @mouseleave="isHovered = false"
                 class="relative p-[2px] rounded-xl overflow-hidden transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg"
                 :style="`--x: ${x}%; --y: ${y}%;`">

                <!-- Hardware-Accelerated Proximity Glow Overlay -->
                <div class="absolute inset-0 rounded-xl pointer-events-none transition-opacity duration-300"
                     :class="isHovered ? 'opacity-100' : 'opacity-0'"
                     :style="`background: radial-gradient(250px circle at var(--x) var(--y), rgba(5, 150, 105, 0.45), transparent 80%);`"></div>

                <!-- Fallback Border for Static View -->
                <div class="absolute inset-0 rounded-xl border border-slate-300 dark:border-slate-700 pointer-events-none transition-opacity duration-300"
                     :class="isHovered ? 'opacity-0' : 'opacity-100'"></div>

                <!-- Search Input Container -->
                <div class="relative flex flex-col sm:flex-row bg-white dark:bg-slate-800 rounded-2xl p-2 gap-2 z-10 shadow-sm border border-slate-200 dark:border-slate-700/80">
                    <div class="relative flex-grow flex items-center">
                        <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-4 rtl:right-4 rtl:left-auto pointer-events-none transition-colors group-focus-within:text-emerald-600"></i>
                        <input type="text" 
                               name="q" 
                               placeholder="<?= lang('App.search_placeholder') ?>" 
                               class="w-full pl-11 rtl:pr-11 rtl:pl-4 pr-4 h-14 bg-transparent text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none transition-colors">
                    </div>
                    <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 h-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-[12px] sm:rounded-[12px] font-bold shadow-sm transition-all focus:ring-2 focus:ring-emerald-500/20">
                        <i data-lucide="search" class="w-5 h-5"></i>
                        <span><?= lang('App.search_button') ?></span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Dynamic Category Shortcuts -->
        <?php if (!empty($categories)): ?>
        <div class="flex flex-wrap items-center justify-center gap-2 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
            <span class="font-semibold text-slate-500 dark:text-slate-400 me-1"><?= lang('App.popular_shortcuts') ?></span>
            <?php foreach (array_slice($categories, 0, 8) as $cat): ?>
                <a href="<?= base_url('directory?category=' . $cat['id']) ?>" 
                   class="btn btn-sm btn-secondary font-medium">
                    <?= $isUrdu ? esc($cat['name_ur']) : esc($cat['name_en']) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>


<!-- 2. POPULAR CATEGORIES -->
<section class="py-12 md:py-16 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col items-center text-center mb-10 gap-2">
            <h2 class="blur-reveal text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                <?= lang('App.popular_categories') ?>
            </h2>
            <p class="blur-reveal text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xl">
                <?= lang('App.categories_subtitle') ?>
            </p>
            <a href="<?= base_url('directory') ?>" class="btn btn-sm btn-ghost text-emerald-600 dark:text-emerald-400 mt-2">
                <?= lang('App.view_all') ?> &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4">
            <?php foreach ($categories as $cat): ?>
                <?php 
                    $count = isset($stats['category_totals'][$cat['id']]) ? $stats['category_totals'][$cat['id']] : 0;
                    echo view('components/category_card', ['cat' => $cat, 'isUrdu' => $isUrdu, 'count' => $count]);
                ?>
            <?php endforeach; ?>
        </div>

    </div>
</section>


<!-- 3. RECENTLY ADDED BUSINESSES -->
<section class="py-12 md:py-16 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col items-center text-center mb-10 gap-2">
            <h2 class="blur-reveal text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                <?= lang('App.recently_added') ?>
            </h2>
            <p class="blur-reveal text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-xl">
                <?= lang('App.recent_subtitle') ?>
            </p>
            <a href="<?= base_url('directory') ?>" class="btn btn-sm btn-ghost text-emerald-600 dark:text-emerald-400 mt-2">
                <?= lang('App.view_all') ?> &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($recentBusinesses as $item): ?>
                <?= view('components/business_card', ['item' => $item, 'isUrdu' => $isUrdu]) ?>
            <?php endforeach; ?>
        </div>

    </div>
</section>


<!-- 4. WALL OF KOT SULTAN (PREVIEW MODE) -->
<?= view('components/wall_section', [
    'wallEntries' => $wallEntries,
    'isUrdu'      => $isUrdu,
    'mode'        => 'preview'
]) ?>


<!-- 5. DIRECTORY STATISTICS WITH ANIMATED COUNTERS -->
<section class="py-12 md:py-16 bg-slate-100 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-xl mx-auto mb-10">
            <h2 class="blur-reveal text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight mb-2">
                <?= lang('App.stats_title') ?>
            </h2>
            <p class="blur-reveal text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                <?= lang('App.stats_subtitle') ?>
            </p>
        </div>

        <!-- Dynamic Animated Statistics Counters from Database -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-center">
            <div class="bg-white dark:bg-slate-800/80 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs transition-all hover:-translate-y-0.5 hover:shadow-xs">
                <i data-lucide="store" class="w-6 h-6 text-emerald-600 dark:text-emerald-400 mx-auto mb-2"></i>
                <div class="text-2xl font-extrabold text-slate-900 dark:text-white mb-1" data-count-to="<?= $stats['total_businesses'] ?>">0</div>
                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400"><?= lang('App.total_listings') ?></div>
            </div>

            <?php foreach ($categories as $cat): ?>
            <?php $catCount = isset($stats['category_totals'][$cat['id']]) ? $stats['category_totals'][$cat['id']] : 0; ?>
            <div class="bg-white dark:bg-slate-800/80 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs transition-all hover:-translate-y-0.5 hover:shadow-xs">
                <i data-lucide="<?= !empty($cat['icon']) ? esc($cat['icon']) : 'folder' ?>" class="w-6 h-6 text-emerald-600 dark:text-emerald-400 mx-auto mb-2"></i>
                <div class="text-2xl font-extrabold text-slate-900 dark:text-white mb-1" data-count-to="<?= $catCount ?>">
                    0
                </div>
                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 line-clamp-1">
                    <?= $isUrdu ? esc($cat['name_ur']) : esc($cat['name_en']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<?= $this->endSection() ?>
