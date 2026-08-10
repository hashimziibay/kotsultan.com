<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-8 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= lang('App.admin_villages_title') ?></h2>
        <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_villages_sub') ?> (<?= lang('App.admin_total') ?>: <?= count($villages) ?>)</p>
    </div>
    <a href="<?= base_url('admin/villages/create') ?>" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-2">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span><?= lang('App.admin_add_village') ?></span>
    </a>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left rtl:text-right">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-bold text-[10px] border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="px-4 py-3.5"><?= lang('App.admin_id') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_name_en') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_name_ur') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_connected_businesses') ?></th>
                    <th class="px-4 py-3.5 text-center"><?= lang('App.status') ?></th>
                    <th class="px-4 py-3.5 text-right rtl:text-left"><?= lang('App.actions') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                <?php foreach ($villages as $v): ?>
                <?php $cnt = $countsMap[$v['id']] ?? 0; ?>
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                    <td class="px-4 py-3 font-mono text-slate-400">#<?= $v['id'] ?></td>
                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white"><?= esc($v['name_en']) ?></td>
                    <td class="px-4 py-3 font-urdu text-sm" dir="rtl"><?= esc($v['name_ur']) ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-[11px]">
                            <?= number_format($cnt) ?> <?= lang('App.admin_listings') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase <?= ($v['status'] ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700' ?>">
                            <?= esc($v['status'] ?? 'active') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right rtl:text-left">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="<?= base_url('admin/villages/edit/' . $v['id']) ?>" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-emerald-500" title="<?= lang('App.admin_edit') ?>">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            <form action="<?= base_url('admin/villages/delete/' . $v['id']) ?>" method="POST" onsubmit="return confirm('<?= lang('App.admin_confirm_delete_village') ?>');" class="inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50" title="<?= lang('App.admin_delete') ?>">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
