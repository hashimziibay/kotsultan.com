<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
    $isUrdu = ($lang === 'ur');
?>

<div class="bg-slate-50 dark:bg-slate-900 py-10 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Navigation -->
        <a href="<?= base_url('listings') ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-600 dark:text-slate-400 mb-6 transition-colors">
            &larr; <?= $isUrdu ? 'واپس ڈائرکٹری' : 'Back to Directory' ?>
        </a>

        <!-- Main Card -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden mb-8">
            
            <!-- Hero Photo -->
            <div class="relative h-64 sm:h-80 bg-slate-100 dark:bg-slate-700 overflow-hidden">
                <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80&w=1200" 
                     alt="Al-Shafi Hospital & Clinic" 
                     class="w-full h-full object-cover">
                <div class="absolute top-4 left-4 rtl:left-auto rtl:right-4">
                    <span class="px-3 py-1 rounded-md bg-slate-900/80 text-white text-xs font-bold uppercase tracking-wider backdrop-blur-xs">
                        <?= $isUrdu ? 'کلینک اور ڈاکٹرز' : 'Clinics & Doctors' ?>
                    </span>
                </div>
            </div>

            <!-- Content Header -->
            <div class="p-6 sm:p-8 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-700/80 pb-6">
                    <div>
                        <h1 class="blur-reveal text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">
                            <?= $isUrdu ? 'الشافی ہسپتال و کلینک' : 'Al-Shafi Hospital & Clinic' ?>
                        </h1>
                        
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4 text-emerald-600"></i>
                            <span><?= lang('App.owner') ?>: Dr. Muhammad Aslam</span>
                        </p>
                    </div>

                    <!-- Direct Action Buttons -->
                    <div class="flex items-center gap-3">
                        <a href="tel:03056660169" class="btn btn-md btn-secondary">
                            <i data-lucide="phone" class="w-4 h-4 text-emerald-600"></i>
                            <span><?= lang('App.call_now') ?></span>
                        </a>

                        <a href="https://wa.me/923056660169" target="_blank" rel="noopener" class="btn btn-md btn-success">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span><?= lang('App.whatsapp') ?></span>
                        </a>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    
                    <div class="space-y-4">
                        <h3 class="font-bold text-slate-900 dark:text-white text-base">Location & Contact</h3>
                        
                        <div class="flex items-start gap-3 text-slate-700 dark:text-slate-300">
                            <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600 mt-1 flex-shrink-0"></i>
                            <div>
                                <span class="font-bold block">Address:</span>
                                <span>Main Multan Road, Near General Bus Stand, Kot Sultan, Pakistan</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                            <i data-lucide="phone" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                            <div>
                                <span class="font-bold">Phone:</span>
                                <span dir="ltr" class="font-mono ml-1">0305-6660169</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                            <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                            <div>
                                <span class="font-bold">WhatsApp:</span>
                                <span dir="ltr" class="font-mono ml-1">+92 305 6660169</span>
                            </div>
                        </div>
                    </div>

                    <!-- Map / Location Link -->
                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-700 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-2">Google Map Directions</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                                Click below to open Google Maps directions to reach this location in Kot Sultan.
                            </p>
                        </div>
                        <a href="https://maps.google.com/?q=Kot+Sultan" target="_blank" rel="noopener" class="w-full py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-100 font-bold text-xs rounded-lg flex items-center justify-center gap-2 transition-colors">
                            <i data-lucide="map" class="w-4 h-4"></i>
                            <span>Open in Google Maps</span>
                        </a>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
