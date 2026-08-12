<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= esc($pageHeading) ?></h2>
        <a href="<?= base_url('admin/wall-categories') ?>" class="px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-300 transition-colors">
            &larr; <?= lang('App.admin_back_to_wall_categories') ?>
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 text-rose-700 text-sm font-semibold"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <form action="<?= $category ? base_url('admin/wall-categories/edit/' . $category['id']) : base_url('admin/wall-categories/create') ?>" method="POST" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-5">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_category_name_en') ?></label>
            <input type="text" name="name_en" required value="<?= esc(old('name_en', $category['name_en'] ?? '')) ?>" placeholder="e.g. Education"
                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
        </div>

        <div dir="rtl">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">زمرہ کا نام (اردو) *</label>
            <input type="text" name="name_ur" required value="<?= esc(old('name_ur', $category['name_ur'] ?? '')) ?>" placeholder="مثلاً: تعلیم"
                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium font-urdu">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_lucide_icon') ?></label>
                <input type="text" name="icon" value="<?= esc(old('icon', $category['icon'] ?? 'user')) ?>" placeholder="e.g. graduation-cap, landmark"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                <p class="mt-1 text-[10px] text-slate-400"><?= lang('App.admin_lucide_icon_hint') ?: 'Lucide icon name' ?></p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_wall_category_color') ?></label>
                <select name="color" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold">
                    <?php
                        $colors = ['emerald', 'blue', 'rose', 'amber', 'indigo', 'teal', 'sky', 'violet', 'orange', 'slate'];
                        $selectedColor = old('color', $category['color'] ?? 'emerald');
                    ?>
                    <?php foreach ($colors as $color): ?>
                        <option value="<?= $color ?>" <?= $selectedColor === $color ? 'selected' : '' ?>><?= ucfirst($color) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.display_order') ?></label>
                <input type="number" name="display_order" value="<?= esc(old('display_order', $category['display_order'] ?? 0)) ?>"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.status') ?></label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold">
                    <option value="active" <?= old('status', $category['status'] ?? 'active') === 'active' ? 'selected' : '' ?>><?= lang('App.admin_active_status') ?></option>
                    <option value="inactive" <?= old('status', $category['status'] ?? '') === 'inactive' ? 'selected' : '' ?>><?= lang('App.admin_inactive_status') ?></option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="<?= base_url('admin/wall-categories') ?>" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-300 transition-colors">
                <?= lang('App.admin_cancel') ?>
            </a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition-all">
                <?= lang('App.admin_save_category') ?>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
