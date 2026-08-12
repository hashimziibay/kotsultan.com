<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="min-h-[calc(100vh-5rem)] relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,_rgba(245,158,11,0.12),_transparent_55%),radial-gradient(ellipse_at_bottom_left,_rgba(16,185,129,0.10),_transparent_45%)]"></div>

    <div class="relative max-w-lg mx-auto px-4 sm:px-6 py-12 lg:py-16">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-500/25 mb-4">
                <i data-lucide="store" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">
                <?= lang('App.list_your_business') ?>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                <?= lang('App.add_business_mobile_step') ?>
            </p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 text-rose-700 text-sm font-semibold border border-rose-200">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('info')): ?>
            <div class="mb-4 px-4 py-3 rounded-xl bg-amber-50 text-amber-800 text-sm font-semibold border border-amber-200">
                <?= esc(session()->getFlashdata('info')) ?>
            </div>
        <?php endif; ?>

        <div class="rounded-[1.75rem] border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-[0_20px_50px_rgba(15,23,42,0.08)] p-6 sm:p-8">
            <form action="<?= base_url('add-business/check') ?>" method="POST" class="space-y-5">
                <?= csrf_field() ?>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                        <?= lang('App.phone_label') ?>
                    </label>
                    <div class="relative">
                        <i data-lucide="smartphone" class="pointer-events-none absolute left-3.5 rtl:left-auto rtl:right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 z-10"></i>
                        <input type="tel" id="phone" name="phone" required autocomplete="tel"
                               value="<?= esc(old('phone')) ?>" placeholder="03XXXXXXXXX"
                               class="w-full pl-11 rtl:pl-3 rtl:pr-11 pr-3 py-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-sm font-medium outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition">
                    </div>
                    <p class="mt-2 text-[11px] text-slate-400"><?= lang('App.add_business_mobile_hint') ?></p>
                </div>

                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-extrabold text-sm shadow-lg shadow-amber-500/25 transition">
                    <?= lang('App.continue') ?: 'Continue' ?>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 text-center text-sm text-slate-500">
                <?= lang('App.already_have_account') ?>
                <a href="<?= base_url('login') ?>" class="font-bold text-emerald-600 hover:underline"><?= lang('App.signup_sign_in_instead') ?></a>
            </div>
        </div>

        <p class="mt-5 text-center text-xs text-slate-400">
            <?= lang('App.one_business_per_account') ?>
        </p>
    </div>
</div>
<?= $this->endSection() ?>
