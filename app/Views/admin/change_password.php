<?php
    $lang = service('request')->getLocale();
    $isRtl = ($lang === 'ur');
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Temporary Password - Kot Sultan Admin</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full bg-slate-950 text-slate-100 flex items-center justify-center p-4 font-sans antialiased">

    <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-black text-2xl mx-auto mb-3 shadow-md">
                <i data-lucide="key" class="w-6 h-6"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight"><?= lang('App.admin_security_action') ?></h1>
            <p class="text-xs font-semibold text-amber-400 uppercase tracking-widest mt-1"><?= lang('App.admin_change_temp_password') ?></p>
        </div>

        <div class="p-4 mb-6 rounded-xl bg-amber-950/60 border border-amber-800/80 text-amber-200 text-xs leading-relaxed">
            <?= lang('App.admin_temp_password_warning', ['Admin@12345']) ?>
        </div>

        <!-- Flash Messages -->
        <?php if (session('error')): ?>
        <div class="mb-6 p-4 rounded-xl bg-rose-950/60 border border-rose-800/80 text-rose-300 text-xs font-semibold flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400 flex-shrink-0"></i>
            <span><?= esc(session('error')) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="<?= base_url('admin/change-password') ?>" method="POST" class="space-y-5">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider rtl:tracking-normal text-slate-300 mb-2"><?= lang('App.admin_new_permanent_password') ?></label>
                <div class="relative">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-500 absolute left-3.5 rtl:left-auto rtl:right-3.5 top-3.5"></i>
                    <input type="password" name="new_password" required minlength="8" placeholder="<?= lang('App.admin_new_password_min') ?>"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider rtl:tracking-normal text-slate-300 mb-2"><?= lang('App.admin_confirm_new_password') ?></label>
                <div class="relative">
                    <i data-lucide="shield-check" class="w-4 h-4 text-slate-500 absolute left-3.5 rtl:left-auto rtl:right-3.5 top-3.5"></i>
                    <input type="password" name="confirm_password" required minlength="8" placeholder="<?= lang('App.admin_confirm_new_password') ?>"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-500 text-slate-950 font-extrabold text-sm rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                <span><?= lang('App.admin_update_password_continue') ?></span>
                <i data-lucide="<?= $isRtl ? 'arrow-left' : 'arrow-right' ?>" class="w-4 h-4"></i>
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>
</body>
</html>
