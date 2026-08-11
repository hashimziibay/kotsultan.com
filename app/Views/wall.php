<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
    $isUrdu = ($lang === 'ur');
?>

<!-- Dedicated Hero Section -->
<section class="relative bg-gradient-to-b from-emerald-50/60 to-slate-50 dark:from-slate-900 dark:to-slate-900 py-10 md:py-14 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 mb-3">
                <i data-lucide="award" class="w-3.5 h-3.5"></i>
                <?= lang('App.wall_badge') ?>
            </span>
            <h1 class="blur-reveal text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                <?= lang('App.wall_title') ?>
            </h1>
            <p class="blur-reveal text-sm sm:text-base text-slate-600 dark:text-slate-400 mt-3 leading-relaxed">
                <?= lang('App.wall_subtitle') ?>
            </p>
        </div>
    </div>
</section>

<!-- Main Wall Layout (Sidebar + Results) -->
<div class="py-10 bg-slate-50 dark:bg-slate-900 min-h-screen transition-colors duration-200" x-data="{ mobileFiltersOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Grid Container with explicit LTR to control grid tracks reliably -->
        <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-stretch" dir="ltr">
            
            <!-- SIDEBAR COLUMN -->
            <aside class="hidden lg:block lg:col-span-3 <?= $isUrdu ? 'lg:order-2' : 'lg:order-1' ?> h-full">
                <div class="bg-white dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col h-full overflow-hidden" dir="<?= $isUrdu ? 'rtl' : 'ltr' ?>">
                    
                    <!-- Search Section -->
                    <div class="p-5 border-b border-slate-100 dark:border-slate-700/50">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-4"><?= lang('App.search_button') ?></h3>
                        <form action="<?= base_url('wall-of-kot-sultan') ?>" method="GET" class="flex flex-col gap-3">
                            <?php if (!empty($selectedCategory) && $selectedCategory !== 'all'): ?>
                                <input type="hidden" name="category" value="<?= esc($selectedCategory) ?>">
                            <?php endif; ?>
                            <?php if (!empty($selectedSort)): ?>
                                <input type="hidden" name="sort" value="<?= esc($selectedSort) ?>">
                            <?php endif; ?>
                            
                            <div class="relative">
                                <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-4 rtl:right-4 rtl:left-auto top-1/2 -translate-y-1/2"></i>
                                <input type="text" 
                                       name="q" 
                                       value="<?= esc($searchQuery ?? '') ?>" 
                                       placeholder="<?= lang('App.search_wall_placeholder') ?? lang('App.search_placeholder') ?>" 
                                       class="w-full pl-11 pr-4 rtl:pr-11 rtl:pl-4 h-12 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 text-sm font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all">
                            </div>
                            
                            <button type="submit" class="w-full h-11 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all focus:ring-2 focus:ring-emerald-500/20 flex items-center justify-center">
                                <?= lang('App.search_button') ?>
                            </button>
                            
                            <?php if (!empty($searchQuery) || (!empty($selectedCategory) && $selectedCategory !== 'all')): ?>
                                <a href="<?= base_url('wall-of-kot-sultan') ?>" class="w-full h-11 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                    <?= lang('App.reset_filter') ?>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- Categories Section -->
                    <div class="pt-5 pb-3 px-5 border-b border-slate-100 dark:border-slate-700/50">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400"><?= lang('App.categories') ?? 'Categories' ?></h3>
                    </div>
                    
                    <div class="p-4" x-data="{ showAllCats: false }">
                        <?php
                            $featuredWallCats = array_slice($wallCategories ?? [], 0, 4);
                            $extraWallCats = array_slice($wallCategories ?? [], 4);
                            $extraCount = count($extraWallCats);
                            $isAllActive = ($selectedCategory === 'all' || empty($selectedCategory));
                            $allParams = [];
                            if (!empty($searchQuery)) $allParams['q'] = $searchQuery;
                            if (!empty($selectedSort)) $allParams['sort'] = $selectedSort;
                            $allUrl = base_url('wall-of-kot-sultan' . (!empty($allParams) ? '?' . http_build_query($allParams) : ''));
                        ?>
                        <div class="rounded-2xl border border-emerald-100 dark:border-emerald-900/40 bg-gradient-to-b from-emerald-50/80 to-white dark:from-emerald-950/30 dark:to-slate-800/40 p-3 mb-3">
                            <div class="flex items-center gap-2 mb-3 px-1">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                </span>
                                <div>
                                    <p class="text-xs font-extrabold text-slate-800 dark:text-slate-100 leading-tight"><?= $isUrdu ? 'اہم زمرے' : 'Important categories' ?></p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight"><?= $isUrdu ? 'مقبول زمروں سے شروع کریں' : 'Start with the most popular ones' ?></p>
                                </div>
                            </div>
                            <nav class="space-y-1.5">
                                <a href="<?= $allUrl ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 <?= $isAllActive ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800/50' : 'bg-white/80 dark:bg-slate-900/40 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700' ?>">
                                    <div class="flex items-center gap-3">
                                        <i data-lucide="users" class="w-4.5 h-4.5 <?= $isAllActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                                        <span class="text-[15px]"><?= lang('App.all_wall_categories') ?></span>
                                    </div>
                                </a>
                                <?php foreach ($featuredWallCats as $cat): ?>
                                    <?php
                                        $catSlug = $cat['slug'] ?: $cat['id'];
                                        $catName = $isUrdu ? ($cat['name_ur'] ?: $cat['name_en']) : ($cat['name_en'] ?: $cat['name_ur']);
                                        $isActive = ($selectedCategory == $cat['id'] || $selectedCategory === $cat['slug'] || $selectedCategory === $cat['name_en']);
                                        $queryParams = [];
                                        if (!empty($searchQuery)) $queryParams['q'] = $searchQuery;
                                        if (!empty($selectedSort)) $queryParams['sort'] = $selectedSort;
                                        $queryParams['category'] = $catSlug;
                                    ?>
                                    <a href="<?= base_url('wall-of-kot-sultan?' . http_build_query($queryParams)) ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 <?= $isActive ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800/50' : 'bg-white/80 dark:bg-slate-900/40 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700' ?>">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="<?= esc($cat['icon'] ?: 'user') ?>" class="w-4.5 h-4.5 <?= $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                                            <span class="text-[15px]"><?= esc($catName) ?></span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </nav>
                        </div>

                        <?php if ($extraCount > 0): ?>
                            <button type="button"
                                    @click="showAllCats = !showAllCats"
                                    class="w-full mb-2 h-11 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50/70 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 font-bold text-sm hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors flex items-center justify-center gap-2">
                                <i data-lucide="chevrons-down" class="w-4 h-4" x-show="!showAllCats"></i>
                                <i data-lucide="chevrons-up" class="w-4 h-4" x-show="showAllCats" x-cloak></i>
                                <span x-text="showAllCats ? '<?= $isUrdu ? 'کم دکھائیں' : 'Show less' ?>' : '<?= $isUrdu ? 'مزید دیکھنے کے لیے کلک کریں (' . $extraCount . ')' : 'Click to view more (' . $extraCount . ')' ?>'"></span>
                            </button>
                            <nav class="space-y-1.5" x-show="showAllCats" x-cloak x-transition>
                                <?php foreach ($extraWallCats as $cat): ?>
                                    <?php
                                        $catSlug = $cat['slug'] ?: $cat['id'];
                                        $catName = $isUrdu ? ($cat['name_ur'] ?: $cat['name_en']) : ($cat['name_en'] ?: $cat['name_ur']);
                                        $isActive = ($selectedCategory == $cat['id'] || $selectedCategory === $cat['slug'] || $selectedCategory === $cat['name_en']);
                                        $queryParams = [];
                                        if (!empty($searchQuery)) $queryParams['q'] = $searchQuery;
                                        if (!empty($selectedSort)) $queryParams['sort'] = $selectedSort;
                                        $queryParams['category'] = $catSlug;
                                    ?>
                                    <a href="<?= base_url('wall-of-kot-sultan?' . http_build_query($queryParams)) ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 <?= $isActive ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800/50' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent' ?>">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="<?= esc($cat['icon'] ?: 'user') ?>" class="w-4.5 h-4.5 <?= $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                                            <span class="text-[15px]"><?= esc($catName) ?></span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
            
            <!-- MAIN CONTENT COLUMN -->
            <main class="lg:col-span-9 <?= $isUrdu ? 'lg:order-1' : 'lg:order-2' ?>" dir="<?= $isUrdu ? 'rtl' : 'ltr' ?>">
                
                <!-- Mobile Inline Expandable Filters/Search (Hidden on Desktop) -->
                <div class="mb-6 lg:hidden">
                    <button type="button" @click="mobileFiltersOpen = !mobileFiltersOpen" class="w-full flex items-center justify-between bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-4 rounded-2xl shadow-sm text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <div class="flex items-center gap-2">
                            <i data-lucide="filter" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                            <span><?= lang('App.search_button') ?> & <?= lang('App.categories') ?? 'Categories' ?></span>
                        </div>
                        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform duration-300" :class="mobileFiltersOpen ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="mobileFiltersOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="mt-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm" style="display: none;">
                        
                        <!-- Mobile Search -->
                        <form action="<?= base_url('wall-of-kot-sultan') ?>" method="GET" class="flex flex-col gap-3 mb-6">
                            <?php if (!empty($selectedCategory) && $selectedCategory !== 'all'): ?>
                                <input type="hidden" name="category" value="<?= esc($selectedCategory) ?>">
                            <?php endif; ?>
                            <?php if (!empty($selectedSort)): ?>
                                <input type="hidden" name="sort" value="<?= esc($selectedSort) ?>">
                            <?php endif; ?>
                            
                            <div class="relative">
                                <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-4 rtl:right-4 rtl:left-auto top-1/2 -translate-y-1/2"></i>
                                <input type="text" name="q" value="<?= esc($searchQuery ?? '') ?>" placeholder="<?= lang('App.search_wall_placeholder') ?? lang('App.search_placeholder') ?>" class="w-full pl-11 pr-4 rtl:pr-11 rtl:pl-4 h-12 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 text-sm font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all">
                            </div>
                            <button type="submit" class="w-full h-11 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all focus:ring-2 focus:ring-emerald-500/20 flex items-center justify-center">
                                <?= lang('App.search_button') ?>
                            </button>
                        </form>
                        
                        <hr class="border-slate-200 dark:border-slate-700 mb-4">
                        
                        <!-- Mobile Categories -->
                        <div x-data="{ showAllCatsMobile: false }">
                            <div class="rounded-2xl border border-emerald-100 dark:border-emerald-900/40 bg-gradient-to-b from-emerald-50/80 to-white dark:from-emerald-950/30 dark:to-slate-800/40 p-3 mb-3">
                                <div class="flex items-center gap-2 mb-3 px-1">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300">
                                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                    </span>
                                    <div>
                                        <p class="text-xs font-extrabold text-slate-800 dark:text-slate-100 leading-tight"><?= $isUrdu ? 'اہم زمرے' : 'Important categories' ?></p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight"><?= $isUrdu ? 'مقبول زمروں سے شروع کریں' : 'Start with the most popular ones' ?></p>
                                    </div>
                                </div>
                                <nav class="space-y-1.5">
                                    <a href="<?= $allUrl ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 <?= $isAllActive ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800/50' : 'bg-white/80 dark:bg-slate-900/40 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700' ?>">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="users" class="w-4.5 h-4.5 <?= $isAllActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                                            <span class="text-[15px]"><?= lang('App.all_wall_categories') ?></span>
                                        </div>
                                    </a>
                                    <?php foreach ($featuredWallCats as $cat): ?>
                                        <?php
                                            $catSlug = $cat['slug'] ?: $cat['id'];
                                            $catName = $isUrdu ? ($cat['name_ur'] ?: $cat['name_en']) : ($cat['name_en'] ?: $cat['name_ur']);
                                            $isActive = ($selectedCategory == $cat['id'] || $selectedCategory === $cat['slug'] || $selectedCategory === $cat['name_en']);
                                            $queryParams = [];
                                            if (!empty($searchQuery)) $queryParams['q'] = $searchQuery;
                                            if (!empty($selectedSort)) $queryParams['sort'] = $selectedSort;
                                            $queryParams['category'] = $catSlug;
                                        ?>
                                        <a href="<?= base_url('wall-of-kot-sultan?' . http_build_query($queryParams)) ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 <?= $isActive ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800/50' : 'bg-white/80 dark:bg-slate-900/40 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700' ?>">
                                            <div class="flex items-center gap-3">
                                                <i data-lucide="<?= esc($cat['icon'] ?: 'user') ?>" class="w-4.5 h-4.5 <?= $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                                                <span class="text-[15px]"><?= esc($catName) ?></span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </nav>
                            </div>

                            <?php if ($extraCount > 0): ?>
                                <button type="button"
                                        @click="showAllCatsMobile = !showAllCatsMobile"
                                        class="w-full mb-2 h-11 rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50/70 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 font-bold text-sm hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors flex items-center justify-center gap-2">
                                    <i data-lucide="chevrons-down" class="w-4 h-4" x-show="!showAllCatsMobile"></i>
                                    <i data-lucide="chevrons-up" class="w-4 h-4" x-show="showAllCatsMobile" x-cloak></i>
                                    <span x-text="showAllCatsMobile ? '<?= $isUrdu ? 'کم دکھائیں' : 'Show less' ?>' : '<?= $isUrdu ? 'مزید دیکھنے کے لیے کلک کریں (' . $extraCount . ')' : 'Click to view more (' . $extraCount . ')' ?>'"></span>
                                </button>
                                <nav class="space-y-1.5" x-show="showAllCatsMobile" x-cloak x-transition>
                                    <?php foreach ($extraWallCats as $cat): ?>
                                        <?php
                                            $catSlug = $cat['slug'] ?: $cat['id'];
                                            $catName = $isUrdu ? ($cat['name_ur'] ?: $cat['name_en']) : ($cat['name_en'] ?: $cat['name_ur']);
                                            $isActive = ($selectedCategory == $cat['id'] || $selectedCategory === $cat['slug'] || $selectedCategory === $cat['name_en']);
                                            $queryParams = [];
                                            if (!empty($searchQuery)) $queryParams['q'] = $searchQuery;
                                            if (!empty($selectedSort)) $queryParams['sort'] = $selectedSort;
                                            $queryParams['category'] = $catSlug;
                                        ?>
                                        <a href="<?= base_url('wall-of-kot-sultan?' . http_build_query($queryParams)) ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 <?= $isActive ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800/50' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent' ?>">
                                            <div class="flex items-center gap-3">
                                                <i data-lucide="<?= esc($cat['icon'] ?: 'user') ?>" class="w-4.5 h-4.5 <?= $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                                                <span class="text-[15px]"><?= esc($catName) ?></span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Top Controls: Result Count & Sort Selector -->
                <div class="relative z-20 flex flex-col sm:flex-row justify-between items-center mb-6 bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-sm gap-4">
                    <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 font-semibold">
                        <span><?= lang('App.showing_results') ?>: <strong class="text-emerald-600 dark:text-emerald-400"><?= $totalResults ?></strong> <?= lang('App.wall_title') ?></span>
                    </div>

                    <!-- Sort Dropdown Form -->
                    <form action="<?= base_url('wall-of-kot-sultan') ?>" method="GET" class="flex items-center gap-3 w-full sm:w-auto">
                        <?php if (!empty($searchQuery)): ?>
                            <input type="hidden" name="q" value="<?= esc($searchQuery) ?>">
                        <?php endif; ?>
                        <?php if (!empty($selectedCategory) && $selectedCategory !== 'all'): ?>
                            <input type="hidden" name="category" value="<?= esc($selectedCategory) ?>">
                        <?php endif; ?>

                        <label class="text-sm font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap hidden sm:block"><?= lang('App.sort_label') ?>:</label>
                        
                        <div x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false" class="relative w-full sm:w-[220px]">
                            <input type="hidden" name="sort" value="<?= esc($selectedSort) ?>">
                            
                            <!-- Closed Box -->
                            <button type="button" @click="open = !open" 
                                    class="w-full flex items-center justify-between gap-3 h-[52px] px-4 bg-slate-50 dark:bg-slate-900 border transition-all duration-300 outline-none rounded-[16px] shadow-sm"
                                    :class="open ? 'border-emerald-500/40 shadow-[0_4px_20px_rgba(16,185,129,0.12)] ring-2 ring-emerald-500/10' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'">
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                    <?php
                                        $sortLabels = [
                                            'newest' => lang('App.sort_newest'),
                                            'featured' => lang('App.sort_featured'),
                                            'alphabetical' => lang('App.sort_alphabetical'),
                                            'views' => lang('App.sort_views'),
                                            'oldest' => lang('App.sort_oldest'),
                                        ];
                                        echo esc($sortLabels[$selectedSort] ?? $sortLabels['newest']);
                                    ?>
                                </span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="open ? 'rotate-180 text-emerald-500' : ''"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                 class="absolute rtl:right-0 ltr:left-0 top-[calc(100%+8px)] w-full min-w-[240px] bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/60 rounded-[16px] shadow-[0_12px_40px_-12px_rgba(0,0,0,0.15)] p-2 z-[100] max-h-[300px] overflow-y-auto"
                                 style="display: none;">
                                 
                                 <?php foreach ($sortLabels as $val => $label): ?>
                                    <?php $isSelected = ($selectedSort === $val); ?>
                                    <button type="button" 
                                            onclick="this.closest('form').querySelector('input[name=sort]').value = '<?= $val ?>'; this.closest('form').submit();"
                                            class="w-full flex items-center justify-between px-4 py-3 rounded-[12px] text-[15px] font-semibold transition-colors duration-150 <?= $isSelected ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50' ?>">
                                        <span><?= esc($label) ?></span>
                                        <?php if ($isSelected): ?>
                                            <i data-lucide="check" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                                        <?php endif; ?>
                                    </button>
                                 <?php endforeach; ?>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Personalities Grid -->
                <?php if (empty($wallEntries)): ?>
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700 max-w-xl mx-auto my-12 shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="user-x" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white"><?= lang('App.no_wall_entries') ?></h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-6"><?= lang('App.wall_empty_sub') ?></p>
                        <a href="<?= base_url('wall-of-kot-sultan') ?>" class="btn btn-md btn-primary">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            <span><?= lang('App.reset_filter') ?></span>
                        </a>
                    </div>
                <?php else: ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                        <?php foreach ($wallEntries as $item): ?>
                            <div class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-5 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between relative overflow-hidden transform hover:-translate-y-1">
                                
                                <!-- Card Body -->
                                <div>
                                    <!-- Header Photo & Category Badge -->
                                    <div class="flex items-start justify-between gap-3 mb-4">
                                        <div class="relative w-16 h-16 rounded-2xl overflow-hidden border-2 border-emerald-500/20 group-hover:border-emerald-500 transition-colors shrink-0 shadow-sm">
                                            <img src="<?= esc($item['photo_url']) ?>" 
                                                 alt="<?= esc($item['display_name']) ?>" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                 onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80'">
                                        </div>
                                        <div class="flex flex-col items-end gap-1">
                                            <?php if (!empty($item['featured'])): ?>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 flex items-center gap-1">
                                                    <i data-lucide="sparkles" class="w-3 h-3"></i>
                                                    <?= lang('App.featured_badge') ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($item['display_category'])): ?>
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center gap-1">
                                                    <i data-lucide="<?= esc($item['category_icon'] ?: 'user') ?>" class="w-3 h-3 text-emerald-600 dark:text-emerald-400"></i>
                                                    <?= esc($item['display_category']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Name & Profession -->
                                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-snug mb-1 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                        <?= esc($item['display_name']) ?>
                                    </h3>

                                    <?php if (!empty($item['display_profession'])): ?>
                                        <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mb-3 flex items-center gap-1.5">
                                            <i data-lucide="briefcase" class="w-3.5 h-3.5 shrink-0"></i>
                                            <span><?= esc($item['display_profession']) ?></span>
                                        </p>
                                    <?php endif; ?>

                                    <!-- Years of Service / Lifespan -->
                                    <?php if (!empty($item['years_of_service'])): ?>
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700/60 text-[11px] font-medium text-slate-600 dark:text-slate-300 mb-4">
                                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span><?= esc($item['years_of_service']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Short Intro -->
                                    <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed mb-5">
                                        <?= esc($item['display_intro']) ?>
                                    </p>
                                </div>

                                <!-- Action Button -->
                                <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                                    <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        <?= (int)($item['views'] ?? 0) ?>
                                    </span>

                                    <a href="<?= esc($item['url']) ?>" 
                                       class="btn btn-sm btn-primary py-2 px-4 rounded-xl flex items-center gap-1.5 font-bold shadow-xs">
                                        <span><?= lang('App.view_profile') ?></span>
                                        <i data-lucide="<?= $isUrdu ? 'arrow-left' : 'arrow-right' ?>" class="w-4 h-4"></i>
                                    </a>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination Controls -->
                    <?php if ($totalPages > 1): ?>
                        <?php $params = $_GET; ?>
                        <div class="flex items-center justify-center flex-wrap gap-2 pt-4">
                            <?php if ($currentPage > 1): ?>
                                <?php $params['page'] = $currentPage - 1; ?>
                                <a href="<?= base_url('wall-of-kot-sultan?' . http_build_query($params)) ?>" class="btn btn-sm btn-outline flex items-center gap-1">
                                    <i data-lucide="<?= $isUrdu ? 'chevron-right' : 'chevron-left' ?>" class="w-4 h-4"></i>
                                    <span><?= lang('App.pagination_previous') ?></span>
                                </a>
                            <?php endif; ?>

                            <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage   = min($totalPages, $currentPage + 2);
                                for ($p = $startPage; $p <= $endPage; $p++):
                                    $params['page'] = $p;
                                    $isActive = ($p === $currentPage);
                            ?>
                                <a href="<?= base_url('wall-of-kot-sultan?' . http_build_query($params)) ?>" 
                                   class="w-9 h-9 flex items-center justify-center rounded-xl text-xs font-bold transition-all <?= $isActive ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-emerald-500' ?>">
                                    <?= $p ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <?php $params['page'] = $currentPage + 1; ?>
                                <a href="<?= base_url('wall-of-kot-sultan?' . http_build_query($params)) ?>" class="btn btn-sm btn-outline flex items-center gap-1">
                                    <span><?= lang('App.pagination_next') ?></span>
                                    <i data-lucide="<?= $isUrdu ? 'chevron-left' : 'chevron-right' ?>" class="w-4 h-4"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </main>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
