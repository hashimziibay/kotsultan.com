<?php
/**
 * Reusable Wall of Kot Sultan Component
 * Expects: $wallEntries (array), $isUrdu (bool), $mode ('preview' | 'full')
 */
$mode = $mode ?? 'preview';
$displayEntries = ($mode === 'preview') ? array_slice($wallEntries ?? [], 0, 6) : ($wallEntries ?? []);
?>

<?php if ($mode === 'preview'): ?>
<!-- PREVIEW MODE (For Home Page) -->
<section class="py-16 md:py-24 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200 relative overflow-hidden">
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col items-center text-center mb-12 gap-3">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 text-xs font-bold uppercase tracking-wider">
                <i data-lucide="award" class="w-3.5 h-3.5"></i> <?= lang('App.wall_badge') ?>
            </div>
            <h2 class="blur-reveal text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                <?= lang('App.wall_title') ?>
            </h2>
            <p class="blur-reveal text-sm sm:text-base text-slate-500 dark:text-slate-400 max-w-2xl">
                <?= lang('App.wall_subtitle') ?>
            </p>
            <a href="<?= base_url('wall-of-kot-sultan') ?>" class="btn btn-sm btn-ghost text-emerald-600 dark:text-emerald-400 mt-2">
                <span><?= lang('App.view_all') ?></span> &rarr;
            </a>
        </div>

        <?php if (empty($displayEntries)): ?>
            <!-- Clean Empty State -->
            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700 max-w-lg mx-auto">
                <i data-lucide="user-round-x" class="w-10 h-10 text-slate-400 mx-auto mb-3"></i>
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-1">
                    <?= lang('App.no_wall_entries') ?>
                </h3>
            </div>
        <?php else: ?>
            <!-- Apple-Style Horizontal Story Carousel / Grid -->
            <div class="overflow-x-auto hide-scrollbar pb-6 -mx-4 px-4 sm:mx-0 sm:px-0">
                <div class="flex gap-6 min-w-max md:min-w-0 md:grid md:grid-cols-3">
                    <?php foreach ($displayEntries as $person): ?>
                    <div class="w-80 md:w-auto bg-slate-50/80 dark:bg-slate-800/80 rounded-2xl border border-slate-200/90 dark:border-slate-700 p-8 flex flex-col items-center text-center backdrop-blur-xs transition-all duration-300 transform hover:-translate-y-1.5 hover:shadow-xl hover:border-emerald-500/50">
                        <div class="relative mb-6">
                            <img src="<?= !empty($person['photo']) ? esc($person['photo']) : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=300' ?>" 
                                 alt="<?= esc($person['display_name']) ?>" 
                                 class="w-28 h-28 rounded-full object-contain bg-slate-100 dark:bg-slate-700 border-4 border-white dark:border-slate-700 shadow-md">
                            <span class="absolute bottom-0 right-0 rtl:right-auto rtl:left-0 w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs shadow-xs">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </span>
                        </div>
                        
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-1">
                            <?= esc($person['display_name']) ?>
                        </h3>
                        
                        <span class="inline-block px-3 py-1.5 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-xs font-bold mb-4 max-w-full whitespace-pre-wrap break-words leading-relaxed text-start">
                            <?= lang('App.years_service') ?>: <?= esc($person['years_of_service']) ?>
                        </span>
                        
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal line-clamp-4">
                            <?= esc($person['display_intro']) ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php else: ?>
<!-- FULL MODE (For Dedicated Page /wall-of-kot-sultan) -->
<div class="py-12 md:py-16 bg-slate-50 dark:bg-slate-900/50 transition-colors duration-200 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?php if (empty($displayEntries)): ?>
            <!-- Clean Empty State for Dedicated Page -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-slate-200 dark:border-slate-700 max-w-xl mx-auto shadow-xs my-8">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="award" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                    <?= lang('App.no_wall_entries') ?>
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                    <?= lang('App.wall_empty_sub') ?>
                </p>
                <a href="<?= base_url('volunteer') ?>" class="btn btn-md btn-primary">
                    <i data-lucide="hand-heart" class="w-4 h-4"></i>
                    <span><?= lang('App.recommend_member') ?></span>
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($displayEntries as $person): ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/90 dark:border-slate-700 p-8 flex flex-col items-center text-center shadow-xs transition-all duration-300 transform hover:-translate-y-1.5 hover:shadow-xl hover:border-emerald-500/50">
                    <div class="relative mb-6">
                        <img src="<?= !empty($person['photo']) ? esc($person['photo']) : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=300' ?>" 
                             alt="<?= esc($person['display_name']) ?>" 
                             class="w-28 h-28 rounded-full object-contain bg-slate-100 dark:bg-slate-700 border-4 border-white dark:border-slate-700 shadow-md">
                        <span class="absolute bottom-0 right-0 rtl:right-auto rtl:left-0 w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs shadow-xs">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-1">
                        <?= esc($person['display_name']) ?>
                    </h3>
                    
                    <span class="inline-block px-3 py-1.5 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-xs font-bold mb-4 max-w-full whitespace-pre-wrap break-words leading-relaxed text-start">
                        <?= lang('App.years_service') ?>: <?= esc($person['years_of_service']) ?>
                    </span>
                    
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                        <?= esc($person['display_intro']) ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>
