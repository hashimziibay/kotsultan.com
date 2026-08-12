<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $isEdit = !empty($business); ?>
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-8 pt-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white">
                    <?= $isEdit ? (lang('App.edit_business_listing') ?: 'Edit business') : (lang('App.add_business_listing') ?: 'Add business') ?>
                </h1>
                <p class="text-xs text-slate-500 mt-1"><?= lang('App.business_submit_hint') ?></p>
            </div>
            <a href="<?= base_url('dashboard?tab=business') ?>" class="btn btn-sm btn-secondary">&larr; <?= lang('App.back') ?: 'Back' ?></a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 text-rose-700 text-sm font-semibold"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if ($isEdit && ($business['status'] ?? '') === 'pending'): ?>
            <div class="mb-4 px-4 py-3 rounded-xl bg-amber-50 text-amber-800 text-sm font-semibold border border-amber-200">
                <?= lang('App.pending_admin_approval') ?>
            </div>
        <?php endif; ?>

        <form action="<?= $isEdit ? base_url('dashboard/business/edit/' . $business['id']) : base_url('dashboard/business/create') ?>"
              method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>

            <?= view('components/public_business_fields', [
                'categories' => $categories ?? [],
                'areas'      => $areas ?? [],
                'villages'   => $villages ?? [],
                'business'   => $business,
                'user'       => $user ?? null,
            ]) ?>

            <div class="flex justify-end gap-2">
                <a href="<?= base_url('dashboard?tab=business') ?>" class="btn btn-md btn-secondary"><?= lang('App.admin_cancel') ?: 'Cancel' ?></a>
                <button type="submit" class="btn btn-md btn-primary">
                    <?= $isEdit ? (lang('App.dashboard_save_profile') ?: 'Save') : (lang('App.submit_for_review') ?: 'Submit for review') ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
