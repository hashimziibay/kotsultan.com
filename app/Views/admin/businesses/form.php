<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= esc($pageHeading) ?></h2>
        <a href="<?= base_url('admin/businesses') ?>" class="px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-300 transition-colors">
            &larr; <?= lang('App.admin_back_to_listings') ?>
        </a>
    </div>

    <form action="<?= $business ? base_url('admin/businesses/edit/' . $business['id']) : base_url('admin/businesses/create') ?>" 
          method="POST" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>

        <!-- Category & Location Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider rtl:tracking-normal text-emerald-600 dark:text-emerald-400"><?= lang('App.admin_section_1') ?></h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_category_label') ?></label>
                    <select name="category_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                        <option value=""><?= lang('App.admin_select_category') ?></option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($business['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= esc($c['name_en'] ?: $c['name_ur']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_area_optional') ?></label>
                    <select name="area_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                        <option value=""><?= lang('App.admin_select_area') ?></option>
                        <?php foreach ($areas as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= ($business['area_id'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                            <?= esc($a['name_en'] ?: $a['name_ur']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_village_optional') ?></label>
                    <select name="village_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                        <option value=""><?= lang('App.admin_select_village') ?></option>
                        <?php foreach ($villages as $v): ?>
                        <option value="<?= $v['id'] ?>" <?= ($business['village_id'] ?? '') == $v['id'] ? 'selected' : '' ?>>
                            <?= esc($v['name_en'] ?: $v['name_ur']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Bilingual Information Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-6">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider rtl:tracking-normal text-emerald-600 dark:text-emerald-400"><?= lang('App.admin_section_2') ?></h3>

            <!-- English Section -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-4">
                <div class="font-bold text-xs text-slate-500 uppercase tracking-wider rtl:tracking-normal"><?= lang('App.admin_english_details') ?></div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_business_name_en') ?></label>
                    <input type="text" name="name_en" required value="<?= esc($business['name_en'] ?? '') ?>" placeholder="e.g. Al-Madina Super Store"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_address_en') ?></label>
                    <input type="text" name="address_en" value="<?= esc($business['address_en'] ?? '') ?>" placeholder="e.g. Main Bazaar, Kot Sultan"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_description_en') ?></label>
                    <textarea name="description_en" rows="3" placeholder="Overview of services..."
                              class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium"><?= esc($business['description_en'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Urdu Section -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-4">
                <div class="font-bold text-xs text-slate-500 uppercase tracking-wider rtl:tracking-normal"><?= lang('App.admin_urdu_details') ?></div>
                
                <div dir="rtl">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">کاروبار کا نام (اردو)</label>
                    <input type="text" name="name_ur" value="<?= esc($business['name_ur'] ?? '') ?>" placeholder="مثلاً: المدینہ سپر اسٹور"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium font-urdu">
                </div>

                <div dir="rtl">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">پتہ (اردو)</label>
                    <input type="text" name="address_ur" value="<?= esc($business['address_ur'] ?? '') ?>" placeholder="مثلاً: مین بازار، کوٹ سلطان"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium font-urdu">
                </div>

                <div dir="rtl">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">تفصیل (اردو)</label>
                    <textarea name="description_ur" rows="3" placeholder="خدمات کی تفصیل..."
                              class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium font-urdu"><?= esc($business['description_ur'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Contact & Media Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider rtl:tracking-normal text-emerald-600 dark:text-emerald-400"><?= lang('App.admin_section_3') ?></h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_phone_number') ?></label>
                    <input type="text" name="phone" value="<?= esc($business['phone'] ?? '') ?>" placeholder="e.g. 03001234567"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_whatsapp') ?></label>
                    <input type="text" name="whatsapp" value="<?= esc($business['whatsapp'] ?? '') ?>" placeholder="e.g. 923001234567"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_email') ?></label>
                    <input type="email" name="email" value="<?= esc($business['email'] ?? '') ?>" placeholder="info@store.com"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_website') ?></label>
                    <input type="text" name="website" value="<?= esc($business['website'] ?? '') ?>" placeholder="www.store.com"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_listing_status') ?></label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold">
                        <option value="active" <?= ($business['status'] ?? 'active') === 'active' ? 'selected' : '' ?>><?= lang('App.admin_active_status') ?></option>
                        <option value="pending" <?= ($business['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending (app review)</option>
                        <option value="inactive" <?= ($business['status'] ?? '') === 'inactive' ? 'selected' : '' ?>><?= lang('App.admin_inactive_status') ?></option>
                    </select>
                    <?php if (!empty($ownerAppUser)): ?>
                        <p class="mt-2 text-[11px] text-slate-500">
                            Submitted by app account:
                            <a class="font-bold text-emerald-600 hover:underline" href="<?= base_url('admin/app-users/' . (int) $ownerAppUser['id']) ?>">
                                <?= esc($ownerAppUser['name']) ?> (<?= esc($ownerAppUser['phone']) ?>)
                            </a>
                        </p>
                    <?php elseif (!empty($business['user_id'])): ?>
                        <p class="mt-2 text-[11px] text-slate-500">
                            Linked app user id: #<?= (int) $business['user_id'] ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_business_image') ?></label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-700">
                    <?php if (!empty($business['image'])): ?>
                    <div class="mt-2 text-xs text-slate-400"><?= lang('App.admin_current') ?> <?= esc($business['image']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="<?= base_url('admin/businesses') ?>" class="px-5 py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-300 transition-colors">
                <?= lang('App.admin_cancel') ?>
            </a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition-all">
                <?= lang('App.admin_save_business') ?>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
