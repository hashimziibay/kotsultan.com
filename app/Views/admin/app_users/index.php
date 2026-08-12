<?= $this->extend('admin/layouts/admin') ?>
<?= $this->section('content') ?>

<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= esc($pageHeading) ?></h2>
    </div>

    <form method="GET" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
        <input type="text" name="q" value="<?= esc($query ?? '') ?>" placeholder="Search name or phone"
               class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
        <select name="type" class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
            <option value="">All types</option>
            <option value="user" <?= ($selectedType ?? '') === 'user' ? 'selected' : '' ?>>Users</option>
            <option value="business" <?= ($selectedType ?? '') === 'business' ? 'selected' : '' ?>>Business</option>
        </select>
        <select name="status" class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
            <option value="">All statuses</option>
            <option value="active" <?= ($selectedStatus ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= ($selectedStatus ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
        <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">Filter</button>
    </form>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Phone</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Listings</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($users)): ?>
                        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No app users found</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="px-4 py-3 font-bold">#<?= (int) $u['id'] ?></td>
                                <td class="px-4 py-3 font-semibold"><?= esc($u['name']) ?></td>
                                <td class="px-4 py-3"><?= esc($u['phone']) ?></td>
                                <td class="px-4 py-3">
                                    <?php $t = ($u['account_type'] ?? 'user') === 'business' ? 'business' : 'user'; ?>
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold uppercase <?= $t === 'business' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' ?>">
                                        <?= $t ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3"><?= (int) ($bizCounts[(int) $u['id']] ?? 0) ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold uppercase <?= ($u['status'] ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' ?>">
                                        <?= esc($u['status'] ?? 'active') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="<?= base_url('admin/app-users/' . $u['id']) ?>" class="text-emerald-600 font-bold hover:underline">View</a>
                                    <form action="<?= base_url('admin/app-users/' . $u['id'] . '/toggle') ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <button class="text-slate-500 font-bold hover:underline"><?= ($u['status'] ?? 'active') === 'active' ? 'Disable' : 'Enable' ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
