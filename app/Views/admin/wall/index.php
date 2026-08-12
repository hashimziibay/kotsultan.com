<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-8 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= lang('App.admin_wall') ?></h2>
        <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_wall_sub') ?> (<?= lang('App.admin_total') ?>: <?= count($personalities) ?>)</p>
    </div>
    <a href="<?= base_url('admin/wall-of-kot-sultan/create') ?>" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-2">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span><?= lang('App.admin_add_personality') ?></span>
    </a>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left rtl:text-right">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-bold text-[10px] border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="px-4 py-3.5"><?= lang('App.admin_id') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_photo_name') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_category') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_profession') ?></th>
                    <th class="px-4 py-3.5 text-center"><?= lang('App.admin_featured') ?></th>
                    <th class="px-4 py-3.5 text-center"><?= lang('App.status') ?></th>
                    <th class="px-4 py-3.5 text-right rtl:text-left"><?= lang('App.actions') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                <?php foreach ($personalities as $p): ?>
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                    <td class="px-4 py-3 font-mono text-slate-400">#<?= $p['id'] ?></td>
                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                        <div class="flex items-center gap-3">
                            <img src="<?= !empty($p['photo']) ? base_url($p['photo']) : base_url('images/placeholder-person.jpg') ?>" 
                                 alt="Photo" class="w-10 h-10 rounded-full object-contain bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <div>
                                <a href="<?= base_url('admin/wall-of-kot-sultan/edit/' . $p['id']) ?>" class="hover:text-emerald-500 block leading-snug">
                                    <?= esc($p['name_en']) ?>
                                </a>
                                <?php if (!empty($p['name_ur']) && $p['name_ur'] !== $p['name_en']): ?>
                                <span class="font-urdu text-[11px] text-slate-500 block" dir="rtl"><?= esc($p['name_ur']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                        <div class="flex flex-wrap gap-1">
                            <?php
                                $labels = $p['category_labels'] ?? [];
                                if ($labels === [] && ! empty($p['category_name_en'])) {
                                    $labels = [$p['category_name_en']];
                                }
                            ?>
                            <?php if ($labels === []): ?>
                                <span class="text-slate-400 text-[11px]">—</span>
                            <?php else: ?>
                                <?php foreach ($labels as $label): ?>
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 font-bold text-[11px]"><?= esc($label) ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300"><?= esc($p['profession_en'] ?: $p['profession_ur'] ?: 'N/A') ?></td>
                    <td class="px-4 py-3 text-center">
                        <?php if (!empty($p['featured'])): ?>
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 font-extrabold text-[10px]">★ <?= lang('App.featured_badge') ?></span>
                        <?php else: ?>
                        <span class="text-slate-400 text-[10px]">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <form action="<?= base_url('admin/wall-of-kot-sultan/toggle/' . $p['id']) ?>" method="POST" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider rtl:tracking-normal <?= ($p['status'] ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700' ?>">
                                <?= esc($p['status'] ?? 'active') ?>
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right rtl:text-left">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="<?= base_url('admin/wall-of-kot-sultan/edit/' . $p['id']) ?>" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-emerald-500" title="<?= lang('App.admin_edit') ?>">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            <form action="<?= base_url('admin/wall-of-kot-sultan/delete/' . $p['id']) ?>" method="POST" onsubmit="return confirm('<?= lang('App.admin_confirm_delete_personality') ?>');" class="inline">
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
