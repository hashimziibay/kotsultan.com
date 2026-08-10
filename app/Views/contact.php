<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Header Section -->
<section class="relative bg-gradient-to-b from-emerald-50/50 to-slate-50 dark:from-slate-900 dark:to-slate-900 py-12 md:py-16 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 text-xs font-bold uppercase tracking-wider mb-3">
            <i data-lucide="phone-call" class="w-3.5 h-3.5"></i> <?= lang('App.contact_badge') ?>
        </span>
        <h1 class="blur-reveal text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-3">
            <?= lang('App.nav_contact') ?>
        </h1>
        <p class="blur-reveal text-sm sm:text-base text-slate-600 dark:text-slate-300 max-w-xl mx-auto leading-relaxed">
            <?= lang('App.contact_hero_sub') ?>
        </p>
    </div>
</section>



<!-- Contact Form & Office Info -->
<section class="py-12 md:py-16 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Info Panel -->
            <div class="lg:col-span-5 space-y-6">
                <div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight mb-2">
                        <?= lang('App.admin_contact_title') ?>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        <?= lang('App.admin_contact_sub') ?>
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80">
                        <i data-lucide="map-pin" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm"><?= lang('App.office_location') ?></h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"><?= lang('App.office_address') ?></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80">
                        <i data-lucide="mail" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm"><?= lang('App.email_address') ?></h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">info@kotsultan.com</p>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Right Contact Form -->
            <div class="lg:col-span-7 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 p-6 sm:p-8 shadow-xs">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1">
                    <?= lang('App.send_message_title') ?>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">
                    <?= lang('App.send_message_sub') ?>
                </p>

                <form action="#" method="POST" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.your_full_name') ?></label>
                            <input type="text" required class="w-full px-4 h-14 bg-slate-50 hover:bg-white dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                        </div>

                        <div>
                            <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.phone_number') ?></label>
                            <input type="tel" required dir="ltr" class="w-full px-4 h-14 bg-slate-50 hover:bg-white dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.subject_business') ?></label>
                        <input type="text" required class="w-full px-4 h-14 bg-slate-50 hover:bg-white dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.message_label') ?></label>
                        <textarea rows="4" required class="w-full px-4 py-4 min-h-[140px] bg-slate-50 hover:bg-white dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 text-sm md:text-base font-medium outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-sm"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 h-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold shadow-sm transition-all focus:ring-2 focus:ring-emerald-500/20">
                            <i data-lucide="send" class="w-5 h-5"></i>
                            <span><?= lang('App.submit_message') ?></span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</section>


<!-- Map Location Section -->
<section class="py-12 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i data-lucide="map" class="w-5 h-5 text-emerald-600"></i>
                    <span><?= lang('App.map_title') ?></span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    <?= lang('App.map_subtitle') ?>
                </p>
            </div>
            <a href="https://maps.google.com/?q=Kot+Sultan+Layyah" target="_blank" rel="noopener" class="btn btn-sm btn-outline">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span><?= lang('App.open_in_google_maps') ?></span>
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm h-[400px] bg-slate-100 dark:bg-slate-800 relative">
            <iframe src="https://maps.google.com/maps?q=Kot+Sultan,+Layyah,+Punjab,+Pakistan&t=&z=14&ie=UTF8&iwloc=&output=embed"
                    class="w-full h-full border-0 filter dark:contrast-125 dark:brightness-90 transition-all duration-300"
                    allowfullscreen="" 
                    loading="lazy" 
                    title="<?= lang('App.map_title') ?>"
                    referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
