<footer class="bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 border-t border-slate-200/80 dark:border-slate-800 transition-colors duration-200 relative overflow-hidden">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            
            <!-- Column 1: Brand & Bio -->
            <div class="space-y-4">
                <a href="<?= base_url('/') ?>" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold shadow-sm">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-slate-900 dark:text-white">
                        <?= lang('App.brand_name') ?>
                    </span>
                </a>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    <?= lang('App.about_text') ?>
                </p>
                <div class="pt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100/80 dark:bg-slate-800 text-emerald-800 dark:text-emerald-400 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <?= lang('App.location_tag') ?>
                    </span>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h4 class="font-bold text-slate-900 dark:text-white mb-4 text-base tracking-wide">
                    <?= lang('App.quick_links') ?>
                </h4>
                <ul class="space-y-2.5 text-sm font-medium">
                    <li><a href="<?= base_url('/') ?>" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors"><?= lang('App.nav_home') ?></a></li>
                    <li><a href="<?= base_url('directory') ?>" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors"><?= lang('App.nav_directory') ?></a></li>
                    <li><a href="<?= base_url('wall-of-kot-sultan') ?>" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors"><?= lang('App.nav_wall') ?></a></li>
                    <li><a href="<?= base_url('volunteer') ?>" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors"><?= lang('App.nav_volunteer') ?></a></li>
                    <li><a href="<?= base_url('about') ?>" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors"><?= lang('App.nav_about') ?></a></li>
                    <li><a href="<?= base_url('contact') ?>" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors"><?= lang('App.nav_contact') ?></a></li>
                </ul>
            </div>

            <!-- Column 3: Popular Directory Categories -->
            <div>
                <h4 class="font-bold text-slate-900 dark:text-white mb-4 text-base tracking-wide">
                    <?= lang('App.categories') ?>
                </h4>
                <ul class="space-y-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    <li><a href="<?= base_url('directory') ?>" class="hover:text-slate-900 dark:hover:text-white transition-colors"><?= lang('App.cat_medical') ?></a></li>
                    <li><a href="<?= base_url('directory') ?>" class="hover:text-slate-900 dark:hover:text-white transition-colors"><?= lang('App.cat_education') ?></a></li>
                    <li><a href="<?= base_url('directory') ?>" class="hover:text-slate-900 dark:hover:text-white transition-colors"><?= lang('App.cat_shops') ?></a></li>
                    <li><a href="<?= base_url('directory') ?>" class="hover:text-slate-900 dark:hover:text-white transition-colors"><?= lang('App.cat_food') ?></a></li>
                    <li><a href="<?= base_url('directory') ?>" class="hover:text-slate-900 dark:hover:text-white transition-colors"><?= lang('App.cat_crafts') ?></a></li>
                </ul>
            </div>

            <!-- Column 4: Contact & Emergency -->
            <div>
                <h4 class="font-bold text-slate-900 dark:text-white mb-4 text-base tracking-wide">
                    <?= lang('App.nav_contact') ?>
                </h4>
                <div class="space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    <p class="flex items-start gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600 dark:text-emerald-500 flex-shrink-0 mt-0.5"></i>
                        <span><?= lang('App.office_address') ?></span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i data-lucide="mail" class="w-4 h-4 text-emerald-600 dark:text-emerald-500 flex-shrink-0"></i>
                        <span>info@kotsultan.com</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i data-lucide="phone" class="w-4 h-4 text-emerald-600 dark:text-emerald-500 flex-shrink-0"></i>
                        <span dir="ltr">+92 305 6660169</span>
                    </p>
                </div>
            </div>

        </div>

        <div class="pt-8 border-t border-slate-200 dark:border-slate-800/80 text-center text-xs text-slate-500 dark:text-slate-500">
            <p>&copy; <?= date('Y') ?> <?= lang('App.brand_name') ?>. <?= lang('App.copyright') ?></p>
        </div>

    </div>
</footer>
