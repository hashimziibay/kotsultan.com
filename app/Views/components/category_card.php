<?php
/**
 * Reusable Category Card Component
 * Expects: $cat (array), $isUrdu (bool), $count (int)
 */
?>
<a href="<?= !empty($cat['url']) ? esc($cat['url']) : base_url('directory?category=' . (!empty($cat['slug']) ? $cat['slug'] : $cat['id'])) ?>" 
   class="group p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-emerald-50/50 dark:hover:bg-slate-800 hover:border-emerald-500 dark:hover:border-emerald-500 transition-all duration-200 transform hover:-translate-y-1 hover:shadow-md text-center flex flex-col items-center">
    <div class="w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
        <i data-lucide="<?= !empty($cat['icon']) ? esc($cat['icon']) : 'folder' ?>" class="w-6 h-6"></i>
    </div>
    <h3 class="font-bold text-slate-900 dark:text-white text-sm sm:text-base mb-1 group-hover:text-emerald-600 transition-colors">
        <?= render_localized_text($cat['display_name']) ?>
    </h3>
    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">
        <?= isset($count) ? $count : 0 ?> <?= lang('App.listings_count') ?>
    </span>
</a>
