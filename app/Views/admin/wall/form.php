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
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        <?= lang('App.admin_category_label') ?>
                    </label>
                    <?php
                        $selectedIds = $selectedCategoryIds ?? [];
                        if ($selectedIds === [] && ! empty($person['category_id'])) {
                            $selectedIds = [(int) $person['category_id']];
                        }
                        $oldIds = old('category_ids');
                        if (is_array($oldIds)) {
                            $selectedIds = array_map('intval', $oldIds);
                        }
                        $categoryOptions = [];
                        foreach ($categories ?? [] as $c) {
                            $categoryOptions[] = [
                                'id'        => (int) $c['id'],
                                'label'     => trim(($c['name_en'] ?? '') !== '' ? $c['name_en'] : ($c['name_ur'] ?? '')),
                                'label_alt' => trim(($c['name_ur'] ?? '') . ' ' . ($c['name_en'] ?? '')),
                            ];
                        }
                    ?>
                    <?= view('components/searchable_multi_select', [
                        'name'              => 'category_ids',
                        'options'           => $categoryOptions,
                        'selected'          => $selectedIds,
                        'required'          => true,
                        'placeholder'       => lang('App.admin_select_category'),
                        'searchPlaceholder' => lang('App.search_category') ?: 'Search category…',
                        'inputClass'        => 'w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium',
                    ]) ?>
                    <p class="mt-1 text-[10px] text-slate-400"><?= lang('App.admin_wall_categories_hint') ?: 'Select one or more categories. The first selected is the primary category.' ?></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.admin_years_service') ?></label>
                    <textarea name="years_of_service" rows="2" placeholder="e.g. 1980 - 2015"
                              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium"><?= esc($person['years_of_service'] ?? '') ?></textarea>
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
                    <?php if (!empty($person['photo'])): ?>
                        <?php
                            $currentPhoto = trim((string) $person['photo']);
                            $currentPhotoUrl = preg_match('#^(https?:)?//#i', $currentPhoto)
                                ? $currentPhoto
                                : base_url($currentPhoto);
                        ?>
                        <div class="mb-3 flex items-center gap-3">
                            <img src="<?= esc($currentPhotoUrl) ?>"
                                 alt="Current photo"
                                 class="w-20 h-20 rounded-xl object-contain bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"
                                 onerror="this.style.display='none'">
                            <div class="text-[11px] text-slate-500">
                                <p class="font-bold text-slate-700 dark:text-slate-300"><?= lang('App.admin_current_photo') ?? 'Current profile photo' ?></p>
                                <p><?= lang('App.admin_photo_replace_hint') ?? 'Choose a new file below to replace it.' ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-700">
                    <p class="text-[10px] text-slate-400 mt-1"><?= lang('App.admin_profile_photo_hint') ?? 'Main profile photo (single image).' ?></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1"><?= lang('App.status') ?></label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold">
                        <option value="active" <?= ($person['status'] ?? 'active') === 'active' ? 'selected' : '' ?>><?= lang('App.admin_active_status') ?></option>
                        <option value="pending" <?= ($person['status'] ?? '') === 'pending' ? 'selected' : '' ?>><?= lang('App.admin_pending_status') ?: 'Pending' ?></option>
                        <option value="inactive" <?= ($person['status'] ?? '') === 'inactive' ? 'selected' : '' ?>><?= lang('App.admin_inactive_status') ?></option>
                    </select>
                    <?php if (!empty($person['submitter_name']) || !empty($person['submitter_phone'])): ?>
                        <p class="mt-2 text-[10px] text-slate-500 leading-relaxed">
                            <?= lang('App.admin_wall_submitted_by') ?>:
                            <strong><?= esc($person['submitter_name'] ?? '') ?></strong>
                            <?php if (!empty($person['submitter_phone'])): ?> · <?= esc($person['submitter_phone']) ?><?php endif; ?>
                            <?php if (!empty($person['submitter_email'])): ?> · <?= esc($person['submitter_email']) ?><?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider"><?= lang('App.admin_external_links') ?></h4>
                        <p class="text-[11px] text-slate-500 mt-1"><?= lang('App.admin_external_links_hint') ?></p>
                    </div>
                    <button type="button" id="add-ext-link-row" class="shrink-0 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200 dark:border-emerald-800">
                        + <?= lang('App.admin_add_link') ?>
                    </button>
                </div>

                <?php
                    $socialPlatforms = \App\Models\WallModel::socialPlatforms();
                    $partitioned = \App\Models\WallModel::partitionLinks(
                        isset($person['external_links']) ? (string) $person['external_links'] : null
                    );
                    $webLinks = $partitioned['external'];
                    $socialLinks = $partitioned['social'];
                    if ($webLinks === []) {
                        $webLinks = [['title' => '', 'url' => '']];
                    }
                    if ($socialLinks === []) {
                        $socialLinks = [['platform' => '', 'title' => '', 'url' => '']];
                    }
                ?>
                <div id="ext-link-rows" class="space-y-2">
                    <?php foreach ($webLinks as $link): ?>
                    <div class="ext-link-row rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-3 space-y-2">
                        <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1"><?= lang('App.admin_link_url_label') ?></label>
                                <input type="text" name="ext_link_url[]" value="<?= esc($link['url'] ?? '') ?>"
                                       placeholder="https://example.com/article"
                                       inputmode="url" autocomplete="url"
                                       class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                            </div>
                            <div class="flex sm:items-end">
                                <button type="button" class="remove-ext-link-row w-full sm:w-auto px-3 py-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold border border-transparent hover:border-rose-200">
                                    <?= lang('App.admin_remove') ?>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1"><?= lang('App.admin_link_title_label') ?></label>
                            <input type="text" name="ext_link_title[]" value="<?= esc($link['title'] ?? '') ?>"
                                   placeholder="<?= esc(lang('App.admin_link_title_placeholder')) ?>"
                                   class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider"><?= lang('App.admin_social_links') ?></h4>
                        <p class="text-[11px] text-slate-500 mt-1"><?= lang('App.admin_social_links_hint') ?></p>
                    </div>
                    <button type="button" id="add-social-link-row" class="shrink-0 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200 dark:border-emerald-800">
                        + <?= lang('App.admin_add_social_link') ?>
                    </button>
                </div>

                <div id="social-link-rows" class="space-y-2">
                    <?php foreach ($socialLinks as $link): ?>
                    <div class="social-link-row rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-3 space-y-2">
                        <div class="grid grid-cols-1 sm:grid-cols-[minmax(10rem,0.8fr)_1.4fr_auto] gap-2">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1"><?= lang('App.admin_select_platform') ?></label>
                                <select name="social_link_platform[]"
                                        class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                                    <option value=""><?= esc(lang('App.admin_select_platform')) ?></option>
                                    <?php foreach ($socialPlatforms as $key => $meta): ?>
                                        <option value="<?= esc($key) ?>" <?= (($link['platform'] ?? '') === $key) ? 'selected' : '' ?>>
                                            <?= esc($meta['label']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1"><?= lang('App.admin_link_url_label') ?></label>
                                <input type="text" name="social_link_url[]" value="<?= esc($link['url'] ?? '') ?>"
                                       placeholder="https://..."
                                       inputmode="url" autocomplete="url"
                                       class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
                            </div>
                            <div class="flex sm:items-end">
                                <button type="button" class="remove-social-link-row w-full sm:w-auto px-3 py-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold border border-transparent hover:border-rose-200">
                                    <?= lang('App.admin_remove') ?>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="social_link_title[]" value="">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider"><?= lang('App.admin_attachments') ?></h4>
                        <p class="text-[11px] text-slate-500 mt-1"><?= lang('App.admin_attachments_hint') ?></p>
                    </div>
                    <button type="button" id="add-attachment-input" class="shrink-0 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200 dark:border-emerald-800">
                        + <?= lang('App.admin_add_more_files') ?>
                    </button>
                </div>

                <div id="attachment-inputs" class="space-y-2">
                    <input type="file" name="attachments[]" multiple
                           accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-800 dark:file:text-slate-200">
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

    <?php $attachments = $attachments ?? []; ?>
    <?php if (!empty($person['id']) && !empty($attachments)): ?>
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-3 mt-6">
        <h4 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider"><?= lang('App.admin_existing_attachments') ?></h4>
        <div class="space-y-2">
            <?php foreach ($attachments as $att): ?>
            <div class="flex items-center justify-between gap-3 px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <div class="min-w-0 flex items-center gap-2">
                    <?php if (($att['file_type'] ?? '') === 'image'): ?>
                        <img src="<?= esc($att['url']) ?>" alt="" class="w-12 h-12 rounded-lg object-cover border border-slate-200 dark:border-slate-700">
                    <?php else: ?>
                        <span class="w-12 h-12 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[10px] font-extrabold uppercase text-slate-600 dark:text-slate-300">
                            <?= esc($att['file_type'] ?? 'file') ?>
                        </span>
                    <?php endif; ?>
                    <div class="min-w-0">
                        <a href="<?= esc($att['url']) ?>" target="_blank" class="block text-xs font-bold text-emerald-700 dark:text-emerald-400 truncate hover:underline">
                            <?= esc($att['original_name'] ?? basename($att['file_path'] ?? '')) ?>
                        </a>
                        <p class="text-[10px] text-slate-400"><?= number_format(((int) ($att['file_size'] ?? 0)) / 1024, 1) ?> KB</p>
                    </div>
                </div>
                <form action="<?= base_url('admin/wall-of-kot-sultan/' . $person['id'] . '/attachment/' . $att['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('<?= esc(lang('App.admin_confirm_delete_attachment'), 'js') ?>');">
                    <?= csrf_field() ?>
                    <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40" title="<?= lang('App.admin_delete') ?>">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

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
      input.className = 'w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-800 dark:file:text-slate-200';
      box.appendChild(input);
    });
  }

  const removeLabel = <?= json_encode(lang('App.admin_remove')) ?>;
  const titlePlaceholder = <?= json_encode(lang('App.admin_link_title_placeholder')) ?>;
  const urlLabel = <?= json_encode(lang('App.admin_link_url_label')) ?>;
  const titleLabel = <?= json_encode(lang('App.admin_link_title_label')) ?>;
  const selectPlatformLabel = <?= json_encode(lang('App.admin_select_platform')) ?>;
  const platforms = <?= json_encode($socialPlatforms, JSON_UNESCAPED_UNICODE) ?>;

  function platformOptionsHtml(selected) {
    let html = `<option value="">${String(selectPlatformLabel).replace(/</g, '&lt;')}</option>`;
    Object.keys(platforms).forEach((key) => {
      const label = platforms[key].label || key;
      const sel = selected === key ? ' selected' : '';
      html += `<option value="${key}"${sel}>${String(label).replace(/</g, '&lt;')}</option>`;
    });
    return html;
  }

  function bindRowRemove(box, rowSelector, btnSelector, clearFn) {
    if (!box) return;
    box.querySelectorAll(btnSelector).forEach((btn) => {
      btn.addEventListener('click', () => {
        const rows = box.querySelectorAll(rowSelector);
        if (rows.length <= 1) {
          clearFn(rows[0]);
          return;
        }
        btn.closest(rowSelector)?.remove();
      });
    });
  }

  const extBox = document.getElementById('ext-link-rows');
  const addExtBtn = document.getElementById('add-ext-link-row');
  bindRowRemove(extBox, '.ext-link-row', '.remove-ext-link-row', (row) => {
    if (!row) return;
    row.querySelectorAll('input').forEach((el) => { el.value = ''; });
  });
  if (addExtBtn && extBox) {
    addExtBtn.addEventListener('click', () => {
      const row = document.createElement('div');
      row.className = 'ext-link-row rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-3 space-y-2';
      row.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2">
          <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">${String(urlLabel).replace(/</g, '&lt;')}</label>
            <input type="text" name="ext_link_url[]" value="" placeholder="https://example.com/article"
                   inputmode="url" autocomplete="url"
                   class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
          </div>
          <div class="flex sm:items-end">
            <button type="button" class="remove-ext-link-row w-full sm:w-auto px-3 py-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold border border-transparent hover:border-rose-200">${removeLabel}</button>
          </div>
        </div>
        <div>
          <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">${String(titleLabel).replace(/</g, '&lt;')}</label>
          <input type="text" name="ext_link_title[]" value="" placeholder="${String(titlePlaceholder).replace(/"/g, '&quot;')}"
                 class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
        </div>
      `;
      extBox.appendChild(row);
      bindRowRemove(row, '.ext-link-row', '.remove-ext-link-row', () => {});
      row.querySelector('.remove-ext-link-row')?.addEventListener('click', () => {
        const rows = extBox.querySelectorAll('.ext-link-row');
        if (rows.length <= 1) {
          row.querySelectorAll('input').forEach((el) => { el.value = ''; });
          return;
        }
        row.remove();
      });
    });
  }

  const socialBox = document.getElementById('social-link-rows');
  const addSocialBtn = document.getElementById('add-social-link-row');
  bindRowRemove(socialBox, '.social-link-row', '.remove-social-link-row', (row) => {
    if (!row) return;
    row.querySelectorAll('input').forEach((el) => { el.value = ''; });
    const sel = row.querySelector('select');
    if (sel) sel.selectedIndex = 0;
  });
  if (addSocialBtn && socialBox) {
    addSocialBtn.addEventListener('click', () => {
      const row = document.createElement('div');
      row.className = 'social-link-row rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-3 space-y-2';
      row.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-[minmax(10rem,0.8fr)_1.4fr_auto] gap-2">
          <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">${String(selectPlatformLabel).replace(/</g, '&lt;')}</label>
            <select name="social_link_platform[]"
                    class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
              ${platformOptionsHtml('')}
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">${String(urlLabel).replace(/</g, '&lt;')}</label>
            <input type="text" name="social_link_url[]" value="" placeholder="https://..."
                   inputmode="url" autocomplete="url"
                   class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium">
          </div>
          <div class="flex sm:items-end">
            <button type="button" class="remove-social-link-row w-full sm:w-auto px-3 py-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold border border-transparent hover:border-rose-200">${removeLabel}</button>
          </div>
        </div>
        <input type="hidden" name="social_link_title[]" value="">
      `;
      socialBox.appendChild(row);
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
    });
  }
})();
</script>

<?= $this->endSection() ?>
