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

        <!-- Filter & Search Bar with BorderGlow -->
        <form action="<?= base_url('directory') ?>" method="GET" class="bg-white dark:bg-slate-800 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                
                <!-- Search Input -->
                <div class="md:col-span-6 relative">
                    <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-4 rtl:right-4 rtl:left-auto top-1/2 -translate-y-1/2"></i>
                    <input type="text" 
                           name="q" 
                           value="<?= esc($searchQuery ?? '') ?>" 
                           placeholder="<?= lang('App.search_placeholder') ?>" 
                           class="w-full pl-11 pr-4 rtl:pr-11 rtl:pl-4 h-14 bg-slate-50 hover:bg-white dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                </div>

                <!-- Category Filter -->
                <div class="md:col-span-4 relative">
                    <?php
                        $categoryOptions = [];
                        $categoryOptions[] = [
                            'value' => '',
                            'label' => lang('App.all_categories'),
                            'icon'  => 'layout-grid'
                        ];
                        foreach ($categories as $cat) {
                            $categoryOptions[] = [
                                'value' => (string)$cat['id'],
                                'label' => $isUrdu ? $cat['name_ur'] : $cat['name_en'],
                                'icon'  => !empty($cat['icon']) ? $cat['icon'] : 'folder'
                            ];
                        }
                    ?>
                    <?= view('components/custom_select', [
                        'name' => 'category',
                        'options' => $categoryOptions,
                        'selected' => $selectedCategory ?? '',
                        'placeholder' => lang('App.all_categories'),
                        'searchable' => true
                    ]) ?>
                </div>

                <!-- Submit Button -->
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 h-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold shadow-sm transition-all focus:ring-2 focus:ring-emerald-500/20">
                        <i data-lucide="filter" class="w-5 h-5"></i>
                        <span><?= lang('App.filter_button') ?></span>
                    </button>
                    <?php if (!empty($searchQuery) || !empty($selectedCategory)): ?>
                        <a href="<?= base_url('directory') ?>" class="flex-shrink-0 flex items-center justify-center w-14 h-14 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-2xl border border-slate-200 dark:border-slate-700 transition-all shadow-sm" title="<?= lang('App.reset_filter') ?>">
                            <i data-lucide="rotate-ccw" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>



    </div>
</div>


<!-- Directory Grid Results -->
<div class="py-10 bg-slate-50 dark:bg-slate-900/50 min-h-screen transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?php if (empty($businesses)): ?>
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-slate-200 dark:border-slate-700 my-8 max-w-xl mx-auto">
                <i data-lucide="search-x" class="w-12 h-12 text-slate-400 mx-auto mb-3"></i>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1"><?= lang('App.no_results') ?></h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6"><?= lang('App.no_results_sub') ?></p>
                <a href="<?= base_url('directory') ?>" class="btn btn-md btn-primary">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    <span><?= lang('App.reset_filter') ?></span>
                </a>
            </div>
        <?php else: ?>
            <div class="flex justify-between items-center mb-6 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                <span><?= lang('App.showing_results') ?>: <?= count($businesses) ?></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($businesses as $item): ?>
                    <?= view('components/business_card', ['item' => $item, 'isUrdu' => $isUrdu]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>
