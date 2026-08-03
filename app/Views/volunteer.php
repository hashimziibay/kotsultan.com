<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
    $whyVolunteer = [
        [
            'icon'  => 'heart-handshake',
            'title' => lang('App.help_community'),
            'desc'  => lang('App.help_community_desc'),
        ],
        [
            'icon'  => 'book-open-check',
            'title' => lang('App.improve_info'),
            'desc'  => lang('App.improve_info_desc'),
        ],
        [
            'icon'  => 'store',
            'title' => lang('App.support_businesses'),
            'desc'  => lang('App.support_businesses_desc'),
        ],
        [
            'icon'  => 'landmark',
            'title' => lang('App.preserve_history'),
            'desc'  => lang('App.preserve_history_desc'),
        ],
        [
            'icon'  => 'users',
            'title' => lang('App.make_info_easier'),
            'desc'  => lang('App.make_info_easier_desc'),
        ],
    ];

    $howToHelp = [
        ['icon' => 'plus-circle',  'label' => lang('App.add_missing_businesses')],
        ['icon' => 'pencil-line',  'label' => lang('App.update_business_info')],
        ['icon' => 'flag',         'label' => lang('App.report_incorrect_details')],
        ['icon' => 'layout-grid',  'label' => lang('App.suggest_new_categories')],
        ['icon' => 'scroll-text',  'label' => lang('App.share_history_info')],
        ['icon' => 'languages',    'label' => lang('App.translate_eng_urdu')],
        ['icon' => 'map-pinned',   'label' => lang('App.recommend_places')],
        ['icon' => 'badge-check',  'label' => lang('App.verify_locations')],
    ];
?>

<!-- 1. Hero Section -->
<section class="relative bg-gradient-to-b from-emerald-50/50 to-slate-50 dark:from-slate-900 dark:to-slate-900 py-12 md:py-20 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 text-xs font-bold uppercase tracking-wider mb-3">
            <i data-lucide="hand-heart" class="w-3.5 h-3.5"></i>
            <?= lang('App.volunteer_badge') ?>
        </span>
        <h1 class="blur-reveal text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-4">
            <?= lang('App.volunteer_hero_title') ?>
        </h1>
        <p class="blur-reveal text-base sm:text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed mb-8">
            <?= lang('App.volunteer_hero_subtitle') ?>
        </p>
        <a href="#volunteer-form" class="btn btn-lg btn-primary">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span><?= lang('App.join_as_volunteer') ?></span>
        </a>
    </div>
</section>


<!-- 2. Why Volunteer? -->
<section class="py-12 md:py-16 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center space-y-3 mb-10">
            <h2 class="blur-reveal text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                <?= lang('App.why_volunteer_title') ?>
            </h2>
            <p class="blur-reveal text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                <?= lang('App.why_volunteer_sub') ?>
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($whyVolunteer as $item): ?>
            <div class="p-6 rounded-2xl bg-slate-50/70 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700/80 space-y-3 hover:border-emerald-500/50 transition-all duration-200">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="<?= esc($item['icon']) ?>" class="w-5 h-5"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    <?= esc($item['title']) ?>
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <?= esc($item['desc']) ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- 3. How You Can Help -->
<section class="py-12 md:py-16 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center space-y-3 mb-10">
            <h2 class="blur-reveal text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                <?= lang('App.how_you_can_help_title') ?>
            </h2>
            <p class="blur-reveal text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                <?= lang('App.how_you_can_help_sub') ?>
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($howToHelp as $item): ?>
            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700/80 flex items-start gap-3 group hover:border-emerald-500 transition-all duration-200">
                <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="<?= esc($item['icon']) ?>" class="w-4 h-4"></i>
                </div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white leading-snug">
                    <?= esc($item['label']) ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- 4. Volunteer Registration Form -->
<section id="volunteer-form" class="py-12 md:py-16 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200 scroll-mt-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center space-y-3 mb-8">
            <h2 class="blur-reveal text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                <?= lang('App.volunteer_form_title') ?>
            </h2>
            <p class="blur-reveal text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                <?= lang('App.volunteer_form_sub') ?>
            </p>
        </div>

        <div class="bg-slate-50/70 dark:bg-slate-800/70 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-6 sm:p-8 shadow-xs">
            <form action="#" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">
                            <?= lang('App.your_full_name') ?>
                        </label>
                        <input type="text" name="full_name" required class="w-full px-4 h-14 bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">
                            <?= lang('App.phone_number') ?>
                        </label>
                        <input type="tel" name="phone" required dir="ltr" class="w-full px-4 h-14 bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">
                            <?= lang('App.whatsapp_number') ?>
                        </label>
                        <input type="tel" name="whatsapp" required dir="ltr" class="w-full px-4 h-14 bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">
                            <?= lang('App.email_optional') ?>
                        </label>
                        <input type="email" name="email" dir="ltr" class="w-full px-4 h-14 bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">
                            <?= lang('App.village_area') ?>
                        </label>
                        <input type="text" name="area" required class="w-full px-4 h-14 bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">
                            <?= lang('App.occupation') ?>
                        </label>
                        <input type="text" name="occupation" required class="w-full px-4 h-14 bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">
                        <?= lang('App.how_would_you_help') ?>
                    </label>
                    <?php
                        $mappedHelpOptions = [];
                        if (!empty($helpOptions)) {
                            foreach ($helpOptions as $option) {
                                $mappedHelpOptions[] = [
                                    'value' => (string)$option['value'],
                                    'label' => ($lang === 'ur') ? $option['label_ur'] : $option['label_en'],
                                    'icon'  => 'check-circle' // Generic icon for help options
                                ];
                            }
                        }
                    ?>
                    <?= view('components/custom_select', [
                        'name' => 'help_type',
                        'options' => $mappedHelpOptions,
                        'selected' => '',
                        'placeholder' => lang('App.select_option'),
                        'searchable' => false,
                        'class' => 'bg-white' // Match the textarea bg color
                    ]) ?>
                </div>

                <div>
                    <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">
                        <?= lang('App.short_message') ?>
                    </label>
                    <textarea name="message" rows="4" class="w-full px-4 py-4 min-h-[140px] bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 h-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold shadow-sm transition-all focus:ring-2 focus:ring-emerald-500/20">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        <span><?= lang('App.submit_application') ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>


<!-- 5. Community Message -->
<section class="py-12 md:py-16 bg-gradient-to-b from-emerald-50/50 to-slate-50 dark:from-slate-900 dark:to-slate-950 transition-colors duration-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-5">
            <i data-lucide="users-round" class="w-7 h-7"></i>
        </div>
        <h2 class="blur-reveal text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight mb-4">
            <?= lang('App.community_msg_title') ?>
        </h2>
        <p class="blur-reveal text-sm sm:text-base text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed mb-6">
            <?= lang('App.community_msg_sub') ?>
        </p>
        <a href="#volunteer-form" class="btn btn-md btn-outline">
            <i data-lucide="arrow-up" class="w-4 h-4"></i>
            <span><?= lang('App.register_now') ?></span>
        </a>
    </div>
</section>

<?= $this->endSection() ?>
