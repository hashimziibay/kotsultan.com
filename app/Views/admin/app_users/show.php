<?= $this->extend('admin/layouts/admin') ?>
<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= esc($pageHeading) ?></h2>
        <a href="<?= base_url('admin/app-users') ?>" class="px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs font-bold">&larr; Back</a>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 space-y-2 text-sm">
        <p><span class="text-slate-500">Phone:</span> <strong><?= esc($user['phone']) ?></strong></p>
        <p><span class="text-slate-500">Type:</span> <strong><?= esc($user['account_type'] ?? 'user') ?></strong></p>
        <p><span class="text-slate-500">Status:</span> <strong><?= esc($user['status'] ?? 'active') ?></strong></p>
        <p><span class="text-slate-500">Locale / Theme:</span> <?= esc(($user['locale'] ?? 'en') . ' / ' . ($user['theme'] ?? 'light')) ?></p>
        <p><span class="text-slate-500">Has password:</span> <?= !empty($user['password_hash']) ? 'Yes' : 'No' ?></p>
        <form action="<?= base_url('admin/app-users/' . $user['id'] . '/toggle') ?>" method="POST" class="pt-2">
            <?= csrf_field() ?>
            <button class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold">
                <?= ($user['status'] ?? 'active') === 'active' ? 'Disable account' : 'Enable account' ?>
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 space-y-3">
        <h3 class="text-sm font-extrabold uppercase tracking-wider text-emerald-600">Owned businesses</h3>
        <?php if (empty($businesses)): ?>
            <p class="text-xs text-slate-400">No linked businesses yet.</p>
        <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($businesses as $b): ?>
                    <div class="flex items-center justify-between gap-3 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700">
                        <div class="min-w-0">
                            <a href="<?= base_url('admin/businesses/edit/' . $b['id']) ?>" class="text-xs font-bold text-emerald-700 hover:underline truncate block">
                                <?= esc($b['name_en'] ?: $b['name_ur']) ?>
                            </a>
                            <p class="text-[10px] text-slate-400">#<?= (int) $b['id'] ?> · <?= esc($b['status']) ?></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <?php if (($b['status'] ?? '') === 'pending'): ?>
                                <form action="<?= base_url('admin/app-users/business/' . $b['id'] . '/approve') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <button class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-[10px] font-bold">Approve</button>
                                </form>
                            <?php endif; ?>
                            <a href="<?= base_url('admin/businesses/edit/' . $b['id']) ?>" class="text-[10px] font-bold text-slate-500 hover:underline">Edit</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
