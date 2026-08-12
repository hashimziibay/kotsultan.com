<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
    $isUrdu = ($lang === 'ur');
    $field = 'w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium outline-none focus:border-emerald-500';
    $selectedIds = $selectedCategoryIds ?? [];
    $categoryOptions = [];
    foreach ($categories as $c) {
        $label = $isUrdu
            ? (($c['name_ur'] ?? '') !== '' ? $c['name_ur'] : ($c['name_en'] ?? ''))
            : (($c['name_en'] ?? '') !== '' ? $c['name_en'] : ($c['name_ur'] ?? ''));
        $categoryOptions[] = [
            'id'        => (int) $c['id'],
            'label'     => trim((string) $label),
            'label_alt' => trim(($c['name_ur'] ?? '') . ' ' . ($c['name_en'] ?? '')),
        ];
    }
    $socialPlatforms = $socialPlatforms ?? \App\Models\WallModel::socialPlatforms();
?>

<section class="relative bg-gradient-to-b from-emerald-50/50 to-slate-50 dark:from-slate-900 dark:to-slate-900 py-10 md:py-14 border-b border-slate-200/80 dark:border-slate-800">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <a href="<?= base_url('wall-of-kot-sultan') ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-emerald-600 mb-4 transition-colors">
            <i data-lucide="<?= $isUrdu ? 'arrow-right' : 'arrow-left' ?>" class="w-4 h-4"></i>
            <span><?= lang('App.wall_title') ?></span>
        </a>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">
            <?= lang('App.wall_submit_title') ?>
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl mx-auto">
            <?= lang('App.wall_submit_subtitle') ?>
        </p>
    </div>
</section>

