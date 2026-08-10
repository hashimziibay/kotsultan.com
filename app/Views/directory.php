<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
    $isUrdu = ($lang === 'ur');
?>

<!-- Header Section -->
<div class="bg-gradient-to-b from-emerald-50/60 to-slate-50 dark:from-slate-900 dark:to-slate-900 py-10 md:py-14 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-3xl mx-auto mb-8 text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-2 block">
                <?= lang('App.directory_badge') ?>
            </span>
            <h1 class="blur-reveal text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                <?= lang('App.nav_directory') ?>
            </h1>
            <p class="blur-reveal text-sm sm:text-base text-slate-600 dark:text-slate-400 mt-2">
                <?= lang('App.directory_subtitle') ?>
            </p>
        </div>

    </div>
</div>

<!-- Directory Main Layout (Sidebar + Results) -->
<div class="py-8 bg-slate-50 dark:bg-slate-900/50 min-h-screen transition-colors duration-200" x-data="{ mobileFiltersOpen: false }">
    
    <!-- Mobile Filter Slide-Over Drawer -->
    <div x-show="mobileFiltersOpen" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="mobileFiltersOpen" 
             x-transition.opacity 
             class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" 
             @click="mobileFiltersOpen = false"></div>
        
        <div x-show="mobileFiltersOpen" 
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="-translate-x-full rtl:translate-x-full" 
             x-transition:enter-end="translate-x-0" 
             x-transition:leave="transition ease-in duration-200 transform" 
             x-transition:leave-start="translate-x-0" 
             x-transition:leave-end="-translate-x-full rtl:translate-x-full" 
             class="fixed inset-y-0 left-0 rtl:right-0 rtl:left-auto w-full max-w-[280px] bg-white dark:bg-slate-900 shadow-2xl overflow-y-auto">
             
             <div class="px-5 py-6">
                 <div class="flex items-center justify-between mb-6">
                     <h2 class="text-lg font-extrabold text-slate-900 dark:text-white uppercase tracking-wider"><?= lang('App.popular_categories') ?></h2>
                     <button @click="mobileFiltersOpen = false" type="button" class="p-2 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-full transition-colors">
                         <i data-lucide="x" class="w-5 h-5"></i>
                     </button>
                 </div>
                 
                 <!-- Mobile Categories List -->
                 <nav class="space-y-1.5">
                    <?php 
                        $allCount = array_sum($categoryTotals);
                        $isAllActive = empty($selectedCategory);
                    ?>
                    <a href="<?= base_url('directory' . (!empty($searchQuery) ? '?q=' . urlencode($searchQuery) : '')) ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-colors <?= $isAllActive ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800/50' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent' ?>">
                        <div class="flex items-center gap-3">
                            <i data-lucide="layout-grid" class="w-4.5 h-4.5 <?= $isAllActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                            <span><?= lang('App.all_categories') ?></span>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full <?= $isAllActive ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' ?>">
                            <?= $allCount ?>
                        </span>
                    </a>
                    
                    <?php foreach ($categories as $cat): ?>
                        <?php 
                            $isActive = ((string) $selectedCategory === (string) $cat['id']);
                            $catCount = $categoryTotals[$cat['id']] ?? 0;
                            $catUrl = $cat['url'] ?? base_url('directory/' . ($cat['seo_slug'] ?? $cat['slug']));
                            if (!empty($searchQuery)) {
                                $catUrl .= (str_contains($catUrl, '?') ? '&' : '?') . 'q=' . urlencode($searchQuery);
                            }
                        ?>
                        <a href="<?= esc($catUrl) ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-colors <?= $isActive ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800/50' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent' ?>">
                            <div class="flex items-center gap-3">
                                <i data-lucide="<?= !empty($cat['icon']) ? $cat['icon'] : 'folder' ?>" class="w-4.5 h-4.5 <?= $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                                <span><?= esc($cat['display_name']) ?></span>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full <?= $isActive ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' ?>">
                                <?= $catCount ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                 </nav>
             </div>
        </div>
    </div>


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-stretch" dir="<?= $isUrdu ? 'rtl' : 'ltr' ?>">
            
            <!-- LEFT SIDEBAR (Desktop) -->
            <aside class="hidden lg:block lg:col-span-3 h-full">
                <div class="bg-white dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col h-full overflow-hidden">
                    
                    <!-- Search Section -->
                    <div class="p-5 border-b border-slate-100 dark:border-slate-700/50" dir="<?= $isUrdu ? 'rtl' : 'ltr' ?>">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-4"><?= lang('App.search_button') ?></h3>
                        <?php
                            $searchAction = base_url('directory');
                            foreach ($categories as $c) {
                                if ((string) $selectedCategory === (string) $c['id']) {
                                    $searchAction = $c['url'] ?? $searchAction;
                                    break;
                                }
                            }
                        ?>
                        <form action="<?= esc($searchAction) ?>" method="GET" class="flex flex-col gap-3">
                            <div class="relative">
                                <i data-lucide="search" class="search-field-icon w-5 h-5 text-slate-400 absolute top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input type="text" 
                                       name="q" 
                                       value="<?= esc($searchQuery ?? '') ?>" 
                                       placeholder="<?= lang('App.search_placeholder') ?>" 
                                       class="search-field-input w-full h-12 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 text-sm font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all">
                            </div>
                            
                            <button type="submit" class="w-full h-11 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all focus:ring-2 focus:ring-emerald-500/20 flex items-center justify-center">
                                <?= lang('App.search_button') ?>
                            </button>
                            
                            <?php if (!empty($searchQuery) || !empty($selectedCategory)): ?>
                                <a href="<?= base_url('directory') ?>" class="w-full h-11 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                    <?= lang('App.reset_filter') ?>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- Categories Section -->
                    <div class="pt-5 pb-3 px-5 border-b border-slate-100 dark:border-slate-700/50" dir="<?= $isUrdu ? 'rtl' : 'ltr' ?>">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400"><?= lang('App.categories') ?? 'Categories' ?></h3>
                    </div>
                    
                    <div class="p-4" dir="<?= $isUrdu ? 'rtl' : 'ltr' ?>">
                        <nav class="space-y-1.5">
                            <a href="<?= base_url('directory' . (!empty($searchQuery) ? '?q=' . urlencode($searchQuery) : '')) ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 <?= $isAllActive ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800/50' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent' ?>">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="layout-grid" class="w-4.5 h-4.5 <?= $isAllActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                                    <span class="text-[15px]"><?= lang('App.all_categories') ?></span>
                                </div>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full <?= $isAllActive ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' ?>">
                                    <?= $allCount ?>
                                </span>
                            </a>
                            
                            <?php foreach ($categories as $cat): ?>
                                <?php 
                                    $isActive = ((string) $selectedCategory === (string) $cat['id']);
                                    $catCount = $categoryTotals[$cat['id']] ?? 0;
                                    $catUrl = $cat['url'] ?? base_url('directory/' . ($cat['seo_slug'] ?? $cat['slug']));
                                    if (!empty($searchQuery)) {
                                        $catUrl .= (str_contains($catUrl, '?') ? '&' : '?') . 'q=' . urlencode($searchQuery);
                                    }
                                ?>
                                <a href="<?= esc($catUrl) ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 <?= $isActive ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800/50' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent' ?>">
                                    <div class="flex items-center gap-3">
                                        <i data-lucide="<?= !empty($cat['icon']) ? $cat['icon'] : 'folder' ?>" class="w-4.5 h-4.5 <?= $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                                        <span class="text-[15px]"><?= esc($cat['display_name']) ?></span>
                                    </div>
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full <?= $isActive ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' ?>">
                                        <?= $catCount ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                </div>
            </aside>

            <!-- RIGHT CONTENT AREA (Search + Results) -->
            <main class="lg:col-span-9" dir="<?= $isUrdu ? 'rtl' : 'ltr' ?>">
                
                <!-- Mobile Toggle & Search Bar (Hidden on Desktop) -->
                <div class="mb-6 lg:hidden space-y-4">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="mobileFiltersOpen = true" class="flex-shrink-0 flex items-center justify-center w-14 h-14 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-700 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            <i data-lucide="filter" class="w-5 h-5"></i>
                        </button>
                        
                        <form action="<?= base_url('directory') ?>" method="GET" class="flex-grow flex gap-3 relative">
                            <?php if (!empty($selectedCategory)): ?>
                                <input type="hidden" name="category" value="<?= esc($selectedCategory) ?>">
                            <?php endif; ?>
                            
                            <div class="relative flex-grow">
                                <i data-lucide="search" class="search-field-icon w-5 h-5 text-slate-400 absolute top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input type="text" 
                                       name="q" 
                                       value="<?= esc($searchQuery ?? '') ?>" 
                                       placeholder="<?= lang('App.search_placeholder') ?>" 
                                       class="search-field-input w-full h-14 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                            </div>
                            
                            <button type="submit" class="flex-shrink-0 px-6 h-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold shadow-sm transition-all focus:ring-2 focus:ring-emerald-500/20 hidden sm:block">
                                <?= lang('App.search_button') ?>
                            </button>
                            
                            <?php if (!empty($searchQuery) || !empty($selectedCategory)): ?>
                                <a href="<?= base_url('directory') ?>" class="flex-shrink-0 flex items-center justify-center w-14 h-14 bg-white hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-2xl border border-slate-200 dark:border-slate-700 transition-all shadow-sm" title="<?= lang('App.reset_filter') ?>">
                                    <i data-lucide="rotate-ccw" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Directory Grid Results -->
                <?php if (empty($businesses)): ?>
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-slate-200 dark:border-slate-700 my-8">
                        <i data-lucide="search-x" class="w-12 h-12 text-slate-400 mx-auto mb-3"></i>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1"><?= lang('App.no_results') ?></h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-6"><?= lang('App.no_results_sub') ?></p>
                        <a href="<?= base_url('directory') ?>" class="btn btn-md btn-primary">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            <span><?= lang('App.reset_filter') ?></span>
                        </a>
                    </div>
                <?php else: ?>
                    <?php 
                        $totalResults = $totalResults ?? count($businesses);
                        $currentPage  = $currentPage ?? 1;
                        $totalPages   = $totalPages ?? 1;
                        $perPage      = $perPage ?? 18;
                        $startItem    = max(1, ($currentPage - 1) * $perPage + 1);
                        $endItem      = min($totalResults, $currentPage * $perPage);
                    ?>
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 text-xs text-slate-500 dark:text-slate-400 font-semibold gap-2 px-1">
                        <span><?= lang('App.showing_results') ?>: <?= $startItem ?>-<?= $endItem ?> <?= lang('App.pagination_of') ?> <?= $totalResults ?></span>
                        <?php if ($totalPages > 1): ?>
                        <span><?= lang('App.pagination_page') ?> <?= $currentPage ?> <?= lang('App.pagination_of') ?> <?= $totalPages ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                        <?php foreach ($businesses as $item): ?>
                            <?= view('components/business_card', ['item' => $item, 'isUrdu' => $isUrdu]) ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <?php
                            $directoryBaseUrl = $directoryBaseUrl ?? base_url('directory');
                            $pageQuery = [];
                            if (! empty($searchQuery)) {
                                $pageQuery['q'] = $searchQuery;
                            }
                            $buildPageUrl = static function (int $pageNum) use ($directoryBaseUrl, $pageQuery): string {
                                $q = $pageQuery;
                                if ($pageNum > 1) {
                                    $q['page'] = $pageNum;
                                }
                                return $directoryBaseUrl . ($q ? ('?' . http_build_query($q)) : '');
                            };
                        ?>
                        <div class="flex items-center justify-center flex-wrap gap-2 pt-4">
                            <?php if ($currentPage > 1): ?>
                                <a href="<?= esc($buildPageUrl($currentPage - 1)) ?>" class="btn btn-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 flex items-center gap-1 hover:border-emerald-500 dark:hover:border-emerald-500">
                                    <i data-lucide="<?= $isUrdu ? 'chevron-right' : 'chevron-left' ?>" class="w-4 h-4"></i>
                                    <span><?= lang('App.pagination_previous') ?></span>
                                </a>
                            <?php endif; ?>

                            <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage   = min($totalPages, $currentPage + 2);
                                for ($p = $startPage; $p <= $endPage; $p++):
                                    $isActive = ($p === $currentPage);
                            ?>
                                <a href="<?= esc($buildPageUrl($p)) ?>" 
                                   class="w-9 h-9 flex items-center justify-center rounded-xl text-xs font-bold transition-all <?= $isActive ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-emerald-500' ?>">
                                    <?= $p ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="<?= esc($buildPageUrl($currentPage + 1)) ?>" class="btn btn-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 flex items-center gap-1 hover:border-emerald-500 dark:hover:border-emerald-500">
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
