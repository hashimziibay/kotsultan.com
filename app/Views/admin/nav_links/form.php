<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="mb-8">
    <div class="flex items-center gap-3 text-sm text-slate-500 mb-2">
        <a href="<?= base_url('admin/nav-links') ?>" class="hover:text-emerald-600 transition-colors">Menu Management</a>
        <i data-lucide="chevron-right" class="w-3 h-3 rtl:rotate-180"></i>
        <span class="text-slate-800 dark:text-slate-200 font-semibold"><?= $title ?></span>
    </div>
    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white"><?= $title ?></h2>
</div>

<?php if (session()->has('errors')): ?>
    <div class="p-4 mb-6 text-sm text-red-800 bg-red-50 rounded-xl dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800">
        <ul class="list-disc list-inside">
            <?php foreach (session('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden max-w-3xl">
    <form action="<?= isset($link) ? base_url('admin/nav-links/edit/' . $link['id']) : base_url('admin/nav-links/create') ?>" method="post" class="p-6 sm:p-8 space-y-6">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- English Title -->
            <div class="space-y-2">
                <label for="title_en" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider rtl:tracking-normal">English Title <span class="text-red-500">*</span></label>
                <input type="text" id="title_en" name="title_en" value="<?= old('title_en', $link['title_en'] ?? '') ?>" required
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all dark:text-white"
                       placeholder="e.g. Home">
            </div>

            <!-- Urdu Title -->
            <div class="space-y-2">
                <label for="title_ur" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider rtl:tracking-normal">Urdu Title <span class="text-red-500">*</span></label>
                <input type="text" id="title_ur" name="title_ur" value="<?= old('title_ur', $link['title_ur'] ?? '') ?>" required dir="rtl"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all dark:text-white font-urdu"
                       placeholder="e.g. صفحہ اول">
            </div>
        </div>

        <div class="space-y-2">
            <label for="url" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider rtl:tracking-normal">URL Destination <span class="text-red-500">*</span></label>
            <input type="text" id="url" name="url" value="<?= old('url', $link['url'] ?? '') ?>" required
                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all dark:text-white font-mono text-xs"
                   placeholder="e.g. / or directory or https://example.com">
            <p class="text-[11px] text-slate-500">For internal links, use the path (e.g. <code>directory</code> or <code>/</code>). Do not use javascript: links.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sort Order -->
            <div class="space-y-2">
                <label for="sort_order" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider rtl:tracking-normal"><?= lang('App.display_order') ?> <span class="text-red-500">*</span></label>
                <input type="number" id="sort_order" name="sort_order" value="<?= old('sort_order', $link['sort_order'] ?? '0') ?>" required min="1"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all dark:text-white font-mono">
            </div>

            <!-- Status -->
            <div class="space-y-2">
                <label for="status" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider rtl:tracking-normal"><?= lang('App.status') ?></label>
                <select id="status" name="status" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all dark:text-white">
                    <option value="active" <?= old('status', $link['status'] ?? 'active') === 'active' ? 'selected' : '' ?>><?= lang('App.admin_active_visible') ?></option>
                    <option value="inactive" <?= old('status', $link['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>><?= lang('App.admin_inactive_hidden') ?></option>
                </select>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
            <a href="<?= base_url('admin/nav-links') ?>" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"><?= lang('App.admin_cancel') ?></a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-bold transition-all shadow-xs">
                <?= isset($link) ? lang('App.admin_update_menu_item') : lang('App.admin_add_menu_item') ?>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
