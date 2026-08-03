<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
    $isUrdu = ($lang === 'ur');
?>

<!-- Dedicated Page Hero Section -->
<section class="relative bg-gradient-to-b from-emerald-50/50 to-slate-50 dark:from-slate-900 dark:to-slate-900 py-12 md:py-20 border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-200 overflow-hidden">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 text-xs font-bold uppercase tracking-wider mb-3">
            <i data-lucide="award" class="w-3.5 h-3.5"></i> <?= lang('App.wall_badge') ?>
        </span>
        <h1 class="blur-reveal text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-4">
            <?= lang('App.wall_title') ?>
        </h1>
        <p class="blur-reveal text-base sm:text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed">
            <?= lang('App.wall_subtitle') ?>
        </p>
    </div>
</section>

<!-- Reusable Wall Component in Full Mode -->
<?= view('components/wall_section', [
    'wallEntries' => $wallEntries,
    'isUrdu'      => $isUrdu,
    'mode'        => 'full'
]) ?>

<?= $this->endSection() ?>
