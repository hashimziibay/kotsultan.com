<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-8 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= lang('App.admin_emergency_title') ?></h2>
        <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_emergency_sub') ?> (<?= lang('App.admin_total') ?>: <?= count($contacts) ?>)</p>
    </div>
    <a href="<?= base_url('admin/emergency-numbers/create') ?>" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-2">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span><?= lang('App.admin_add_helpline') ?></span>
    </a>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left rtl:text-right">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-bold text-[10px] border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="px-4 py-3.5"><?= lang('App.admin_id') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_icon') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_department_name') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_category') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_primary_phone') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_working_hours') ?></th>
                    <th class="px-4 py-3.5 text-center"><?= lang('App.status') ?></th>
                    <th class="px-4 py-3.5 text-right rtl:text-left"><?= lang('App.actions') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                <?php foreach ($contacts as $c): ?>
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                    <td class="px-4 py-3 font-mono text-slate-400">#<?= $c['id'] ?></td>
                    <td class="px-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                            <i data-lucide="<?= esc($c['icon'] ?: 'phone-call') ?>" class="w-4 h-4"></i>
                        </div>
                    </td>
                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                        <a href="<?= base_url('admin/emergency-numbers/edit/' . $c['id']) ?>" class="hover:text-emerald-500 block leading-snug">
                            <?= esc($c['department_name_en']) ?>
                        </a>
                        <?php if (!empty($c['department_name_ur']) && $c['department_name_ur'] !== $c['department_name_en']): ?>
                        <span class="font-urdu text-[11px] text-slate-500 block" dir="rtl"><?= esc($c['department_name_ur']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                        <span class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 font-bold text-[11px]">
                            <?= esc($c['category_en'] ?: $c['category_ur']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 font-mono text-slate-900 dark:text-white font-extrabold">
                        <?= esc($c['phone_primary']) ?>
                    </td>
                    <td class="px-4 py-3 text-slate-500">
                        <?= esc($c['working_hours_en'] ?: $c['working_hours_ur'] ?: '24/7') ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form action="<?= base_url('admin/emergency-numbers/toggle/' . $c['id']) ?>" method="POST" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider rtl:tracking-normal <?= ($c['status'] ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700' ?>">
                                <?= esc($c['status'] ?? 'active') ?>
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right rtl:text-left">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="<?= base_url('admin/emergency-numbers/edit/' . $c['id']) ?>" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-emerald-500" title="<?= lang('App.admin_edit') ?>">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            <form action="<?= base_url('admin/emergency-numbers/delete/' . $c['id']) ?>" method="POST" onsubmit="return confirm('<?= lang('App.admin_confirm_delete_helpline') ?>');" class="inline">
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
