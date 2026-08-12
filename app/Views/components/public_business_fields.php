<?php
/**
 * Shared public business listing fields (mirrors admin form, no status control).
 *
 * Expects: $categories, $areas, $villages, optional $business, optional $user
 */
$business = $business ?? null;
$user     = $user ?? null;
$cats = [];
foreach ($categories ?? [] as $c) {
    $cats[] = [
        'id'        => (int) $c['id'],
        'label'     => trim(($c['name_en'] ?? '') !== '' ? $c['name_en'] : ($c['name_ur'] ?? '')),
        'label_alt' => trim(($c['name_ur'] ?? '') . ' ' . ($c['name_en'] ?? '')),
    ];
}
$areaOpts = [];
foreach ($areas ?? [] as $a) {
    $areaOpts[] = [
        'id'        => (int) $a['id'],
        'label'     => trim(($a['name_en'] ?? '') !== '' ? $a['name_en'] : ($a['name_ur'] ?? '')),
        'label_alt' => trim(($a['name_ur'] ?? '') . ' ' . ($a['name_en'] ?? '')),
    ];
}
$villageOpts = [];
foreach ($villages ?? [] as $v) {
    $villageOpts[] = [
        'id'        => (int) $v['id'],
        'label'     => trim(($v['name_en'] ?? '') !== '' ? $v['name_en'] : ($v['name_ur'] ?? '')),
        'label_alt' => trim(($v['name_ur'] ?? '') . ' ' . ($v['name_en'] ?? '')),
    ];
}
$field = 'w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium outline-none focus:border-emerald-500';
?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-4">
    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400"><?= lang('App.admin_section_1') ?></h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_category_label') ?></label>
            <?= view('components/searchable_select', [
                'name'              => 'category_id',
                'options'           => $cats,
                'selected'          => old('category_id', $business['category_id'] ?? ''),
                'required'          => true,
                'placeholder'       => lang('App.admin_select_category'),
                'searchPlaceholder' => lang('App.search_category') ?: 'Search category…',
                'inputClass'        => $field . ' text-start flex items-center justify-between gap-2',
                'extraInputAttrs'   => 'data-biz-field',
            ]) ?>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_area_optional') ?></label>
            <?= view('components/searchable_select', [
                'name'              => 'area_id',
                'options'           => $areaOpts,
                'selected'          => old('area_id', $business['area_id'] ?? ''),
                'required'          => false,
                'placeholder'       => lang('App.admin_select_area'),
                'searchPlaceholder' => lang('App.search_area') ?: 'Search area…',
                'inputClass'        => $field . ' text-start flex items-center justify-between gap-2',
            ]) ?>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_village_optional') ?></label>
            <?= view('components/searchable_select', [
                'name'              => 'village_id',
                'options'           => $villageOpts,
                'selected'          => old('village_id', $business['village_id'] ?? ''),
                'required'          => false,
                'placeholder'       => lang('App.admin_select_village'),
                'searchPlaceholder' => lang('App.search_village') ?: 'Search village…',
                'inputClass'        => $field . ' text-start flex items-center justify-between gap-2',
            ]) ?>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-6">
    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400"><?= lang('App.admin_section_2') ?></h3>

    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="font-bold text-xs text-slate-500 uppercase tracking-wider"><?= lang('App.admin_english_details') ?></div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_business_name_en') ?></label>
            <input type="text" name="name_en" required data-biz-field value="<?= esc(old('name_en', $business['name_en'] ?? '')) ?>" placeholder="e.g. Al-Madina Super Store" class="<?= esc($field) ?>">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_address_en') ?></label>
            <input type="text" name="address_en" value="<?= esc(old('address_en', $business['address_en'] ?? '')) ?>" placeholder="e.g. Main Bazaar, Kot Sultan" class="<?= esc($field) ?>">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_description_en') ?></label>
            <textarea name="description_en" rows="3" placeholder="Overview of services..." class="<?= esc($field) ?>"><?= esc(old('description_en', $business['description_en'] ?? '')) ?></textarea>
        </div>
    </div>

    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="font-bold text-xs text-slate-500 uppercase tracking-wider"><?= lang('App.admin_urdu_details') ?></div>
        <div dir="rtl">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">کاروبار کا نام (اردو)</label>
            <input type="text" name="name_ur" value="<?= esc(old('name_ur', $business['name_ur'] ?? '')) ?>" placeholder="مثلاً: المدینہ سپر اسٹور" class="<?= esc($field) ?> font-urdu">
        </div>
        <div dir="rtl">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">پتہ (اردو)</label>
            <input type="text" name="address_ur" value="<?= esc(old('address_ur', $business['address_ur'] ?? '')) ?>" placeholder="مثلاً: مین بازار، کوٹ سلطان" class="<?= esc($field) ?> font-urdu">
        </div>
        <div dir="rtl">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">تفصیل (اردو)</label>
            <textarea name="description_ur" rows="3" placeholder="خدمات کی تفصیل..." class="<?= esc($field) ?> font-urdu"><?= esc(old('description_ur', $business['description_ur'] ?? '')) ?></textarea>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-4">
    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400"><?= lang('App.admin_section_3') ?></h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_phone_number') ?></label>
            <input type="text" name="phone" value="<?= esc(old('phone', $business['phone'] ?? ($user['phone'] ?? ''))) ?>" placeholder="e.g. 03001234567" class="<?= esc($field) ?> font-mono">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_whatsapp') ?></label>
            <input type="text" name="whatsapp" value="<?= esc(old('whatsapp', $business['whatsapp'] ?? '')) ?>" placeholder="e.g. 923001234567" class="<?= esc($field) ?> font-mono">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_email') ?></label>
            <input type="email" name="email" value="<?= esc(old('email', $business['email'] ?? '')) ?>" placeholder="info@store.com" class="<?= esc($field) ?>">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_website') ?></label>
            <input type="text" name="website" value="<?= esc(old('website', $business['website'] ?? '')) ?>" placeholder="www.store.com" class="<?= esc($field) ?>">
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_business_image') ?></label>
        <input type="file" name="image" accept="image/*"
               class="w-full text-xs text-slate-500 file:me-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-700">
        <?php if (! empty($business['image'])): ?>
            <div class="mt-2 flex items-center gap-3">
                <img src="<?= esc(get_business_image_url($business['image'])) ?>" alt="" class="w-16 h-16 rounded-lg object-cover border border-slate-200">
                <span class="text-xs text-slate-400"><?= lang('App.admin_current') ?> <?= esc($business['image']) ?></span>
            </div>
        <?php endif; ?>
        <p class="mt-1 text-[11px] text-slate-400"><?= lang('App.business_image_hint') ?: 'Optional. JPG/PNG recommended.' ?></p>
    </div>
</div>
