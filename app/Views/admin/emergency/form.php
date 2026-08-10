<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= esc($pageHeading) ?></h2>
        <a href="<?= base_url('admin/emergency-numbers') ?>" class="px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-300 transition-colors">
            &larr; <?= lang('App.admin_back_to_emergency') ?>
        </a>
    </div>

    <form action="<?= $contact ? base_url('admin/emergency-numbers/edit/' . $contact['id']) : base_url('admin/emergency-numbers/create') ?>" method="POST" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-5">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_category_en_label') ?></label>
                <input type="text" name="category_en" required value="<?= esc($contact['category_en'] ?? '') ?>" placeholder="e.g. Police & Security"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
            </div>

            <div dir="rtl">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">زمرہ (اردو) *</label>
                <input type="text" name="category_ur" value="<?= esc($contact['category_ur'] ?? '') ?>" placeholder="مثلاً: پولیس اور سیکیورٹی"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium font-urdu">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_department_name_en_label') ?></label>
                <input type="text" name="department_name_en" required value="<?= esc($contact['department_name_en'] ?? '') ?>" placeholder="e.g. Police Station Kot Sultan"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
            </div>

            <div dir="rtl">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">محکمہ کا نام (اردو) *</label>
                <input type="text" name="department_name_ur" value="<?= esc($contact['department_name_ur'] ?? '') ?>" placeholder="مثلاً: تھانہ کوٹ سلطان"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium font-urdu">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_primary_phone_label') ?></label>
                <input type="text" name="phone_primary" required value="<?= esc($contact['phone_primary'] ?? '') ?>" placeholder="e.g. 15 or 03001234567"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_secondary_phone') ?></label>
                <input type="text" name="phone_secondary" value="<?= esc($contact['phone_secondary'] ?? '') ?>" placeholder="e.g. 0606-123456"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_lucide_icon_label') ?></label>
                <input type="text" name="icon" value="<?= esc($contact['icon'] ?? 'phone-call') ?>" placeholder="e.g. shield-alert, ambulance"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_working_hours_en_label') ?></label>
                <input type="text" name="working_hours_en" value="<?= esc($contact['working_hours_en'] ?? '') ?>" placeholder="e.g. 24/7 Service"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
            </div>

            <div dir="rtl">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">کام کے اوقات (اردو)</label>
                <input type="text" name="working_hours_ur" value="<?= esc($contact['working_hours_ur'] ?? '') ?>" placeholder="مثلاً: 24 گھنٹے سروس"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium font-urdu">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_google_maps_url') ?></label>
            <input type="text" name="google_maps" value="<?= esc($contact['google_maps'] ?? '') ?>" placeholder="https://maps.google.com/..."
                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono">
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="<?= base_url('admin/emergency-numbers') ?>" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-300 transition-colors">
                <?= lang('App.admin_cancel') ?>
            </a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition-all">
                <?= lang('App.admin_save_contact') ?>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
