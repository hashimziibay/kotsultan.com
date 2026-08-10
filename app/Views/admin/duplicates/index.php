<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-8 shadow-xs">
    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= lang('App.admin_duplicates_title') ?></h2>
    <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_duplicates_sub') ?></p>
</div>

<?php if (empty($duplicateGroups)): ?>
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-12 text-center text-slate-400">
    <i data-lucide="check-check" class="w-12 h-12 text-emerald-500 mx-auto mb-3"></i>
    <h3 class="font-extrabold text-base text-slate-800 dark:text-white"><?= lang('App.admin_no_duplicates') ?></h3>
    <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_no_duplicates_sub') ?></p>
</div>
<?php else: ?>
<div class="space-y-6">
    <?php foreach ($duplicateGroups as $idx => $group): ?>
    <div class="bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-900/50 rounded-2xl p-6 shadow-xs">
        
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <h3 class="font-bold text-sm text-slate-900 dark:text-white"><?= esc($group['type']) ?></h3>
            </div>
            <span class="text-xs font-bold text-amber-600 dark:text-amber-400"><?= count($group['items']) ?> <?= lang('App.admin_records') ?></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($group['items'] as $item): ?>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-bold font-mono px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">ID #<?= $item['id'] ?></span>
                        <span class="text-xs text-slate-500"><?= esc($item['cat_en'] ?? '') ?></span>
                    </div>
                    
                    <h4 class="font-extrabold text-sm text-slate-900 dark:text-white mb-1"><?= esc($item['name_en']) ?></h4>
                    <?php if (!empty($item['name_ur'])): ?>
                    <p class="font-urdu text-xs text-slate-500 mb-2" dir="rtl"><?= esc($item['name_ur']) ?></p>
                    <?php endif; ?>

                    <div class="space-y-1 text-xs text-slate-600 dark:text-slate-300">
                        <div><strong class="text-slate-400">Phone:</strong> <?= esc($item['phone'] ?: 'N/A') ?></div>
                        <div><strong class="text-slate-400">Address:</strong> <?= esc($item['address_en'] ?: 'N/A') ?></div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700/60 flex items-center justify-between text-xs">
                    <a href="<?= base_url('admin/businesses/edit/' . $item['id']) ?>" target="_blank" class="text-emerald-600 hover:underline font-bold"><?= lang('App.admin_inspect_record') ?> &rarr;</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Safe Merge Form -->
        <?php if (count($group['items']) >= 2): ?>
        <form action="<?= base_url('admin/duplicates/merge') ?>" method="POST" onsubmit="return confirm('<?= lang('App.admin_confirm_merge') ?>');" class="mt-4 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex flex-col sm:flex-row items-center justify-between gap-4">
            <?= csrf_field() ?>
            <div class="flex items-center gap-3 text-xs font-semibold text-amber-900 dark:text-amber-200">
                <i data-lucide="git-merge" class="w-4 h-4 text-amber-500"></i>
                <span><?= lang('App.admin_merge_tool') ?></span>
            </div>

            <div class="flex items-center gap-2">
                <select name="master_id" required class="px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-bold">
                    <option value=""><?= lang('App.admin_keep_master') ?></option>
                    <?php foreach ($group['items'] as $item): ?>
                    <option value="<?= $item['id'] ?>">ID #<?= $item['id'] ?> - <?= esc($item['name_en']) ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="slave_id" required class="px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-bold">
                    <option value=""><?= lang('App.admin_secondary_id') ?></option>
                    <?php foreach ($group['items'] as $item): ?>
                    <option value="<?= $item['id'] ?>">ID #<?= $item['id'] ?> - <?= esc($item['name_en']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-500 text-slate-950 font-extrabold text-xs rounded-lg transition-all shadow-xs">
                    <?= lang('App.admin_merge_pair') ?>
                </button>
            </div>
        </form>
        <?php endif; ?>

    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
