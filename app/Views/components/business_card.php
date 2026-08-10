<?php
/**
 * Reusable Business Card Component
 * Expects: $item (array), $isUrdu (bool)
 */
?>
<div class="business-card bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80 overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 transform hover:-translate-y-1 flex flex-col justify-between group">
    <div>
        <!-- Photo Header -->
        <div class="relative h-44 bg-slate-100 dark:bg-slate-700 overflow-hidden">
            <?php $cardImage = get_business_image_url($item['image'] ?? ''); ?>
            <img src="<?= esc($cardImage) ?>" 
                 loading="lazy" decoding="async"
                 onerror="this.onerror=null;this.src='<?= esc('https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&q=80&w=600') ?>';"
                 alt="<?= esc($item['display_name']) ?>" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            <div class="absolute top-3 left-3 rtl:left-auto rtl:right-3">
                <span class="business-card-badge px-2.5 py-1 rounded-md bg-slate-900/80 text-white text-xs font-bold uppercase tracking-wider backdrop-blur-xs">
                    <?= render_localized_text($item['display_category_name']) ?>
                </span>
            </div>
        </div>

        <!-- Business Details -->
        <div class="business-card-body p-4 space-y-1.5">
            <h3 class="business-card-title text-lg font-bold text-slate-900 dark:text-white leading-snug group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                <a href="<?= esc($item['url'] ?? base_url('listing/' . (!empty($item['slug']) ? $item['slug'] : $item['id']))) ?>">
                    <?= render_localized_text($item['display_name']) ?>
                </a>
            </h3>
            
            <?php if (!empty($item['owner_name'])): ?>
            <p class="business-card-meta text-xs font-medium text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                <span><?= lang('App.owner') ?>: <?= render_localized_text($item['owner_name']) ?></span>
            </p>
            <?php endif; ?>

            <?php if (!empty($item['display_address'])): ?>
            <p class="business-card-meta text-xs text-slate-600 dark:text-slate-300 flex items-start gap-1.5">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600 mt-0.5 flex-shrink-0"></i>
                <span class="line-clamp-2"><?= render_localized_text($item['display_address']) ?></span>
            </p>
            <?php endif; ?>

            <?php if (!empty($item['phone'])): ?>
            <p class="business-card-meta text-xs text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                <i data-lucide="phone" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0"></i>
                <span dir="ltr" class="font-mono"><?= esc($item['phone']) ?></span>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actions (Call, WhatsApp, View Details) -->
    <div class="business-card-actions p-4 pt-0 grid grid-cols-2 gap-2 mt-3 border-t border-slate-100 dark:border-slate-700/60 pt-3">
        <a href="tel:<?= esc($item['phone']) ?>" class="btn btn-sm btn-outline">
            <i data-lucide="phone" class="w-3.5 h-3.5 text-emerald-600"></i>
            <span><?= lang('App.call_now') ?></span>
        </a>
        
        <?php if (!empty($item['whatsapp'])): ?>
        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $item['whatsapp']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-success">
            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
            <span><?= lang('App.whatsapp') ?></span>
        </a>
        <?php else: ?>
        <a href="<?= esc($item['url'] ?? base_url('listing/' . (!empty($item['slug']) ? $item['slug'] : $item['id']))) ?>" class="btn btn-sm btn-secondary">
            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
            <span><?= lang('App.view_details') ?></span>
        </a>
        <?php endif; ?>
    </div>
</div>
