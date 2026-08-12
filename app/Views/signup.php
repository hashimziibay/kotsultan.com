<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
    $defaultRole   = $defaultRole ?? old('account_type', 'user');
    $verifiedPhone = $verifiedPhone ?? '';
    $lockPhone     = ! empty($lockPhone);
    $forceBusiness = $defaultRole === 'business' && $verifiedPhone !== '';
?>
<div class="min-h-screen w-full bg-slate-50 dark:bg-slate-900 pt-24 pb-12 px-4"
     x-data="{ role: '<?= esc($forceBusiness ? 'business' : $defaultRole) ?>' }">

    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-600 text-white shadow-lg mb-4">
                <i data-lucide="<?= $forceBusiness ? 'store' : 'map-pin' ?>" class="w-6 h-6"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight mb-2">
                <?= $forceBusiness ? lang('App.list_your_business') : lang('App.signup_create_account') ?>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium">
                <?= $forceBusiness ? lang('App.business_submit_hint') : lang('App.signup_join_community') ?>
            </p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 px-4 py-3 rounded-xl bg-rose-50 text-rose-700 text-sm font-semibold border border-rose-200"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-semibold border border-emerald-200"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <?php if (! $forceBusiness): ?>
        <div class="flex p-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl mb-6 max-w-xl mx-auto">
            <button type="button" @click="role = 'user'"
                    :class="role === 'user' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                    class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                <i data-lucide="user" class="w-4 h-4"></i> <?= lang('App.signup_normal_user') ?>
            </button>
            <a href="<?= base_url('add-business') ?>"
               class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 text-slate-500 hover:text-slate-900 dark:hover:text-white">
                <i data-lucide="store" class="w-4 h-4"></i> <?= lang('App.signup_business_owner') ?>
            </a>
        </div>
        <?php endif; ?>

        <form action="<?= base_url('signup') ?>" method="POST" enctype="multipart/form-data" class="space-y-6"
              @submit="if (role !== 'business') { $el.querySelectorAll('[data-biz-field]').forEach(el => el.removeAttribute('required')) }">
            <?= csrf_field() ?>
            <input type="hidden" name="account_type" :value="role">

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-600"><?= lang('App.account_information') ?></h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.full_name') ?></label>
                        <input type="text" name="name" required value="<?= esc(old('name')) ?>"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.phone_label') ?></label>
                        <?php if ($lockPhone && $verifiedPhone !== ''): ?>
                            <input type="tel" name="account_phone" readonly value="<?= esc($verifiedPhone) ?>"
                                   class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-bold outline-none">
                            <p class="mt-1 text-[11px] text-emerald-600 font-semibold">
                                <?= lang('App.phone_verified_locked') ?>
                                <a href="<?= base_url('add-business') ?>" class="underline"><?= lang('App.change') ?: 'Change' ?></a>
                            </p>
                        <?php else: ?>
                            <input type="tel" name="account_phone" required value="<?= esc(old('account_phone', old('phone'))) ?>" placeholder="03XXXXXXXXX"
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium outline-none focus:border-emerald-500">
                            <p class="mt-1 text-[11px] text-slate-400"><?= lang('App.account_phone_hint') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div x-show="role === 'business'" x-cloak>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.password') ?></label>
                    <div class="relative max-w-md" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" name="password" :required="role === 'business'" minlength="6"
                               placeholder="<?= lang('App.signup_password_placeholder') ?>"
                               class="w-full px-3 py-2 pe-10 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium outline-none focus:border-emerald-500">
                        <button type="button" @click="show = !show" class="absolute end-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <i data-lucide="eye" x-show="!show" class="w-4 h-4"></i>
                            <i data-lucide="eye-off" x-show="show" x-cloak class="w-4 h-4"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400"><?= lang('App.business_password_hint') ?></p>
                </div>
            </div>

            <div x-show="role === 'business'" x-cloak class="space-y-6">
                <?= view('components/public_business_fields', [
                    'categories' => $categories ?? [],
                    'areas'      => $areas ?? [],
                    'villages'   => $villages ?? [],
                    'business'   => null,
                    'user'       => ['phone' => $verifiedPhone],
                ]) ?>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                <p class="text-sm text-slate-500">
                    <?= lang('App.already_have_account') ?>
                    <a href="<?= base_url('login') ?>" class="text-emerald-600 font-bold hover:underline"><?= lang('App.signup_sign_in_instead') ?></a>
                </p>
                <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-sm rounded-xl shadow-md inline-flex items-center justify-center gap-2">
                    <span x-text="role === 'business' ? '<?= esc(lang('App.submit_for_review')) ?>' : '<?= esc(lang('App.signup_create_account_button')) ?>'"></span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
