<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<!-- Action Header & Filters -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-8 shadow-xs">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= lang('App.admin_business_listings') ?></h2>
            <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_business_listings_sub') ?> (<?= lang('App.admin_total') ?>: <?= number_format($total) ?>)</p>
        </div>
        <a href="<?= base_url('admin/businesses/create') ?>" class="btn px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span><?= lang('App.admin_add_new_business') ?></span>
        </a>
    </div>

    <!-- Filter Form -->
    <form action="<?= base_url('admin/businesses') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        
        <!-- Search Query -->
        <div class="relative">
            <input type="text" name="q" value="<?= esc($query) ?>" placeholder="<?= lang('App.admin_search_hint') ?>"
                   class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium focus:outline-none focus:border-emerald-500">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
        </div>

        <!-- Category Filter -->
        <select name="category" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium focus:outline-none focus:border-emerald-500">
            <option value=""><?= lang('App.admin_all_categories') ?></option>
            <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $selectedCategory == $c['id'] ? 'selected' : '' ?>>
                <?= esc($c['name_en'] ?: $c['name_ur']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <!-- Area Filter -->
        <select name="area" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium focus:outline-none focus:border-emerald-500">
            <option value=""><?= lang('App.admin_all_areas') ?></option>
            <?php foreach ($areas as $a): ?>
            <option value="<?= $a['id'] ?>" <?= $selectedArea == $a['id'] ? 'selected' : '' ?>>
                <?= esc($a['name_en'] ?: $a['name_ur']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <!-- Status Filter -->
        <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium focus:outline-none focus:border-emerald-500">
            <option value=""><?= lang('App.admin_all_statuses') ?></option>
            <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>><?= lang('App.admin_active_status') ?></option>
            <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : '' ?>>Pending (app)</option>
            <option value="inactive" <?= $selectedStatus === 'inactive' ? 'selected' : '' ?>><?= lang('App.admin_inactive_status') ?></option>
        </select>

        <!-- Submit & Reset -->
        <div class="flex gap-2">
            <button type="submit" class="flex-1 py-2 bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
                <?= lang('App.admin_filter') ?>
            </button>
            <a href="<?= base_url('admin/businesses') ?>" class="px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors flex items-center justify-center">
                <?= lang('App.admin_reset') ?>
            </a>
        </div>
    </form>
</div>

<!-- Business Data Table -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
    
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left rtl:text-right">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-bold text-[10px] border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="px-4 py-3.5"><?= lang('App.admin_id') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.business_name') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_category') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_phone_contact') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_address') ?></th>
                    <th class="px-4 py-3.5 text-center"><?= lang('App.status') ?></th>
                    <th class="px-4 py-3.5 text-right rtl:text-left"><?= lang('App.actions') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                <?php if (empty($businesses)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        <i data-lucide="store" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                        <p class="font-bold"><?= lang('App.admin_no_businesses_found') ?></p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($businesses as $b): ?>
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                    <td class="px-4 py-3 text-slate-400 font-mono">#<?= $b['id'] ?></td>
                    
                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                                <img src="<?= esc(get_business_image_url($b['image'] ?? '', 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&q=80&w=100')) ?>" 
                                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&q=80&w=100';"
                                     alt="Preview" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <a href="<?= base_url('admin/businesses/edit/' . $b['id']) ?>" class="hover:text-emerald-500 block leading-snug">
                                    <?= esc($b['name_en']) ?>
                                </a>
                                <?php if (!empty($b['name_ur']) && $b['name_ur'] !== $b['name_en']): ?>
                                <span class="font-urdu text-[11px] text-slate-500 block" dir="rtl"><?= esc($b['name_ur']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                        <span class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-[11px]">
                            <?= esc($b['cat_en'] ?? $b['category_id']) ?>
                        </span>
                    </td>

                    <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-300">
                        <?= esc($b['phone'] ?: 'N/A') ?>
                    </td>

                    <td class="px-4 py-3 text-slate-500 line-clamp-1 max-w-xs">
                        <?= esc($b['address_en'] ?: $b['address_ur'] ?: 'Kot Sultan') ?>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <?php
                            $st = $b['status'] ?? 'active';
                            $badgeClass = $st === 'active'
                                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'
                                : ($st === 'pending'
                                    ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'
                                    : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400');
                        ?>
                        <?php if ($st === 'pending'): ?>
                            <form action="<?= base_url('admin/app-users/business/' . $b['id'] . '/approve') ?>" method="POST" class="inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider <?= $badgeClass ?>" title="Approve listing">
                                    pending · approve
                                </button>
                            </form>
                        <?php else: ?>
                        <form action="<?= base_url('admin/businesses/toggle/' . $b['id']) ?>" method="POST" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider rtl:tracking-normal <?= $badgeClass ?>">
                                <?= esc($st) ?>
                            </button>
                        </form>
                        <?php endif; ?>
                        <?php if (!empty($b['user_id'])): ?>
                            <div class="mt-1">
                                <a href="<?= base_url('admin/app-users/' . (int) $b['user_id']) ?>" class="text-[10px] text-emerald-600 font-bold hover:underline">
                                    App owner #<?= (int) $b['user_id'] ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td class="px-4 py-3 text-right rtl:text-left">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="<?= base_url('admin/businesses/edit/' . $b['id']) ?>" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-emerald-500" title="<?= lang('App.admin_edit') ?>">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            <form action="<?= base_url('admin/businesses/delete/' . $b['id']) ?>" method="POST" onsubmit="return confirm('<?= lang('App.admin_confirm_delete_business') ?>');" class="inline">
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

    <!-- Server-Side Pagination Controls -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
        <div class="text-slate-500 font-semibold">
            <?= lang('App.admin_showing_page') ?> <strong><?= $page ?></strong> <?= lang('App.admin_of') ?> <strong><?= $totalPages ?></strong> (<?= lang('App.admin_total') ?> <?= number_format($total) ?> <?= lang('App.admin_records_plural') ?>)
        </div>
        <div class="flex gap-2">
            <?php if ($page > 1): ?>
            <a href="<?= base_url('admin/businesses?page=' . ($page - 1) . '&q=' . urlencode($query) . '&category=' . $selectedCategory) ?>" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold hover:bg-slate-100">
                &larr; <?= lang('App.admin_previous') ?>
            </a>
            <?php endif; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="<?= base_url('admin/businesses?page=' . ($page + 1) . '&q=' . urlencode($query) . '&category=' . $selectedCategory) ?>" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold hover:bg-slate-100">
                <?= lang('App.admin_next') ?> &rarr;
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
