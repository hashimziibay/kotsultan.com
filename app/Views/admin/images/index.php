<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<!-- Action Header & Upload -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-8 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= lang('App.admin_images_title') ?></h2>
        <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_images_sub') ?> (<?= lang('App.admin_total') ?>: <?= count($images) ?>)</p>
    </div>

    <!-- Upload Modal Trigger / Form -->
    <form action="<?= base_url('admin/images/upload') ?>" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
        <?= csrf_field() ?>
        <select name="module" class="px-3 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold">
            <option value="businesses"><?= lang('App.admin_businesses') ?></option>
            <option value="wall"><?= lang('App.admin_wall') ?></option>
            <option value="emergency"><?= lang('App.admin_emergency') ?></option>
        </select>
        <input type="file" name="image_file" required accept="image/*" class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-700">
        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
            <i data-lucide="upload-cloud" class="w-4 h-4"></i>
            <span><?= lang('App.admin_upload') ?></span>
        </button>
    </form>
</div>

<!-- Image Grid -->
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
    <?php foreach ($images as $img): ?>
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs flex flex-col justify-between group">
        
        <!-- Thumbnail -->
        <div class="relative h-36 bg-slate-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center p-2">
            <img src="<?= base_url($img['path']) ?>" alt="Thumbnail" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-200">
            
            <!-- Reference Badge -->
            <div class="absolute top-2 right-2">
                <?php if ($img['references'] > 0): ?>
                <span class="px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-extrabold shadow-sm">
                    <?= lang('App.admin_used') ?> (<?= $img['references'] ?>)
                </span>
                <?php else: ?>
                <span class="px-2 py-0.5 rounded-full bg-slate-600 text-slate-200 text-[10px] font-bold shadow-sm">
                    <?= lang('App.admin_unused') ?>
                </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Meta Info & Delete -->
        <div class="p-3 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 space-y-1">
            <div class="font-bold text-xs text-slate-800 dark:text-slate-200 truncate" title="<?= esc($img['filename']) ?>">
                <?= esc($img['filename']) ?>
            </div>
            <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono">
                <span><?= round($img['size'] / 1024, 1) ?> KB</span>
                <span><?= date('M d, Y', $img['mtime']) ?></span>
            </div>

            <!-- Delete Form -->
            <div class="pt-2 flex justify-end">
                <?php if ($img['references'] > 0): ?>
                <button disabled title="<?= lang('App.admin_protected') ?>" class="px-2 py-1 bg-slate-200 dark:bg-slate-800 text-slate-400 text-[10px] font-bold rounded-lg cursor-not-allowed">
                    <?= lang('App.admin_protected') ?>
                </button>
                <?php else: ?>
                <form action="<?= base_url('admin/images/delete') ?>" method="POST" onsubmit="return confirm('<?= lang('App.admin_confirm_delete_image') ?>');" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="path" value="<?= esc($img['path']) ?>">
                    <button type="submit" class="px-2 py-1 bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 hover:bg-rose-200 text-[10px] font-bold rounded-lg transition-colors">
                        <?= lang('App.admin_delete') ?>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>
