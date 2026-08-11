<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$qs = static function (array $overrides = []) use ($query, $selectedStatus, $perPage, $page) {
    $params = array_filter([
        'q'        => $overrides['q'] ?? $query,
        'status'   => $overrides['status'] ?? $selectedStatus,
        'per_page' => $overrides['per_page'] ?? $perPage,
        'page'     => $overrides['page'] ?? $page,
    ], static fn ($v) => $v !== null && $v !== '');
    return base_url('admin/categories?' . http_build_query($params));
};
?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-6 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= lang('App.admin_categories_title') ?></h2>
        <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_categories_sub') ?> (<?= lang('App.admin_total') ?>: <?= number_format($total) ?>)</p>
    </div>
    <a href="<?= base_url('admin/categories/create') ?>" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-2">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span><?= lang('App.admin_add_category') ?></span>
    </a>
</div>

<form method="get" action="<?= base_url('admin/categories') ?>" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 mb-6 shadow-xs flex flex-col md:flex-row gap-3 items-stretch md:items-end">
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
    <div class="w-full md:w-32">
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Per page</label>
        <select name="per_page" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
            <?php foreach ([10, 25, 50, 100] as $n): ?>
            <option value="<?= $n ?>" <?= (int) $perPage === $n ? 'selected' : '' ?>><?= $n ?></option>
            <?php endforeach; ?>
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
                    <th class="px-4 py-3.5"><?= lang('App.admin_connected_listings') ?></th>
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
                <?php $listingCount = $countsMap[$c['id']] ?? 0; ?>
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                    <td class="px-4 py-3 font-mono text-slate-400">#<?= $c['id'] ?></td>
                    <td class="px-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <i data-lucide="<?= esc($c['icon'] ?: 'folder') ?>" class="w-4 h-4"></i>
                        </div>
                    </td>
                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white"><?= esc($c['name_en']) ?></td>
                    <td class="px-4 py-3 font-urdu text-sm" dir="rtl"><?= esc($c['name_ur']) ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-[11px]">
                            <?= number_format($listingCount) ?> <?= lang('App.admin_listings') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center font-mono font-bold"><?= $c['display_order'] ?></td>
                    <td class="px-4 py-3 text-center">
                        <form action="<?= base_url('admin/categories/toggle/' . $c['id']) ?>" method="POST" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider rtl:tracking-normal <?= ($c['status'] ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400' ?>">
                                <?= esc($c['status'] ?? 'active') ?>
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right rtl:text-left">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="<?= base_url('admin/categories/edit/' . $c['id']) ?>" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-emerald-500" title="<?= lang('App.admin_edit') ?>">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            <form action="<?= base_url('admin/categories/delete/' . $c['id']) ?>" method="POST" onsubmit="return confirm('<?= lang('App.admin_confirm_delete_category') ?>');" class="inline">
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

    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        <div class="text-slate-500 font-semibold">
            <?= lang('App.admin_showing_page') ?> <strong><?= (int) $page ?></strong> <?= lang('App.admin_of') ?> <strong><?= (int) $totalPages ?></strong>
            (<?= lang('App.admin_total') ?> <?= number_format($total) ?> <?= lang('App.admin_records_plural') ?>)
        </div>
        <div class="flex gap-2">
            <?php if ($page > 1): ?>
            <a href="<?= esc($qs(['page' => $page - 1])) ?>" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold hover:bg-slate-100 dark:hover:bg-slate-700">
                &larr; <?= lang('App.admin_previous') ?>
            </a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a href="<?= esc($qs(['page' => $page + 1])) ?>" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold hover:bg-slate-100 dark:hover:bg-slate-700">
                <?= lang('App.admin_next') ?> &rarr;
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
