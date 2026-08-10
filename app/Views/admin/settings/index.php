<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto space-y-6">
    
    <!-- Account Information -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs">
        <h2 class="text-base font-extrabold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <i data-lucide="user-check" class="w-5 h-5 text-emerald-500"></i>
            <span><?= lang('App.admin_account_info') ?></span>
        </h2>

        <div class="space-y-3 text-xs">
            <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                <span class="text-slate-500 font-bold"><?= lang('App.admin_username') ?></span>
                <span class="font-extrabold text-slate-900 dark:text-white"><?= esc($admin['username'] ?? '') ?></span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                <span class="text-slate-500 font-bold"><?= lang('App.email_address') ?></span>
                <span class="font-mono text-slate-900 dark:text-white"><?= esc($admin['email'] ?? '') ?></span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                <span class="text-slate-500 font-bold"><?= lang('App.admin_role') ?></span>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-extrabold text-[10px] uppercase">
                    <?= esc($admin['role'] ?? 'admin') ?>
                </span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-slate-500 font-bold"><?= lang('App.admin_last_login') ?></span>
                <span class="font-mono text-slate-500"><?= esc($admin['last_login_at'] ?? lang('App.admin_never')) ?></span>
            </div>
        </div>
    </div>

    <!-- Password Change Form -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs">
        <h2 class="text-base font-extrabold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <i data-lucide="shield-check" class="w-5 h-5 text-amber-500"></i>
            <span><?= lang('App.admin_update_password') ?></span>
        </h2>

        <form action="<?= base_url('admin/settings/password') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_current_password') ?></label>
                <input type="password" name="current_password" required placeholder="••••••••"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_new_password_min') ?></label>
                <input type="password" name="new_password" required minlength="8" placeholder="••••••••"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_confirm_new_password2') ?></label>
                <input type="password" name="confirm_password" required minlength="8" placeholder="••••••••"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition-all">
                    <?= lang('App.admin_update_password') ?>
                </button>
            </div>
        </form>
    </div>

</div>

<?= $this->endSection() ?>
