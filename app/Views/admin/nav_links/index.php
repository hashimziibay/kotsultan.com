<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-8 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Menu Management</h2>
        <p class="text-xs text-slate-500 mt-1">Manage public navigation links (Total: <?= count($links) ?>)</p>
    </div>
    <a href="<?= base_url('admin/nav-links/create') ?>" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-2">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        <span><?= lang('App.admin_add_menu_item') ?></span>
    </a>
</div>

<?php if (session()->has('success')): ?>
    <div class="p-4 mb-6 text-sm text-emerald-800 bg-emerald-50 rounded-xl dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 flex items-center gap-2">
        <i data-lucide="check-circle" class="w-4 h-4"></i>
        <?= session('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="p-4 mb-6 text-sm text-red-800 bg-red-50 rounded-xl dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800 flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-4 h-4"></i>
        <?= session('error') ?>
    </div>
<?php endif; ?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left rtl:text-right" id="nav-links-table">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-bold text-[10px] border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="px-4 py-3.5 w-10"></th>
                    <th class="px-4 py-3.5"><?= lang('App.order') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_english_title') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_urdu_title') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_url') ?></th>
                    <th class="px-4 py-3.5 text-center"><?= lang('App.status') ?></th>
                    <th class="px-4 py-3.5 text-right rtl:text-left"><?= lang('App.actions') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50" id="sortable-body">
                <?php if (empty($links)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500"><?= lang('App.admin_no_nav_links') ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($links as $link): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" data-id="<?= $link['id'] ?>">
                            <td class="px-4 py-3 cursor-move text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 handle">
                                <i data-lucide="grip-vertical" class="w-4 h-4"></i>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-500 sort-order-display"><?= $link['sort_order'] ?></td>
                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200 tracking-wider rtl:tracking-normal"><?= esc($link['title_en']) ?></td>
                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200 tracking-wider rtl:tracking-normal font-urdu text-sm"><?= esc($link['title_ur']) ?></td>
                            <td class="px-4 py-3 font-mono text-[11px] text-slate-500"><?= esc($link['url']) ?></td>
                            <td class="px-4 py-3 text-center">
                                <form action="<?= base_url('admin/nav-links/toggle/' . $link['id']) ?>" method="post" class="inline-block">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider transition-colors <?= $link['status'] === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 hover:bg-emerald-200' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 hover:bg-red-200' ?>">
                                        <?= $link['status'] === 'active' ? lang('App.admin_active') : lang('App.admin_inactive') ?>
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right rtl:text-left">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= base_url('admin/nav-links/edit/' . $link['id']) ?>" class="p-1.5 text-slate-400 hover:text-blue-500 bg-slate-50 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="<?= lang('App.admin_edit') ?>">
                                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <form action="<?= base_url('admin/nav-links/delete/' . $link['id']) ?>" method="post" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this menu item? If this is a core page, the link will be permanently removed from the website navigation.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 bg-slate-50 hover:bg-red-50 dark:bg-slate-800 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Delete">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
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

<!-- Sortable.js for Drag & Drop Reordering -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('sortable-body');
    if (el && el.children.length > 0) {
        new Sortable(el, {
            handle: '.handle',
            animation: 150,
            onEnd: function (evt) {
                const rows = el.querySelectorAll('tr[data-id]');
                const orderData = [];
                rows.forEach((row, index) => {
                    orderData.push(row.getAttribute('data-id'));
                    row.querySelector('.sort-order-display').textContent = index + 1;
                });
                
                // Save order via AJAX
                fetch('<?= base_url('admin/nav-links/reorder') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify({ order: orderData })
                }).then(async (response) => {
                    const data = await response.json().catch(() => null);
                    if (!response.ok || !data || data.status !== 'success') {
                        alert((data && data.message) ? data.message : 'Failed to save order.');
                    }
                }).catch(() => {
                    alert('Failed to save order.');
                });
            }
        });
    }
});
</script>

<?= $this->endSection() ?>
