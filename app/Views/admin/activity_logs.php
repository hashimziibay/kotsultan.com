<?= $this->extend('admin/layouts/admin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-8 shadow-xs flex justify-between items-center">
    <div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white"><?= lang('App.admin_activity_logs_title') ?></h2>
        <p class="text-xs text-slate-500 mt-1"><?= lang('App.admin_activity_logs_sub') ?> (<?= lang('App.admin_total') ?>: <?= number_format($total) ?>)</p>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left rtl:text-right">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-bold text-[10px] border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="px-4 py-3.5"><?= lang('App.admin_id') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_action') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_module') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_record_id') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_details') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_admin') ?></th>
                    <th class="px-4 py-3.5"><?= lang('App.admin_ip_address') ?></th>
                    <th class="px-4 py-3.5 text-right rtl:text-left"><?= lang('App.admin_timestamp') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                    <td class="px-4 py-3 font-mono text-slate-400">#<?= $log['id'] ?></td>
                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white"><?= esc($log['action']) ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-bold text-[10px] text-slate-700 dark:text-slate-300">
                            <?= esc($log['module']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 font-mono text-slate-500"><?= esc($log['record_id'] ?: 'N/A') ?></td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300 line-clamp-1 max-w-xs" title="<?= esc($log['details']) ?>"><?= esc($log['details']) ?></td>
                    <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-200"><?= esc($log['admin_username']) ?></td>
                    <td class="px-4 py-3 font-mono text-slate-500"><?= esc($log['ip_address']) ?></td>
                    <td class="px-4 py-3 text-right rtl:text-left font-mono text-slate-400"><?= esc($log['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
        <div class="text-slate-500 font-semibold">
            <?= lang('App.admin_showing_page') ?> <strong><?= $page ?></strong> <?= lang('App.admin_of') ?> <strong><?= $totalPages ?></strong> (<?= lang('App.admin_total') ?> <?= number_format($total) ?> <?= lang('App.admin_records_plural') ?>)
        </div>
        <div class="flex gap-2">
            <?php if ($page > 1): ?>
            <a href="<?= base_url('admin/activity-logs?page=' . ($page - 1)) ?>" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold hover:bg-slate-100">
                &larr; <?= lang('App.admin_previous') ?>
            </a>
            <?php endif; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="<?= base_url('admin/activity-logs?page=' . ($page + 1)) ?>" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold hover:bg-slate-100">
                <?= lang('App.admin_next') ?> &rarr;
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
