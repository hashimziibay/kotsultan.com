<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
    $isUrdu = ($lang === 'ur');
?>

<!-- Profile Hero Section -->
<div class="py-10 md:py-14 bg-gradient-to-b from-emerald-50/60 to-slate-50 dark:from-slate-900 dark:to-slate-900 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Back Button -->
        <a href="<?= base_url('wall-of-kot-sultan') ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-6 transition-colors">
            <i data-lucide="<?= $isUrdu ? 'arrow-right' : 'arrow-left' ?>" class="w-4 h-4"></i>
            <span><?= lang('App.wall_title') ?></span>
        </a>

        <!-- Hero Card Header -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8 shadow-xl flex flex-col md:flex-row gap-6 md:gap-8 items-center md:items-start">
            
            <!-- Personality Image -->
            <div class="relative w-40 h-40 sm:w-48 sm:h-48 rounded-2xl overflow-hidden border-4 border-emerald-500/20 shrink-0 shadow-lg">
                <img src="<?= esc($item['photo_url']) ?>" 
                     alt="<?= esc($item['display_name']) ?>" 
                     class="w-full h-full object-cover"
                     onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80'">
            </div>

            <!-- Basic Details -->
            <div class="flex-grow text-center md:text-start">
                
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-3">
                    <?php if (!empty($item['featured'])): ?>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 flex items-center gap-1">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                            <?= lang('App.featured_badge') ?>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($item['display_category'])): ?>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center gap-1.5 border border-slate-200 dark:border-slate-600">
                            <i data-lucide="<?= esc($item['category_icon'] ?: 'user') ?>" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                            <?= esc($item['display_category']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-snug mb-2">
                    <?= esc($item['display_name']) ?>
                </h1>

                <?php if (!empty($item['display_profession'])): ?>
                    <p class="text-base font-bold text-emerald-600 dark:text-emerald-400 mb-4 flex items-center justify-center md:justify-start gap-2">
                        <i data-lucide="briefcase" class="w-4 h-4 shrink-0"></i>
                        <span><?= esc($item['display_profession']) ?></span>
                    </p>
                <?php endif; ?>

                <!-- Metadata Pills -->
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 text-xs text-slate-600 dark:text-slate-400 font-medium">
                    <?php if (!empty($item['years_of_service'])): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700/60">
                            <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                            <strong><?= lang('App.years_active') ?>:</strong> <?= esc($item['years_of_service']) ?>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($item['birth_date'])): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700/60">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                            <strong><?= lang('App.life_span') ?>:</strong> <?= esc($item['birth_date']) ?><?= !empty($item['death_date']) ? ' - ' . esc($item['death_date']) : '' ?>
                        </span>
                    <?php endif; ?>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700/60">
                        <i data-lucide="eye" class="w-4 h-4 text-slate-400"></i>
                        <?= (int)($item['views'] ?? 0) ?> <?= lang('App.views_count') ?>
                    </span>
                </div>

            </div>

        </div>

    </div>
</div>

<!-- Main Profile Details Content -->
<div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen transition-colors duration-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Full Biography -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2 border-b border-slate-100 dark:border-slate-700/80 pb-3">
                <i data-lucide="file-text" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                <span><?= lang('App.biography') ?></span>
            </h2>
            <div class="text-slate-700 dark:text-slate-300 text-sm sm:text-base leading-relaxed space-y-4 whitespace-pre-line">
                <?= esc($item['display_intro']) ?>
            </div>
        </div>

        <!-- Achievements & Contributions -->
        <?php if (!empty($item['display_achievements'])): ?>
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2 border-b border-slate-100 dark:border-slate-700/80 pb-3">
                <i data-lucide="award" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                <span><?= lang('App.achievements') ?></span>
            </h2>
            <div class="text-slate-700 dark:text-slate-300 text-sm sm:text-base leading-relaxed space-y-4 whitespace-pre-line">
                <?= esc($item['display_achievements']) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Awards & Honors -->
        <?php if (!empty($item['display_awards'])): ?>
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2 border-b border-slate-100 dark:border-slate-700/80 pb-3">
                <i data-lucide="trophy" class="w-5 h-5 text-amber-500"></i>
                <span><?= lang('App.awards') ?></span>
            </h2>
            <div class="text-slate-700 dark:text-slate-300 text-sm sm:text-base leading-relaxed space-y-4 whitespace-pre-line">
                <?= esc($item['display_awards']) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Related Personalities Section -->
        <?php if (!empty($related)): ?>
        <div class="pt-6 border-t border-slate-200 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <i data-lucide="users" class="w-5 h-5 text-emerald-600"></i>
                <span><?= lang('App.related_personalities') ?></span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php foreach ($related as $rel): ?>
                    <a href="<?= esc($rel['url']) ?>" class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-4 shadow-xs hover:shadow-lg transition-all flex items-center gap-4">
                        <img src="<?= esc($rel['photo_url']) ?>" 
                             alt="<?= esc($rel['display_name']) ?>" 
                             class="w-14 h-14 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0 group-hover:scale-105 transition-transform"
                             onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80'">
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 transition-colors truncate">
                                <?= esc($rel['display_name']) ?>
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                <?= esc($rel['display_profession'] ?: $rel['display_category']) ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>
