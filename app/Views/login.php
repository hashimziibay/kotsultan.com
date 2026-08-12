<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
    $prefillPhone = old('phone', $prefillPhone ?? '');
    $intent = $intent ?? '';
?>
<div class="min-h-[calc(100vh-5rem)] relative overflow-hidden">
    <!-- Soft atmosphere -->
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,_rgba(16,185,129,0.12),_transparent_55%),radial-gradient(ellipse_at_bottom_right,_rgba(245,158,11,0.08),_transparent_45%)] dark:bg-[radial-gradient(ellipse_at_top,_rgba(16,185,129,0.15),_transparent_50%)]"></div>
    <div class="pointer-events-none absolute -top-24 -end-24 w-80 h-80 rounded-full bg-emerald-400/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-28 -start-20 w-96 h-96 rounded-full bg-amber-400/10 blur-3xl"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">

            <!-- Brand panel -->
            <div class="order-2 lg:order-1 text-center lg:text-start">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/70 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-wider mb-5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <?= lang('App.brand_name') ?>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-extrabold text-slate-900 dark:text-white tracking-tight leading-[1.15] mb-4">
                    <?= lang('App.login_welcome_title') ?>
                </h1>
                <p class="text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-md mx-auto lg:mx-0 mb-8">
                    <?= lang('App.login_branding_text') ?>
                </p>

                <div class="grid sm:grid-cols-2 gap-3 max-w-lg mx-auto lg:mx-0 text-start">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/60 p-4">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 flex items-center justify-center mb-3">
                            <i data-lucide="store" class="w-4 h-4"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white mb-1"><?= lang('App.signup_business_owner') ?></p>
                        <p class="text-xs text-slate-500 leading-relaxed"><?= lang('App.login_business_tip') ?></p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/60 p-4">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 flex items-center justify-center mb-3">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white mb-1"><?= lang('App.login_secure_access') ?></p>
                        <p class="text-xs text-slate-500 leading-relaxed"><?= lang('App.login_secure_tip') ?></p>
                    </div>
                </div>
            </div>

            <!-- Form card -->
            <div class="order-1 lg:order-2">
                <div class="rounded-[1.75rem] border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-[0_20px_50px_rgba(15,23,42,0.08)] p-6 sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight"><?= lang('App.login_title') ?></h2>
                        <p class="text-sm text-slate-500 mt-1"><?= lang('App.login_form_subtitle') ?></p>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-sm font-semibold border border-rose-200 dark:border-rose-900">
                            <?= esc(session()->getFlashdata('error')) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-sm font-semibold border border-emerald-200 dark:border-emerald-900">
                            <?= esc(session()->getFlashdata('success')) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('info')): ?>
                        <div class="mb-4 px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-200 text-sm font-semibold border border-amber-200 dark:border-amber-900">
                            <?= esc(session()->getFlashdata('info')) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('login') ?>" method="POST" class="space-y-4">
                        <?= csrf_field() ?>
                        <?php if ($intent !== ''): ?>
                            <input type="hidden" name="intent" value="<?= esc($intent) ?>">
                        <?php endif; ?>

                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5"><?= lang('App.phone_label') ?></label>
                            <div class="relative">
                                <i data-lucide="smartphone" class="pointer-events-none absolute left-3.5 rtl:left-auto rtl:right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 z-10"></i>
                                <input type="tel" id="phone" name="phone" required autocomplete="tel"
                                       value="<?= esc($prefillPhone) ?>" placeholder="03XXXXXXXXX"
                                       class="w-full pl-11 rtl:pl-3 rtl:pr-11 pr-3 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                                <?= lang('App.password') ?>
                                <span class="font-medium text-slate-400">· <?= lang('App.business_accounts_only') ?></span>
                            </label>
                            <div class="relative" x-data="{ show: false }">
                                <i data-lucide="lock" class="pointer-events-none absolute left-3.5 rtl:left-auto rtl:right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 z-10"></i>
                                <input :type="show ? 'text' : 'password'" id="password" name="password" autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="w-full pl-11 pr-10 rtl:pr-11 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition">
                                <button type="button" @click="show = !show" class="absolute right-3.5 rtl:right-auto rtl:left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 z-10" aria-label="Toggle password">
                                    <i data-lucide="eye" x-show="!show" class="w-4 h-4"></i>
                                    <i data-lucide="eye-off" x-show="show" x-cloak class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-sm shadow-lg shadow-emerald-600/20 transition active:scale-[0.99]">
                            <?= lang('App.sign_in') ?>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </form>

                    <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3 text-center text-sm">
                        <p class="text-slate-500">
                            <?= lang('App.dont_have_account') ?>
                            <a href="<?= base_url('signup') ?>" class="font-bold text-emerald-600 hover:underline"><?= lang('App.signup_title') ?></a>
                        </p>
                        <a href="<?= base_url('add-business') ?>"
                           class="inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50/70 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 font-bold text-xs hover:bg-emerald-100 dark:hover:bg-emerald-950/50 transition">
                            <i data-lucide="store" class="w-3.5 h-3.5"></i>
                            <?= lang('App.list_your_business') ?>
                        </a>
                    </div>
                </div>

                <p class="text-center text-[11px] text-slate-400 mt-5">
                    <?= lang('App.login_need_help') ?> <span class="font-semibold text-slate-600 dark:text-slate-300" dir="ltr">info@kotsultan.com</span>
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
