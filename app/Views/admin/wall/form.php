<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= esc($pageHeading) ?></h2>
        <a href="<?= base_url('admin/wall-of-kot-sultan') ?>" class="px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-300 transition-colors">
            &larr; <?= lang('App.admin_back_to_wall') ?>
        </a>
    </div>

    <form action="<?= $person ? base_url('admin/wall-of-kot-sultan/edit/' . $person['id']) : base_url('admin/wall-of-kot-sultan/create') ?>" 
          method="POST" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider rtl:tracking-normal text-emerald-600 dark:text-emerald-400"><?= lang('App.admin_wall_section_1') ?></h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_category_label') ?></label>
                    <select name="category_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                        <option value=""><?= lang('App.admin_select_category') ?></option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($person['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= esc($c['name_en'] ?: $c['name_ur']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_years_service') ?></label>
                    <input type="text" name="years_of_service" value="<?= esc($person['years_of_service'] ?? '') ?>" placeholder="e.g. 1980 - 2015"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_featured_member') ?></label>
                    <label class="flex items-center gap-2 pt-2 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-300">
                        <input type="checkbox" name="featured" value="1" <?= (!empty($person['featured'])) ? 'checked' : '' ?> class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span><?= lang('App.admin_show_hero') ?></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider rtl:tracking-normal text-emerald-600 dark:text-emerald-400"><?= lang('App.admin_wall_section_2') ?></h3>

            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-4">
                <div class="font-bold text-xs text-slate-500 uppercase tracking-wider rtl:tracking-normal"><?= lang('App.admin_english_personality_info') ?></div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_name_en_label') ?></label>
                    <input type="text" name="name_en" required value="<?= esc($person['name_en'] ?? '') ?>" placeholder="e.g. Dr. Abdul Qadeer Khan"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_profession_en_label') ?></label>
                    <input type="text" name="profession_en" value="<?= esc($person['profession_en'] ?? '') ?>" placeholder="e.g. Nuclear Scientist & Philanthropist"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_bio_en_label') ?></label>
                    <textarea name="intro_en" rows="3" placeholder="Biography overview..."
                              class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium"><?= esc($person['intro_en'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-4" dir="rtl">
                <div class="font-bold text-xs text-slate-500 uppercase tracking-wider rtl:tracking-normal text-right"><?= lang('App.admin_urdu_details') ?></div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">نام (اردو) *</label>
                    <input type="text" name="name_ur" value="<?= esc($person['name_ur'] ?? '') ?>" placeholder="مثلاً: ڈاکٹر عبد القدیر خان"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium font-urdu">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">پیشہ (اردو)</label>
                    <input type="text" name="profession_ur" value="<?= esc($person['profession_ur'] ?? '') ?>" placeholder="مثلاً: ایٹمی سائنسدان"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium font-urdu">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">سوانح حیات (اردو)</label>
                    <textarea name="intro_ur" rows="3" placeholder="سوانح حیات..."
                              class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium font-urdu"><?= esc($person['intro_ur'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider rtl:tracking-normal text-emerald-600 dark:text-emerald-400"><?= lang('App.admin_wall_section_3') ?></h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_photo_upload') ?></label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.status') ?></label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold">
                        <option value="active" <?= ($person['status'] ?? 'active') === 'active' ? 'selected' : '' ?>><?= lang('App.admin_active_status') ?></option>
                        <option value="inactive" <?= ($person['status'] ?? '') === 'inactive' ? 'selected' : '' ?>><?= lang('App.admin_inactive_status') ?></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="<?= base_url('admin/wall-of-kot-sultan') ?>" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-300 transition-colors">
                <?= lang('App.admin_cancel') ?>
            </a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition-all">
                <?= lang('App.admin_save_personality') ?>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
