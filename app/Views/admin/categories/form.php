<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= esc($pageHeading) ?></h2>
        <a href="<?= base_url('admin/categories') ?>" class="px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-300 transition-colors">
            &larr; <?= lang('App.admin_back_to_categories') ?>
        </a>
    </div>

    <form action="<?= $category ? base_url('admin/categories/edit/' . $category['id']) : base_url('admin/categories/create') ?>" method="POST" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-5">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_category_name_en') ?></label>
            <input type="text" name="name_en" required value="<?= esc($category['name_en'] ?? '') ?>" placeholder="e.g. Hospitals"
                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
        </div>

        <div dir="rtl">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">زمرہ کا نام (اردو) *</label>
            <input type="text" name="name_ur" required value="<?= esc($category['name_ur'] ?? '') ?>" placeholder="مثلاً: ہسپتال"
                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium font-urdu">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_lucide_icon') ?></label>
                <input type="text" name="icon" value="<?= esc($category['icon'] ?? 'folder') ?>" placeholder="e.g. ambulance, store, heart"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.display_order') ?></label>
                <input type="number" name="display_order" value="<?= esc($category['display_order'] ?? 0) ?>"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.status') ?></label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold">
                    <option value="active" <?= ($category['status'] ?? 'active') === 'active' ? 'selected' : '' ?>><?= lang('App.admin_active_status') ?></option>
                    <option value="inactive" <?= ($category['status'] ?? '') === 'inactive' ? 'selected' : '' ?>><?= lang('App.admin_inactive_status') ?></option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="<?= base_url('admin/categories') ?>" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-300 transition-colors">
                <?= lang('App.admin_cancel') ?>
            </a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition-all">
                <?= lang('App.admin_save_category') ?>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
