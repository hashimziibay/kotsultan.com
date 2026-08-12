<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-6 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= lang('App.admin_wall_categories_title') ?></h2>
        <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_wall_categories_sub') ?> (<?= lang('App.admin_total') ?>: <?= count($categories) ?>)</p>
    </div>
    <a href="<?= base_url('admin/wall-categories/create') ?>" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-2">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span><?= lang('App.admin_add_wall_category') ?></span>
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-sm font-semibold border border-emerald-200 dark:border-emerald-800">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-sm font-semibold border border-rose-200 dark:border-rose-800">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<form method="get" action="<?= base_url('admin/wall-categories') ?>" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 mb-6 shadow-xs flex flex-col md:flex-row gap-3 items-stretch md:items-end">
    <div class="flex-1">
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5"><?= lang('App.admin_search') ?? 'Search' ?></label>
        <input type="text" name="q" value="<?= esc($query) ?>" placeholder="<?= esc(lang('App.admin_name_en') . ' / ' . lang('App.admin_name_ur')) ?>"
               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
    </div>
    <div class="w-full md:w-44">
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5"><?= lang('App.status') ?></label>
        <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
            <option value=""><?= lang('App.admin_all_categories') ?></option>
            <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>><?= lang('App.admin_active') ?? 'Active' ?></option>
            <option value="inactive" <?= $selectedStatus === 'inactive' ? 'selected' : '' ?>><?= lang('App.admin_off') ?></option>
        </select>
    </div>
    <button type="submit" class="px-4 py-2.5 bg-slate-900 dark:bg-emerald-600 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition-all">
        <?= lang('App.admin_filter') ?? 'Filter' ?>
    </button>
</form>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left rtl:text-right">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-bold text-[10px] border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="px-4 py-3.5"><?= lang('App.admin_id') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_icon') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_name_en') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_name_ur') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_wall_personalities') ?></th>
                    <th class="px-4 py-3.5 text-center"><?= lang('App.order') ?></th>
                    <th class="px-4 py-3.5 text-center"><?= lang('App.status') ?></th>
                    <th class="px-4 py-3.5 text-right rtl:text-left"><?= lang('App.actions') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center text-slate-500"><?= lang('App.admin_no_results') ?? 'No categories found.' ?></td>
                </tr>
                <?php else: ?>
                <?php foreach ($categories as $c): ?>
                <?php $count = $countsMap[$c['id']] ?? 0; ?>
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                    <td class="px-4 py-3 font-mono text-slate-400">#<?= $c['id'] ?></td>
                    <td class="px-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <i data-lucide="<?= esc($c['icon'] ?: 'user') ?>" class="w-4 h-4"></i>
                        </div>
                    </td>
                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white"><?= esc($c['name_en']) ?></td>
                    <td class="px-4 py-3 font-urdu text-sm" dir="rtl"><?= esc($c['name_ur']) ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-[11px]">
                            <?= number_format($count) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center font-mono font-bold"><?= (int) ($c['display_order'] ?? 0) ?></td>
                    <td class="px-4 py-3 text-center">
                        <form action="<?= base_url('admin/wall-categories/toggle/' . $c['id']) ?>" method="POST" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider <?= ($c['status'] ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400' ?>">
                                <?= esc($c['status'] ?? 'active') ?>
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right rtl:text-left">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="<?= base_url('admin/wall-categories/edit/' . $c['id']) ?>" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-emerald-500" title="<?= lang('App.admin_edit') ?>">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            <form action="<?= base_url('admin/wall-categories/delete/' . $c['id']) ?>" method="POST" onsubmit="return confirm('<?= esc(lang('App.admin_confirm_delete_wall_category'), 'js') ?>');" class="inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50" title="<?= lang('App.admin_delete') ?>">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
