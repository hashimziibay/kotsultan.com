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
            <!-- Line 1: Tagline (follows the active locale; rendered in Jameel Noori Nastaleeq for Urdu) -->
            <p class="font-semibold text-2xl sm:text-3xl text-emerald-600 dark:text-emerald-400 text-center leading-relaxed py-1 <?= $isUrdu ? 'font-urdu' : '' ?>" dir="<?= $isUrdu ? 'rtl' : 'ltr' ?>">
                <?= lang('App.hero_tagline') ?>
            </p>
            <!-- Line 2: Subtitle -->
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed">
                <?= lang('App.hero_subtitle') ?>
            </p>
        </div>

        <!-- Large Search Box with Alpine.js BorderGlow & Micro-interactions -->
        <form action="<?= base_url('directory') ?>" method="GET" class="relative max-w-3xl mx-auto mb-8 z-20">
            <div x-data="{ isFocused: false, isHovered: false }"
                 @mouseenter="isHovered = true"
                 @mouseleave="isHovered = false"
                 class="hero-search-bar relative p-[6px] flex flex-col md:flex-row gap-[6px] bg-white dark:bg-slate-900 rounded-[28px] md:rounded-full transition-all duration-300 ease-out"
                 :class="isFocused ? 'border-emerald-500/45 shadow-[0_0_40px_rgba(16,185,129,0.18),0_12px_30px_rgba(16,185,129,0.08)] scale-[1.01]' : (isHovered ? 'border-emerald-500/25 shadow-[0_0_35px_rgba(16,185,129,0.15),0_12px_30px_rgba(16,185,129,0.08)]' : 'border-emerald-500/15 shadow-[0_0_30px_rgba(16,185,129,0.12),0_12px_30px_rgba(16,185,129,0.08)]')"
                 style="border-width: 1px; border-style: solid;">
                 
                <!-- Search Input Container -->
                <div class="relative flex-grow flex items-center h-14 min-w-0">
                    <i data-lucide="search" class="hero-search-icon w-5 h-5 text-[#10B981] absolute start-5 top-1/2 -translate-y-1/2 pointer-events-none z-10 transition-transform duration-300" :class="isFocused ? 'scale-110' : ''"></i>
                    <input type="text" 
                           name="q" 
                           placeholder="<?= lang('App.search_placeholder') ?>" 
                           @focus="isFocused = true"
                           @blur="isFocused = false"
                           class="hero-search-input w-full h-full ps-14 pe-5 bg-transparent text-[#1E293B] dark:text-white placeholder-[#94A3B8] text-base font-medium outline-none border-none focus:ring-0">
                </div>

                <!-- Search Button -->
                <button type="submit" 
                        class="w-full md:w-[220px] lg:w-[260px] shrink-0 flex items-center justify-center gap-2 h-14 rounded-full bg-gradient-to-r from-[#10B981] to-[#059669] hover:from-[#34d399] hover:to-[#10B981] text-white font-bold tracking-tight shadow-[0_12px_30px_rgba(16,185,129,0.30)] hover:shadow-[0_18px_40px_rgba(16,185,129,0.35)] transition-all duration-300 ease-out hover:-translate-y-[2px] active:scale-[0.98] active:translate-y-0">
                    <i data-lucide="search" class="w-5 h-5 shrink-0"></i>
                    <span class="truncate"><?= lang('App.search_button') ?></span>
                </button>
            </div>
        </form>

        <!-- Dynamic Category Shortcuts -->
        <?php if (!empty($categories)): ?>
        <div class="flex flex-wrap items-center justify-center gap-2 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
            <span class="font-semibold text-slate-500 dark:text-slate-400 me-1"><?= lang('App.popular_shortcuts') ?></span>
            <?php foreach (array_slice($categories, 0, 8) as $cat): ?>
                <a href="<?= !empty($cat['url']) ? esc($cat['url']) : base_url('directory?category=' . (!empty($cat['slug']) ? $cat['slug'] : $cat['id'])) ?>" 
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
                <?= lang('App.view_all') ?> <?= $isUrdu ? '&larr;' : '&rarr;' ?>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
            <?php foreach ($popularCategories as $cat): ?>
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
                <?= lang('App.view_all') ?> <?= $isUrdu ? '&larr;' : '&rarr;' ?>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php foreach ($recentBusinesses as $item): ?>
                <?= view('components/business_card', ['item' => $item, 'isUrdu' => $isUrdu]) ?>
            <?php endforeach; ?>
        </div>

    </div>
</section>


<?= $this->endSection() ?>
