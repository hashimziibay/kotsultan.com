<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<!-- Audit Banner -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-8 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= lang('App.admin_database_title') ?></h2>
        <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_database_sub', ['kts data base/']) ?></p>
    </div>
    
    <form action="<?= base_url('admin/database/import') ?>" method="POST" onsubmit="return confirm('<?= lang('App.admin_confirm_audit') ?>');">
        <?= csrf_field() ?>
        <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-2">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            <span><?= lang('App.admin_run_audit') ?></span>
        </button>
    </form>
</div>

<!-- Comparison Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    
    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal text-slate-400 mb-2"><?= lang('App.admin_live_mysql') ?></div>
        <div class="text-2xl font-extrabold text-slate-900 dark:text-white"><?= number_format($currentBusinessesCount) ?></div>
        <div class="text-xs text-emerald-600 font-semibold mt-1"><?= lang('App.admin_active_mysql') ?></div>
    </div>

    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal text-slate-400 mb-2"><?= lang('App.admin_source_sql') ?></div>
        <div class="text-2xl font-extrabold text-slate-900 dark:text-white"><?= count($sourceFiles) ?> <?= lang('App.admin_files') ?></div>
        <div class="text-xs text-slate-500 font-semibold mt-1">kts data base/*.sql (<?= lang('App.admin_read_only') ?>)</div>
    </div>

    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal text-slate-400 mb-2"><?= lang('App.admin_source_images') ?></div>
        <div class="text-2xl font-extrabold text-slate-900 dark:text-white"><?= number_format($sourcePicCount) ?> <?= lang('App.admin_photos') ?></div>
        <div class="text-xs text-slate-500 font-semibold mt-1">kts web data base pics/</div>
    </div>

    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="text-xs font-bold uppercase tracking-wider rtl:tracking-normal text-slate-400 mb-2"><?= lang('App.admin_broken_images') ?></div>
        <div class="text-2xl font-extrabold <?= $brokenImages > 0 ? 'text-rose-500' : 'text-emerald-500' ?>"><?= number_format($brokenImages) ?></div>
        <div class="text-xs text-slate-500 font-semibold mt-1"><?= lang('App.admin_missing_files') ?></div>
    </div>
</div>

<!-- Business Image Audit (permanent, read-only) -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-8 shadow-xs">
    <div class="flex items-center justify-between mb-1">
        <h3 class="font-extrabold text-slate-900 dark:text-white text-base"><?= lang('App.admin_image_audit_title') ?></h3>
        <i data-lucide="image" class="w-5 h-5 text-emerald-500"></i>
    </div>
    <p class="text-xs text-slate-500 mb-5"><?= lang('App.admin_image_audit_sub') ?></p>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60">
            <div class="text-[10px] font-bold uppercase tracking-wider rtl:tracking-normal text-slate-400 mb-1"><?= lang('App.admin_img_with_ref') ?></div>
            <div class="text-xl font-extrabold text-slate-900 dark:text-white"><?= number_format($imageStats['businessesWithImage'] ?? 0) ?></div>
        </div>
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60">
            <div class="text-[10px] font-bold uppercase tracking-wider rtl:tracking-normal text-emerald-600 dark:text-emerald-400 mb-1"><?= lang('App.admin_img_valid') ?></div>
            <div class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400"><?= number_format($imageStats['validImages'] ?? 0) ?></div>
        </div>
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60">
            <div class="text-[10px] font-bold uppercase tracking-wider rtl:tracking-normal text-rose-600 dark:text-rose-400 mb-1"><?= lang('App.admin_img_missing') ?></div>
            <div class="text-xl font-extrabold text-rose-600 dark:text-rose-400"><?= number_format($imageStats['missingImages'] ?? 0) ?></div>
        </div>
        <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60">
            <div class="text-[10px] font-bold uppercase tracking-wider rtl:tracking-normal text-amber-600 dark:text-amber-400 mb-1"><?= lang('App.admin_img_invalid') ?></div>
            <div class="text-xl font-extrabold text-amber-600 dark:text-amber-400"><?= number_format($imageStats['invalidPaths'] ?? 0) ?></div>
        </div>
        <div class="p-4 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60">
            <div class="text-[10px] font-bold uppercase tracking-wider rtl:tracking-normal text-indigo-600 dark:text-indigo-400 mb-1"><?= lang('App.admin_img_duplicates') ?></div>
            <div class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400"><?= number_format($imageStats['duplicateRefs'] ?? 0) ?></div>
        </div>
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60">
            <div class="text-[10px] font-bold uppercase tracking-wider rtl:tracking-normal text-slate-400 mb-1"><?= lang('App.admin_img_orphans') ?></div>
            <div class="text-xl font-extrabold text-slate-900 dark:text-white"><?= number_format($imageStats['orphanFiles'] ?? 0) ?></div>
        </div>
    </div>

    <p class="text-[11px] text-slate-400 mt-4"><?= lang('App.admin_img_note') ?></p>
</div>

<!-- Source Inspection Card -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-4">
    <h3 class="font-extrabold text-slate-900 dark:text-white text-base"><?= lang('App.admin_source_status') ?></h3>
    
    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
        <p class="font-bold text-slate-900 dark:text-white mb-1"><?= lang('App.admin_safety_policy') ?></p>
        <?= lang('App.admin_safety_policy_text', ['kts data base/', lang('App.admin_read_only')]) ?>
    </div>

    <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
        <?php foreach ($sourceFiles as $sf): ?>
        <div class="py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 font-mono text-slate-800 dark:text-slate-200">
                <i data-lucide="file-code" class="w-4 h-4 text-emerald-500"></i>
                <span><?= esc($sf) ?></span>
            </div>
            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold text-[10px]"><?= lang('App.admin_verified') ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?= $this->endSection() ?>