<section class="py-10 md:py-12 bg-white dark:bg-slate-900">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 text-sm font-semibold border border-emerald-200 dark:border-emerald-800">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 px-4 py-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-sm font-semibold border border-rose-200 dark:border-rose-800">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('wall-of-kot-sultan/submit') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>

            <!-- Honeypot -->
            <div class="hidden" aria-hidden="true">
                <label>Website</label>
                <input type="text" name="website_url" tabindex="-1" autocomplete="off">
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 sm:p-6 space-y-4">
                <h2 class="text-sm font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400"><?= lang('App.wall_submit_section_person') ?></h2>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_category_label') ?> *</label>
                    <?= view('components/searchable_multi_select', [
                        'name'              => 'category_ids',
                        'options'           => $categoryOptions,
                        'selected'          => $selectedIds,
                        'required'          => true,
                        'placeholder'       => lang('App.admin_select_category'),
                        'searchPlaceholder' => lang('App.search_category') ?: 'Search category…',
                        'inputClass'        => $field,
                    ]) ?>
                    <p class="mt-1 text-[11px] text-slate-400"><?= lang('App.admin_wall_categories_hint') ?: 'Select one or more categories. The first selected is the primary category.' ?></p>
                </div>

                <div>
                    <?= view('components/rich_text_editor', [
                        'name'          => 'years_of_service',
                        'value'         => old('years_of_service'),
                        'label'         => lang('App.admin_years_service'),
                        'hint'          => lang('App.admin_years_service_html_hint') ?: 'Use the toolbar to bold, color, lists, and links.',
                        'placeholder'   => 'e.g. Served as headmaster from 1980 to 2015…',
                        'textareaClass' => $field,
                        'height'        => 220,
                        'maxChars'      => 2000,
                    ]) ?>
                </div>

                <div class="p-4 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 space-y-4">
                    <div class="font-bold text-xs text-slate-500 uppercase tracking-wider"><?= lang('App.admin_english_personality_info') ?></div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_name_en_label') ?> *</label>
                        <input type="text" name="name_en" required value="<?= esc(old('name_en')) ?>" placeholder="e.g. Dr. Ahmad Ali"
                               class="<?= esc($field) ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_profession_en_label') ?></label>
                        <input type="text" name="profession_en" value="<?= esc(old('profession_en')) ?>" placeholder="e.g. Teacher / Social Worker"
                               class="<?= esc($field) ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_bio_en_label') ?></label>
                        <textarea name="intro_en" rows="4" placeholder="<?= esc(lang('App.wall_submit_bio_placeholder')) ?>"
                                  class="<?= esc($field) ?>"><?= esc(old('intro_en')) ?></textarea>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 space-y-4" dir="rtl">
                    <div class="font-bold text-xs text-slate-500 uppercase tracking-wider text-right"><?= lang('App.admin_urdu_details') ?></div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">نام (اردو)</label>
                        <input type="text" name="name_ur" value="<?= esc(old('name_ur')) ?>" placeholder="مثلاً: ڈاکٹر احمد علی"
                               class="<?= esc($field) ?> font-urdu">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">پیشہ (اردو)</label>
                        <input type="text" name="profession_ur" value="<?= esc(old('profession_ur')) ?>" placeholder="مثلاً: استاد"
                               class="<?= esc($field) ?> font-urdu">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1 text-right">سوانح حیات (اردو)</label>
                        <textarea name="intro_ur" rows="4" placeholder="مختصر تعارف..."
                                  class="<?= esc($field) ?> font-urdu"><?= esc(old('intro_ur')) ?></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_photo_upload') ?></label>
                    <input type="file" name="photo" accept="image/*"
                           class="w-full text-xs text-slate-500 file:me-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-700">
                    <p class="mt-1 text-[11px] text-slate-400"><?= lang('App.wall_submit_photo_hint') ?></p>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 sm:p-6 space-y-5">
                <h2 class="text-sm font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400"><?= lang('App.wall_submit_section_media') ?></h2>

                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider"><?= lang('App.admin_external_links') ?></h3>
                            <p class="text-[11px] text-slate-500 mt-1"><?= lang('App.admin_external_links_hint') ?></p>
                        </div>
                        <button type="button" id="add-ext-link-row" class="shrink-0 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200 dark:border-emerald-800">
                            + <?= lang('App.admin_add_link') ?>
                        </button>
                    </div>
                    <div id="ext-link-rows" class="space-y-2">
                        <div class="ext-link-row rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 p-3 space-y-2">
                            <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1"><?= lang('App.admin_link_url_label') ?></label>
                                    <input type="text" name="ext_link_url[]" value=""
                                           placeholder="https://example.com/article"
                                           inputmode="url" autocomplete="url"
                                           class="<?= esc($field) ?>">
                                </div>
                                <div class="flex sm:items-end">
                                    <button type="button" class="remove-ext-link-row w-full sm:w-auto px-3 py-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold">
                                        <?= lang('App.admin_remove') ?>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1"><?= lang('App.admin_link_title_label') ?></label>
                                <input type="text" name="ext_link_title[]" value=""
                                       placeholder="<?= esc(lang('App.admin_link_title_placeholder')) ?>"
                                       class="<?= esc($field) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 dark:border-slate-700 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider"><?= lang('App.admin_social_links') ?></h3>
                            <p class="text-[11px] text-slate-500 mt-1"><?= lang('App.admin_social_links_hint') ?></p>
                        </div>
                        <button type="button" id="add-social-link-row" class="shrink-0 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200 dark:border-emerald-800">
                            + <?= lang('App.admin_add_social_link') ?>
                        </button>
                    </div>
                    <div id="social-link-rows" class="space-y-2">
                        <div class="social-link-row rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 p-3 space-y-2">
                            <div class="grid grid-cols-1 sm:grid-cols-[minmax(10rem,0.8fr)_1.4fr_auto] gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1"><?= lang('App.admin_select_platform') ?></label>
                                    <select name="social_link_platform[]" class="<?= esc($field) ?>">
                                        <option value=""><?= esc(lang('App.admin_select_platform')) ?></option>
                                        <?php foreach ($socialPlatforms as $key => $meta): ?>
                                            <option value="<?= esc($key) ?>"><?= esc($meta['label']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1"><?= lang('App.admin_link_url_label') ?></label>
                                    <input type="text" name="social_link_url[]" value=""
                                           placeholder="https://..."
                                           inputmode="url" autocomplete="url"
                                           class="<?= esc($field) ?>">
                                </div>
                                <div class="flex sm:items-end">
                                    <button type="button" class="remove-social-link-row w-full sm:w-auto px-3 py-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold">
                                        <?= lang('App.admin_remove') ?>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="social_link_title[]" value="">
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 dark:border-slate-700 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider"><?= lang('App.admin_attachments') ?></h3>
                            <p class="text-[11px] text-slate-500 mt-1"><?= lang('App.admin_attachments_hint') ?></p>
                        </div>
                        <button type="button" id="add-attachment-input" class="shrink-0 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200 dark:border-emerald-800">
                            + <?= lang('App.admin_add_more_files') ?>
                        </button>
                    </div>
                    <div id="attachment-inputs" class="space-y-2">
                        <input type="file" name="attachments[]" multiple
                               accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                               class="w-full text-xs text-slate-500 file:me-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-800 dark:file:text-slate-200">
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 sm:p-6 space-y-4">
                <h2 class="text-sm font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400"><?= lang('App.wall_submit_section_contact') ?></h2>
                <p class="text-xs text-slate-500"><?= lang('App.wall_submit_contact_hint') ?></p>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.full_name') ?> *</label>
                    <input type="text" name="submitter_name" required value="<?= esc(old('submitter_name')) ?>"
                           class="<?= esc($field) ?>">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.phone_label') ?> *</label>
                        <input type="text" name="submitter_phone" required value="<?= esc(old('submitter_phone')) ?>" placeholder="03XXXXXXXXX"
                               class="<?= esc($field) ?> font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_email') ?></label>
                        <input type="email" name="submitter_email" value="<?= esc(old('submitter_email')) ?>"
                               class="<?= esc($field) ?>">
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-[11px] text-slate-500 text-center sm:text-start"><?= lang('App.wall_submit_review_note') ?></p>
                <button type="submit" class="btn btn-md btn-primary w-full sm:w-auto">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span><?= lang('App.wall_submit_cta') ?></span>
                </button>
            </div>
        </form>
    </div>
</section>

<script>
(() => {
  const btn = document.getElementById('add-attachment-input');
  const box = document.getElementById('attachment-inputs');
  if (btn && box) {
    btn.addEventListener('click', () => {
      const input = document.createElement('input');
      input.type = 'file';
      input.name = 'attachments[]';
      input.multiple = true;
      input.accept = '.jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document';
      input.className = 'w-full text-xs text-slate-500 file:me-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-800 dark:file:text-slate-200';
      box.appendChild(input);
    });
  }

  const removeLabel = <?= json_encode(lang('App.admin_remove')) ?>;
  const titlePlaceholder = <?= json_encode(lang('App.admin_link_title_placeholder')) ?>;
  const urlLabel = <?= json_encode(lang('App.admin_link_url_label')) ?>;
  const titleLabel = <?= json_encode(lang('App.admin_link_title_label')) ?>;
  const selectPlatformLabel = <?= json_encode(lang('App.admin_select_platform')) ?>;
  const platforms = <?= json_encode($socialPlatforms, JSON_UNESCAPED_UNICODE) ?>;
  const fieldClass = <?= json_encode($field) ?>;

  function platformOptionsHtml(selected) {
    let html = `<option value="">${String(selectPlatformLabel).replace(/</g, '&lt;')}</option>`;
    Object.keys(platforms).forEach((key) => {
      const label = platforms[key].label || key;
      const sel = selected === key ? ' selected' : '';
      html += `<option value="${key}"${sel}>${String(label).replace(/</g, '&lt;')}</option>`;
    });
    return html;
  }

  const extBox = document.getElementById('ext-link-rows');
  const addExtBtn = document.getElementById('add-ext-link-row');
  function bindExtRemove(row) {
    row.querySelector('.remove-ext-link-row')?.addEventListener('click', () => {
      const rows = extBox.querySelectorAll('.ext-link-row');
      if (rows.length <= 1) {
        row.querySelectorAll('input').forEach((el) => { el.value = ''; });
        return;
      }
      row.remove();
    });
  }
  if (extBox) {
    extBox.querySelectorAll('.ext-link-row').forEach(bindExtRemove);
  }
  if (addExtBtn && extBox) {
    addExtBtn.addEventListener('click', () => {
      const row = document.createElement('div');
      row.className = 'ext-link-row rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 p-3 space-y-2';
      row.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2">
          <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">${String(urlLabel).replace(/</g, '&lt;')}</label>
            <input type="text" name="ext_link_url[]" value="" placeholder="https://example.com/article"
                   inputmode="url" autocomplete="url" class="${fieldClass}">
          </div>
          <div class="flex sm:items-end">
            <button type="button" class="remove-ext-link-row w-full sm:w-auto px-3 py-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold">${removeLabel}</button>
          </div>
        </div>
        <div>
          <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">${String(titleLabel).replace(/</g, '&lt;')}</label>
          <input type="text" name="ext_link_title[]" value="" placeholder="${String(titlePlaceholder).replace(/"/g, '&quot;')}"
                 class="${fieldClass}">
        </div>
      `;
      extBox.appendChild(row);
      bindExtRemove(row);
    });
  }

  const socialBox = document.getElementById('social-link-rows');
  const addSocialBtn = document.getElementById('add-social-link-row');
  function bindSocialRemove(row) {
    row.querySelector('.remove-social-link-row')?.addEventListener('click', () => {
      const rows = socialBox.querySelectorAll('.social-link-row');
      if (rows.length <= 1) {
        row.querySelectorAll('input').forEach((el) => { el.value = ''; });
        const sel = row.querySelector('select');
        if (sel) sel.selectedIndex = 0;
        return;
      }
      row.remove();
    });
  }
  if (socialBox) {
    socialBox.querySelectorAll('.social-link-row').forEach(bindSocialRemove);
  }
  if (addSocialBtn && socialBox) {
    addSocialBtn.addEventListener('click', () => {
      const row = document.createElement('div');
      row.className = 'social-link-row rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 p-3 space-y-2';
      row.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-[minmax(10rem,0.8fr)_1.4fr_auto] gap-2">
          <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">${String(selectPlatformLabel).replace(/</g, '&lt;')}</label>
            <select name="social_link_platform[]" class="${fieldClass}">
              ${platformOptionsHtml('')}
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">${String(urlLabel).replace(/</g, '&lt;')}</label>
            <input type="text" name="social_link_url[]" value="" placeholder="https://..."
                   inputmode="url" autocomplete="url" class="${fieldClass}">
          </div>
          <div class="flex sm:items-end">
            <button type="button" class="remove-social-link-row w-full sm:w-auto px-3 py-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold">${removeLabel}</button>
          </div>
        </div>
        <input type="hidden" name="social_link_title[]" value="">
      `;
      socialBox.appendChild(row);
      bindSocialRemove(row);
    });
  }
})();
</script>

<?= view('components/rich_text_editor_assets') ?>

<?= $this->endSection() ?>
