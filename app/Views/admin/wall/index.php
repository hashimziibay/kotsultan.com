<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$selectedStatus = $selectedStatus ?? '';
$pendingCount = (int) ($pendingCount ?? 0);
$qs = static function (array $overrides = []) use ($query, $selectedCategory, $selectedStatus) {
    $params = array_filter([
        'q'        => array_key_exists('q', $overrides) ? $overrides['q'] : $query,
        'category' => array_key_exists('category', $overrides) ? $overrides['category'] : $selectedCategory,
        'status'   => array_key_exists('status', $overrides) ? $overrides['status'] : $selectedStatus,
    ], static fn ($v) => $v !== null && $v !== '');
    return base_url('admin/wall-of-kot-sultan?' . http_build_query($params));
};
?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-6 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= lang('App.admin_wall') ?></h2>
        <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_wall_sub') ?> (<?= lang('App.admin_total') ?>: <?= count($personalities) ?>)</p>
        <?php if ($pendingCount > 0): ?>
            <a href="<?= esc($qs(['status' => 'pending'])) ?>" class="inline-flex mt-2 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-extrabold">
                <?= (int) $pendingCount ?> <?= lang('App.admin_pending_nominations') ?>
            </a>
        <?php endif; ?>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="<?= base_url('wall-of-kot-sultan/submit') ?>" target="_blank" class="px-4 py-2.5 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 text-amber-800 dark:text-amber-300 rounded-xl text-xs font-bold transition-all flex items-center gap-2 border border-amber-200 dark:border-amber-800">
            <i data-lucide="link" class="w-4 h-4"></i>
            <span><?= lang('App.admin_public_submit_link') ?></span>
        </a>
        <a href="<?= base_url('admin/wall-categories') ?>" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
            <i data-lucide="tags" class="w-4 h-4"></i>
            <span><?= lang('App.admin_wall_categories_nav') ?></span>
        </a>
        <a href="<?= base_url('admin/wall-of-kot-sultan/create') ?>" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span><?= lang('App.admin_add_personality') ?></span>
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 text-emerald-800 text-sm font-semibold"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 text-rose-700 text-sm font-semibold"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<form method="get" action="<?= base_url('admin/wall-of-kot-sultan') ?>" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 mb-6 shadow-xs flex flex-col md:flex-row gap-3 items-stretch md:items-end">
    <div class="flex-1">
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5"><?= lang('App.admin_search') ?></label>
        <input type="text" name="q" value="<?= esc($query) ?>" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm outline-none focus:border-emerald-500">
    </div>
    <div class="w-full md:w-48">
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5"><?= lang('App.admin_category') ?></label>
        <select name="category" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            <option value=""><?= lang('App.admin_all_categories') ?></option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= (string) $selectedCategory === (string) $c['id'] ? 'selected' : '' ?>><?= esc($c['name_en'] ?: $c['name_ur']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="w-full md:w-40">
        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5"><?= lang('App.status') ?></label>
        <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            <option value=""><?= lang('App.admin_all') ?? 'All' ?></option>
            <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : '' ?>><?= lang('App.admin_pending_status') ?: 'Pending' ?></option>
            <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>><?= lang('App.admin_active') ?></option>
            <option value="inactive" <?= $selectedStatus === 'inactive' ? 'selected' : '' ?>><?= lang('App.admin_off') ?></option>
        </select>
    </div>
    <button type="submit" class="px-4 py-2.5 bg-slate-900 dark:bg-emerald-600 text-white rounded-xl text-xs font-bold"><?= lang('App.admin_filter') ?></button>
</form>

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
                <?php if (empty($personalities)): ?>
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-slate-500"><?= lang('App.admin_no_results') ?></td>
                </tr>
                <?php else: ?>
                <?php foreach ($personalities as $p): ?>
                <?php $st = $p['status'] ?? 'active'; ?>
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors <?= $st === 'pending' ? 'bg-amber-50/40 dark:bg-amber-950/10' : '' ?>">
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
                                <?php if (!empty($p['submitter_name'])): ?>
                                <span class="text-[10px] text-amber-700 dark:text-amber-400 font-semibold"><?= lang('App.admin_wall_submitted_by') ?>: <?= esc($p['submitter_name']) ?></span>
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
                        <?php if ($st === 'pending'): ?>
                            <form action="<?= base_url('admin/wall-of-kot-sultan/approve/' . $p['id']) ?>" method="POST" class="inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 hover:bg-amber-200">
                                    <?= lang('App.admin_approve') ?: 'Approve' ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="<?= base_url('admin/wall-of-kot-sultan/toggle/' . $p['id']) ?>" method="POST" class="inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider <?= $st === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700' ?>">
                                    <?= esc($st) ?>
                                </button>
                            </form>
                        <?php endif; ?>
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
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
