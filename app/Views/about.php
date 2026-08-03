<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Header Section -->
<section class="relative bg-gradient-to-b from-emerald-50/50 to-slate-50 dark:from-slate-900 dark:to-slate-900 py-12 md:py-20 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 text-xs font-bold uppercase tracking-wider mb-3">
            <i data-lucide="info" class="w-3.5 h-3.5"></i> <?= lang('App.about_badge') ?>
        </span>
        <h1 class="blur-reveal text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-4">
            <?= lang('App.about_title') ?>
        </h1>
        <p class="blur-reveal text-base sm:text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed">
            <?= lang('App.about_text') ?>
        </p>
    </div>
</section>


<!-- Mission & Vision Cards -->
<section class="py-12 md:py-16 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Mission Card -->
            <div class="p-8 rounded-2xl bg-slate-50/70 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700/80 space-y-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="target" class="w-6 h-6"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                    <?= lang('App.mission_title') ?>
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <?= lang('App.mission_text') ?>
                </p>
            </div>

            <!-- Vision Card -->
            <div class="p-8 rounded-2xl bg-slate-50/70 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700/80 space-y-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="compass" class="w-6 h-6"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                    <?= lang('App.vision_title') ?>
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <?= lang('App.vision_text') ?>
                </p>
            </div>

        </div>

    </div>
</section>


<!-- History & Town Information -->
<section class="py-12 md:py-16 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-3xl mx-auto text-center space-y-4 mb-12">
            <h2 class="blur-reveal text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                <?= lang('App.history_title') ?>
            </h2>
            <p class="blur-reveal text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                <?= lang('App.history_subtitle') ?>
            </p>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700/80 space-y-3">
                <i data-lucide="map-pin" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                <h4 class="font-bold text-slate-900 dark:text-white text-base"><?= lang('App.location_card_title') ?></h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    <?= lang('App.location_card_desc') ?>
                </p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700/80 space-y-3">
                <i data-lucide="store" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                <h4 class="font-bold text-slate-900 dark:text-white text-base"><?= lang('App.bazaars_card_title') ?></h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    <?= lang('App.bazaars_card_desc') ?>
                </p>
            </div>

            <div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700/80 space-y-3">
                <i data-lucide="graduation-cap" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                <h4 class="font-bold text-slate-900 dark:text-white text-base"><?= lang('App.services_card_title') ?></h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    <?= lang('App.services_card_desc') ?>
                </p>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection() ?>
