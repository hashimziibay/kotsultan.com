<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
    $isUrdu = ($lang === 'ur');
    $isBusiness = (($user['account_type'] ?? 'user') === 'business');
    $tab = ($currentTab ?? 'profile') === 'business' ? 'business' : 'profile';
?>

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-8 transition-colors duration-200 pt-24" x-data="{ currentTab: '<?= esc($tab) ?>' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 text-sm font-semibold"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 text-sm font-semibold"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <div class="flex flex-col md:flex-row gap-8">
            <aside class="w-full md:w-64 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 flex flex-col justify-between shadow-xs">
                <div>
                    <div class="flex items-center gap-3 pb-6 mb-6 border-b border-slate-200 dark:border-slate-700">
                        <div class="w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold">
                            <i data-lucide="<?= $isBusiness ? 'store' : 'user' ?>" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-sm text-slate-900 dark:text-white truncate"><?= esc($user['name']) ?></h3>
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                <?= $isBusiness ? lang('App.signup_business_owner') : lang('App.signup_normal_user') ?>
                            </span>
                        </div>
                    </div>

                    <nav class="space-y-1">
                        <button type="button" @click="currentTab = 'profile'"
                                :class="currentTab === 'profile' ? 'btn-primary' : 'btn-ghost'"
                                class="w-full btn btn-sm justify-start">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            <span><?= lang('App.dashboard_my_profile') ?></span>
                        </button>
                        <?php if ($isBusiness): ?>
                        <button type="button" @click="currentTab = 'business'"
                                :class="currentTab === 'business' ? 'btn-primary' : 'btn-ghost'"
                                class="w-full btn btn-sm justify-start">
                            <i data-lucide="store" class="w-4 h-4"></i>
                            <span><?= lang('App.dashboard_my_business_listing') ?></span>
                        </button>
                        <?php endif; ?>
                    </nav>
                </div>

                <div class="pt-6 mt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="<?= base_url('logout') ?>" class="w-full btn btn-sm btn-ghost text-rose-600 dark:text-rose-400 justify-start">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span><?= lang('App.log_out') ?></span>
                    </a>
                </div>
            </aside>

            <div class="flex-grow space-y-6">
                <div x-show="currentTab === 'profile'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1"><?= lang('App.account_information') ?></h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-6"><?= lang('App.manage_contact_details') ?></p>

                    <form action="<?= base_url('dashboard/profile') ?>" method="POST" class="space-y-4 max-w-xl text-xs">
                        <?= csrf_field() ?>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.full_name') ?></label>
                            <input type="text" name="name" required value="<?= esc($user['name']) ?>"
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.phone_label') ?></label>
                            <input type="text" name="phone" required value="<?= esc($user['phone']) ?>"
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white outline-none">
                        </div>
                        <?php if ($isBusiness): ?>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.password') ?> <span class="font-medium text-slate-400">(<?= lang('App.leave_blank_keep') ?>)</span></label>
                            <input type="password" name="password" minlength="6" placeholder="••••••••"
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white outline-none">
                        </div>
                        <?php endif; ?>
                        <div class="pt-2">
                            <button type="submit" class="btn btn-md btn-primary"><?= lang('App.dashboard_save_profile') ?></button>
                        </div>
                    </form>
                </div>

                <?php if ($isBusiness): ?>
                <div x-show="currentTab === 'business'" x-cloak class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1"><?= lang('App.dashboard_my_business_listing') ?></h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400"><?= lang('App.one_business_per_account') ?></p>
                        </div>
                        <?php if (empty($businesses)): ?>
                        <a href="<?= base_url('dashboard/business/create') ?>" class="btn btn-sm btn-primary inline-flex items-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <?= lang('App.add_business_listing') ?>
                        </a>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($businesses)): ?>
                        <div class="p-6 rounded-lg bg-slate-50 dark:bg-slate-900 border border-dashed border-slate-300 dark:border-slate-700 text-center text-sm text-slate-500">
                            <?= lang('App.no_business_listings_yet') ?>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($businesses as $b): ?>
                                <?php
                                    $st = $b['status'] ?? 'pending';
                                    $badge = $st === 'active'
                                        ? 'bg-emerald-100 text-emerald-800'
                                        : ($st === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700');
                                    $name = $isUrdu
                                        ? (($b['name_ur'] ?? '') !== '' ? $b['name_ur'] : ($b['name_en'] ?? ''))
                                        : (($b['name_en'] ?? '') !== '' ? $b['name_en'] : ($b['name_ur'] ?? ''));
                                ?>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                                    <div class="min-w-0">
                                        <p class="font-bold text-sm text-slate-900 dark:text-white truncate"><?= esc($name) ?></p>
                                        <p class="text-xs text-slate-500 mt-1"><?= esc($b['phone'] ?: '—') ?></p>
                                        <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase <?= $badge ?>"><?= esc($st) ?></span>
                                        <?php if ($st === 'pending'): ?>
                                            <p class="text-[11px] text-amber-700 mt-2"><?= lang('App.pending_admin_approval') ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= base_url('dashboard/business/edit/' . $b['id']) ?>" class="btn btn-sm btn-secondary"><?= lang('App.admin_edit') ?: 'Edit' ?></a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
