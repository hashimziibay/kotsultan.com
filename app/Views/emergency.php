<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
    $isUrdu = ($lang === 'ur');
?>

<!-- Header Section -->
<div class="bg-gradient-to-b from-rose-50/60 to-slate-50 dark:from-slate-900 dark:to-slate-900 py-6 md:py-8 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-3xl mx-auto text-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 mb-3">
                <i data-lucide="siren" class="w-3.5 h-3.5"></i>
                <?= lang('App.emergency_badge') ?>
            </span>
            <h1 class="blur-reveal text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                <?= lang('App.emergency_title') ?>
            </h1>
            <p class="blur-reveal text-sm sm:text-base text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                <?= lang('App.emergency_subtitle') ?>
            </p>
        </div>

    </div>
</div>

<!-- Main Layout (Sidebar + Results) -->
<div class="py-6 bg-slate-50 dark:bg-slate-900/50 min-h-screen transition-colors duration-200" x-data="{ mobileFiltersOpen: false }">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Controls for Mobile -->
        <div class="lg:hidden flex items-center justify-between mb-4" dir="<?= $isUrdu ? 'rtl' : 'ltr' ?>">
            <button @click="mobileFiltersOpen = !mobileFiltersOpen" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700">
                <i data-lucide="filter" class="w-4 h-4"></i>
                <?= lang('App.filter_by_category') ?>
            </button>
            <div class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                <?= $totalResults ?> <?= lang('App.results_found') ?>
            </div>
        </div>

        <!-- Inline Mobile Filters (Expands Naturally) -->
        <div x-show="mobileFiltersOpen" 
             x-collapse
             class="lg:hidden mb-6" dir="<?= $isUrdu ? 'rtl' : 'ltr' ?>" style="display: none;">
             <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden p-4">
                 
                 <!-- Mobile Search -->
                 <form action="<?= base_url('emergency-numbers') ?>" method="GET" class="mb-4">
                     <div class="relative">
                         <input type="text" name="q" value="<?= esc($searchQuery ?? '') ?>" placeholder="<?= lang('App.search_emergency_placeholder') ?>" class="w-full pl-10 rtl:pr-10 rtl:pl-4 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:bg-white dark:focus:bg-slate-800 text-slate-900 dark:text-white transition-colors">
                         <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 rtl:right-3.5 rtl:left-auto top-3.5"></i>
                     </div>
                     <?php if (!empty($selectedCategory) && $selectedCategory !== 'all'): ?>
                         <input type="hidden" name="category" value="<?= esc($selectedCategory) ?>">
                     <?php endif; ?>
                 </form>

                 <!-- Mobile Categories List -->
                 <nav class="space-y-1">
                    <?php 
                        $isAllActive = ($selectedCategory === 'all' || empty($selectedCategory));
                        $allParams = [];
                        if (!empty($searchQuery)) $allParams['q'] = $searchQuery;
                        $allUrl = base_url('emergency-numbers' . (!empty($allParams) ? '?' . http_build_query($allParams) : ''));
                    ?>
                    <a href="<?= $allUrl ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-colors <?= $isAllActive ? 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 font-bold border border-rose-200 dark:border-rose-800/50' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border border-transparent' ?>">
                        <div class="flex items-center gap-3 min-w-0">
                            <i data-lucide="layers" class="w-4.5 h-4.5 shrink-0 <?= $isAllActive ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                            <span class="truncate"><?= lang('App.all_emergency_categories') ?></span>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full shrink-0 <?= $isAllActive ? 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' ?>">
                            <?= $allCount ?? 0 ?>
                        </span>
                    </a>
                    
                    <?php foreach ($categories as $cat): ?>
                        <?php 
                            $catName = $isUrdu ? ($cat['category_ur'] ?: $cat['category_en']) : ($cat['category_en'] ?: $cat['category_ur']);
                            $isActive = ($selectedCategory === $cat['category_en']);
                            $queryParams = [];
                            if (!empty($searchQuery)) $queryParams['q'] = $searchQuery;
                            $queryParams['category'] = $cat['category_en'];
                        ?>
                        <a href="<?= base_url('emergency-numbers?' . http_build_query($queryParams)) ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-colors <?= $isActive ? 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 font-bold border border-rose-200 dark:border-rose-800/50' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border border-transparent' ?>">
                            <div class="flex items-center gap-3 min-w-0">
                                <i data-lucide="<?= esc($cat['icon'] ?: 'phone-call') ?>" class="w-4.5 h-4.5 shrink-0 <?= $isActive ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 dark:text-slate-500' ?>"></i>
                                <span class="truncate"><?= esc($catName) ?></span>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full shrink-0 <?= $isActive ? 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' ?>">
                                <?= esc($cat['count'] ?? 0) ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                 </nav>
             </div>
        </div>

        <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-stretch" dir="<?= $isUrdu ? 'rtl' : 'ltr' ?>">
            
            <!-- LEFT SIDEBAR (Desktop) -->
            <aside class="hidden lg:block lg:col-span-3 h-full">
                <div class="bg-white dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden sticky top-24">
                    
                    <!-- Search Input -->
                    <div class="p-5 border-b border-slate-100 dark:border-slate-700/50">
                        <form action="<?= base_url('emergency-numbers') ?>" method="GET">
                            <div class="relative w-full">
                                <?php if (!empty($selectedCategory) && $selectedCategory !== 'all'): ?>
                                    <input type="hidden" name="category" value="<?= esc($selectedCategory) ?>">
                                <?php endif; ?>
                                <i data-lucide="search" class="w-4.5 h-4.5 text-slate-400 absolute left-3.5 rtl:right-3.5 rtl:left-auto top-3 shrink-0"></i>
                                <input type="text" 
                                       name="q" 
                                       value="<?= esc($searchQuery ?? '') ?>"
                                       placeholder="<?= lang('App.search_emergency_placeholder') ?>"
                                       class="w-full pl-10 rtl:pr-10 rtl:pl-4 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:bg-white dark:focus:bg-slate-800 text-slate-900 dark:text-white transition-colors">
                            </div>
                        </form>
                    </div>

                    <!-- Categories List -->
                    <div class="p-3">
                        <nav class="space-y-1">
                            <a href="<?= $allUrl ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-colors <?= $isAllActive ? 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white' ?>">
                                <div class="flex items-center gap-3 min-w-0">
                                    <i data-lucide="layers" class="w-4.5 h-4.5 shrink-0 <?= $isAllActive ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600' ?>"></i>
                                    <span class="text-sm truncate"><?= lang('App.all_emergency_categories') ?></span>
                                </div>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full shrink-0 <?= $isAllActive ? 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' ?>">
                                    <?= $allCount ?? 0 ?>
                                </span>
                            </a>
                            
                            <?php foreach ($categories as $cat): ?>
                                <?php 
                                    $catName = $isUrdu ? ($cat['category_ur'] ?: $cat['category_en']) : ($cat['category_en'] ?: $cat['category_ur']);
                                    $isActive = ($selectedCategory === $cat['category_en']);
                                    $queryParams = [];
                                    if (!empty($searchQuery)) $queryParams['q'] = $searchQuery;
                                    $queryParams['category'] = $cat['category_en'];
                                ?>
                                <a href="<?= base_url('emergency-numbers?' . http_build_query($queryParams)) ?>" class="group flex items-center justify-between px-3 py-2.5 rounded-xl transition-colors <?= $isActive ? 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white' ?>">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <i data-lucide="<?= esc($cat['icon'] ?: 'phone-call') ?>" class="w-4.5 h-4.5 shrink-0 <?= $isActive ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' ?>"></i>
                                        <span class="text-sm truncate"><?= esc($catName) ?></span>
                                    </div>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full shrink-0 <?= $isActive ? 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' ?>">
                                        <?= esc($cat['count'] ?? 0) ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <main class="lg:col-span-9">
                <!-- Toast Notification Box for Copy Action -->
                <div x-data="{ show: false, message: '' }" 
                     @copy-toast.window="message = $event.detail; show = true; setTimeout(() => show = false, 2500)"
                     x-show="show" 
                     x-transition
                     class="fixed bottom-6 right-6 z-50 bg-rose-600 text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-2 text-xs font-bold"
                     style="display: none;">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span x-text="message"></span>
                </div>

                <!-- Active Filters & Sorting (Desktop) -->
                <div class="hidden lg:flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                        <?php if (!empty($searchQuery)): ?>
                            <span><?= lang('App.search_results_for') ?>: </span>
                            <span class="font-bold text-slate-900 dark:text-white">"<?= esc($searchQuery) ?>"</span>
                        <?php else: ?>
                            <span class="font-semibold text-slate-900 dark:text-white">
                                <?php 
                                    if ($isAllActive) {
                                        echo lang('App.all_emergency_categories');
                                    } else {
                                        $activeName = $selectedCategory;
                                        foreach ($categories as $c) {
                                            if ($c['category_en'] === $selectedCategory) {
                                                $activeName = $isUrdu ? ($c['category_ur'] ?: $c['category_en']) : ($c['category_en'] ?: $c['category_ur']);
                                                break;
                                            }
                                        }
                                        echo esc($activeName);
                                    }
                                ?>
                            </span>
                        <?php endif; ?>
                        <span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400 rounded-md text-xs font-bold ml-2 rtl:ml-0 rtl:mr-2">
                            <?= $totalResults ?>
                        </span>
                    </div>

                    <?php if (!empty($searchQuery) || !$isAllActive): ?>
                    <a href="<?= base_url('emergency-numbers') ?>" class="text-sm font-medium text-rose-600 hover:text-rose-700 dark:text-rose-400 hover:underline">
                        <?= lang('App.clear_all_filters') ?>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Emergency Cards Grid -->
                <?php if (empty($contacts)): ?>
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700 max-w-xl mx-auto my-12 shadow-xs">
                        <div class="w-16 h-16 rounded-2xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="phone-off" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            <?= lang('App.no_emergency_contacts') ?>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-6">
                            <?= lang('App.no_emergency_contacts_sub') ?>
                        </p>
                        <a href="<?= base_url('emergency-numbers') ?>" class="btn btn-md bg-rose-600 hover:bg-rose-700 text-white font-bold border-none rounded-xl px-5 py-2.5 inline-flex items-center gap-2">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            <span><?= lang('App.reset_filter') ?></span>
                        </a>
                    </div>
                <?php else: ?>
                    
                    <!-- Cards Grid -->
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-5">
                        <?php foreach ($contacts as $contact): ?>
                            <?php 
                                $catName = $isUrdu ? ($contact['category_ur'] ?: $contact['category_en']) : ($contact['category_en'] ?: $contact['category_ur']);
                                $deptName = $isUrdu ? ($contact['department_name_ur'] ?: $contact['department_name_en']) : ($contact['department_name_en'] ?: $contact['department_name_ur']);
                                $icon = !empty($contact['icon']) ? $contact['icon'] : 'phone-call';
                            ?>
                            <div class="p-5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group hover:border-rose-500 transition-all shadow-sm hover:shadow-md">
                                
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                        <i data-lucide="<?= esc($icon) ?>" class="w-6 h-6"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-slate-900 dark:text-white text-sm sm:text-base leading-tight truncate">
                                            <?= esc($deptName) ?>
                                        </h3>
                                        <span class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-1 block truncate">
                                            <?= esc($catName) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center shrink-0">
                                    <?php if (!empty($contact['phone_primary'])): ?>
                                    <a href="tel:<?= esc(preg_replace('/[^0-9+]/', '', $contact['phone_primary'])) ?>" class="h-10 px-4 inline-flex items-center justify-center gap-2.5 border border-slate-200 dark:border-slate-700 hover:border-rose-500 dark:hover:border-rose-400 text-slate-700 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 rounded-xl text-sm font-semibold transition-colors shrink-0">
                                        <span dir="ltr" class="font-medium whitespace-nowrap tracking-wide"><?= esc($contact['phone_primary']) ?></span>
                                        <i data-lucide="phone" class="w-4 h-4 text-rose-600 dark:text-rose-400"></i>
                                    </a>
                                    <?php else: ?>
                                    <div class="h-10 px-4 inline-flex items-center justify-center gap-2.5 border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 rounded-xl text-sm font-semibold transition-colors shrink-0">
                                        <span class="font-medium whitespace-nowrap text-xs"><?= lang('App.not_available') ?></span>
                                        <i data-lucide="phone-off" class="w-4 h-4 opacity-50"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                <?php endif; ?>

            </main>
        </div>
    </div>
</div>

<?= $this->endSection() ?>