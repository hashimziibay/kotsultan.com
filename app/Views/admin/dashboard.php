<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<!-- Metric Cards Grid -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
    
    <!-- Total Businesses -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-400 mb-3">
            <span class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal"><?= lang('App.admin_total_listings') ?></span>
            <i data-lucide="store" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
        </div>
        <div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white"><?= number_format($stats['totalBusinesses']) ?></div>
            <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-1"><?= number_format($stats['activeBusinesses']) ?> <?= lang('App.admin_active') ?> • <?= number_format($stats['inactiveBusinesses']) ?> <?= lang('App.admin_off') ?></div>
        </div>
    </div>

    <!-- Categories -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-400 mb-3">
            <span class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal"><?= lang('App.admin_categories') ?></span>
            <i data-lucide="folder-tree" class="w-5 h-5 text-sky-600 dark:text-sky-400"></i>
        </div>
        <div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white"><?= number_format($stats['totalCategories']) ?></div>
            <div class="text-xs font-semibold text-slate-500 mt-1"><?= lang('App.admin_directory_structure') ?></div>
        </div>
    </div>

    <!-- Wall Members -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-400 mb-3">
            <span class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal"><?= lang('App.admin_wall_members') ?></span>
            <i data-lucide="award" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
        </div>
        <div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white"><?= number_format($stats['totalWallMembers']) ?></div>
            <div class="text-xs font-semibold text-amber-600 dark:text-amber-400 mt-1"><?= lang('App.admin_famous_personalities') ?></div>
        </div>
    </div>

    <!-- Emergency Contacts -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-400 mb-3">
            <span class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal"><?= lang('App.admin_emergency_short') ?></span>
            <i data-lucide="phone-call" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
        </div>
        <div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white"><?= number_format($stats['totalEmergency']) ?></div>
            <div class="text-xs font-semibold text-rose-600 dark:text-rose-400 mt-1"><?= lang('App.admin_helplines_services') ?></div>
        </div>
    </div>

    <!-- Images Status -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs flex flex-col justify-between col-span-2 sm:col-span-1">
        <div class="flex items-center justify-between text-slate-400 mb-3">
            <span class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal"><?= lang('App.admin_images_health') ?></span>
            <i data-lucide="image" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
        </div>
        <div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white"><?= number_format($stats['withImages']) ?></div>
            <div class="text-xs font-semibold text-slate-500 mt-1"><?= number_format($stats['withoutImages']) ?> <?= lang('App.admin_missing_images') ?></div>
        </div>
    </div>
</div>

<!-- Database Health & Issues Summary Bar -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <a href="<?= base_url('admin/duplicates') ?>" class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200 flex items-center justify-between hover:bg-amber-500/20 transition-colors">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <i data-lucide="copy" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal block"><?= lang('App.admin_possible_duplicates') ?></span>
                <span class="text-lg font-extrabold"><?= number_format($stats['duplicatePhoneCount']) ?> <?= lang('App.admin_phone_groups') ?></span>
            </div>
        </div>
        <i data-lucide="chevron-right" class="w-5 h-5 text-amber-500"></i>
    </a>

    <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-sky-500/20 flex items-center justify-center text-sky-600 dark:text-sky-400">
                <i data-lucide="languages" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal block text-slate-500 dark:text-slate-400"><?= lang('App.admin_missing_en_fields') ?></span>
                <span class="text-lg font-extrabold text-slate-900 dark:text-white"><?= number_format($stats['missingEnCount']) ?> <?= lang('App.admin_records') ?></span>
            </div>
        </div>
    </div>

    <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <i data-lucide="languages" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal block text-slate-500 dark:text-slate-400"><?= lang('App.admin_missing_ur_fields') ?></span>
                <span class="text-lg font-extrabold text-slate-900 dark:text-white"><?= number_format($stats['missingUrCount']) ?> <?= lang('App.admin_records') ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Two-Column Main Layout: Recent Activity & Breakdown -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left 2-Cols: Recent Listings & Recent Activity -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Recent Businesses Table -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-base flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-emerald-500"></i>
                    <span><?= lang('App.admin_recently_added_businesses') ?></span>
                </h3>
                <a href="<?= base_url('admin/businesses') ?>" class="text-xs font-bold text-emerald-600 hover:text-emerald-500"><?= lang('App.admin_view_all') ?> &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left rtl:text-right">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg rtl:rounded-r-lg"><?= lang('App.business_name') ?></th>
                            <th class="px-4 py-3"><?= lang('App.admin_category') ?></th>
                            <th class="px-4 py-3"><?= lang('App.admin_phone') ?></th>
                            <th class="px-4 py-3 rounded-r-lg rtl:rounded-l-lg text-right rtl:text-left"><?= lang('App.status') ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        <?php foreach ($recentBusinesses as $b): ?>
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                <a href="<?= base_url('admin/businesses/edit/' . $b['id']) ?>" class="hover:text-emerald-500">
                                    <?= esc($b['name_en'] ?: $b['name_ur']) ?>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-500"><?= esc($b['category_name_en'] ?? $b['category_id']) ?></td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400"><?= esc($b['phone'] ?: 'N/A') ?></td>
                            <td class="px-4 py-3 text-right rtl:text-left">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase <?= ($b['status'] ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400' ?>">
                                    <?= esc($b['status'] ?? 'active') ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Audit Logs -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-base flex items-center gap-2">
                    <i data-lucide="activity" class="w-4 h-4 text-sky-500"></i>
                    <span><?= lang('App.admin_recent_activity_logs') ?></span>
                </h3>
                <a href="<?= base_url('admin/activity-logs') ?>" class="text-xs font-bold text-sky-600 hover:text-sky-500"><?= lang('App.admin_view_all_logs') ?> &rarr;</a>
            </div>

            <div class="space-y-3">
                <?php foreach ($recentLogs as $log): ?>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-950 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white block"><?= esc($log['action']) ?> (<?= esc($log['module']) ?>)</span>
                            <span class="text-[10px] text-slate-500"><?= esc($log['admin_username']) ?> • IP: <?= esc($log['ip_address']) ?></span>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono"><?= date('H:i:s, d M', strtotime($log['created_at'])) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- Right 1-Col: Category Breakdown -->
    <div class="space-y-8">
        
        <!-- Category Listing Breakdown -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs">
            <h3 class="font-extrabold text-slate-900 dark:text-white text-base mb-4 flex items-center gap-2">
                <i data-lucide="folder-open" class="w-4 h-4 text-amber-500"></i>
                <span><?= lang('App.admin_top_categories') ?></span>
            </h3>

            <div class="space-y-3 max-h-96 overflow-y-auto hide-scrollbar">
                <?php foreach (array_slice($categoryCounts, 0, 10) as $cat): ?>
                <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors text-xs">
                    <span class="font-bold text-slate-800 dark:text-slate-200"><?= esc(($cat['name_en'] ?? '') ?: ($cat['name_ur'] ?? '') ?: ('Category #' . ($cat['category_id'] ?? ''))) ?></span>
                    <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-[11px]">
                        <?= number_format($cat['total']) ?> <?= lang('App.admin_listings') ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
