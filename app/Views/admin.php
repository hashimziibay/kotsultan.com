<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
    $isUrdu = ($lang === 'ur');
?>

<div class="bg-slate-50 dark:bg-slate-900 min-h-screen py-8 transition-colors duration-200" x-data="{ activeTab: 'businesses' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Admin Header (Theme Adaptive) -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-6 mb-8 shadow-xs">
            <div>
                <h1 class="blur-reveal text-2xl font-bold tracking-tight">Kot Sultan Directory Admin Panel</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage categories, business listings, tags, community wall, and system settings.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-700/50">
                    Directory Administrator
                </span>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2 mb-8 border-b border-slate-200 dark:border-slate-800 pb-4">
            <button @click="activeTab = 'businesses'" 
                    :class="activeTab === 'businesses' ? 'btn-primary' : 'btn-secondary'" 
                    class="btn btn-sm">
                <i data-lucide="store" class="w-4 h-4"></i>
                <span>Businesses (<?= count($businesses) ?>)</span>
            </button>

            <button @click="activeTab = 'categories'" 
                    :class="activeTab === 'categories' ? 'btn-primary' : 'btn-secondary'" 
                    class="btn btn-sm">
                <i data-lucide="folder" class="w-4 h-4"></i>
                <span>Categories (<?= count($categories) ?>)</span>
            </button>

            <button @click="activeTab = 'tags'" 
                    :class="activeTab === 'tags' ? 'btn-primary' : 'btn-secondary'" 
                    class="btn btn-sm">
                <i data-lucide="tag" class="w-4 h-4"></i>
                <span>Business Tags (<?= count($tags) ?>)</span>
            </button>

            <button @click="activeTab = 'wall'" 
                    :class="activeTab === 'wall' ? 'btn-primary' : 'btn-secondary'" 
                    class="btn btn-sm">
                <i data-lucide="award" class="w-4 h-4"></i>
                <span>Wall of Kot Sultan (<?= count($wall) ?>)</span>
            </button>

            <button @click="activeTab = 'settings'" 
                    :class="activeTab === 'settings' ? 'btn-primary' : 'btn-secondary'" 
                    class="btn btn-sm">
                <i data-lucide="settings" class="w-4 h-4"></i>
                <span>Languages & Settings</span>
            </button>
        </div>


        <!-- TAB 1: BUSINESSES -->
        <div x-show="activeTab === 'businesses'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Manage Businesses</h3>
                <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i> Add New Business
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left rtl:text-right text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-700/50 uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Business Name</th>
                            <th class="px-4 py-3">Owner</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">WhatsApp</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 font-medium">
                        <?php foreach ($businesses as $b): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                            <td class="px-4 py-3 font-bold"><?= $b['id'] ?></td>
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                <?= $b['name_en'] ?><br>
                                <span class="text-[11px] text-slate-400 font-normal"><?= $b['name_ur'] ?></span>
                            </td>
                            <td class="px-4 py-3"><?= $b['owner_name'] ?? 'N/A' ?></td>
                            <td class="px-4 py-3 font-mono"><?= $b['phone'] ?></td>
                            <td class="px-4 py-3 font-mono"><?= $b['whatsapp'] ?? '-' ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">
                                    <?= strtoupper($b['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="p-1.5 text-slate-500 hover:text-emerald-600"><i data-lucide="edit" class="w-4 h-4"></i></button>
                                    <button class="p-1.5 text-slate-500 hover:text-red-600"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- TAB 2: CATEGORIES -->
        <div x-show="activeTab === 'categories'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Manage Categories</h3>
                <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i> Add Category
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($categories as $cat): ?>
                <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center font-bold">
                            <i data-lucide="<?= !empty($cat['icon']) ? $cat['icon'] : 'folder' ?>" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm"><?= $cat['name_en'] ?></h4>
                            <span class="text-xs text-slate-500"><?= $cat['name_ur'] ?></span>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-slate-400">Order: <?= $cat['display_order'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>


        <!-- TAB 3: TAGS -->
        <div x-show="activeTab === 'tags'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Manage Tags</h3>
                <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i> Add Tag
                </button>
            </div>

            <div class="flex flex-wrap gap-2">
                <?php foreach ($tags as $t): ?>
                <div class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                    <span>#<?= $t['name_en'] ?> (<?= $t['name_ur'] ?>)</span>
                    <button class="text-slate-400 hover:text-red-500"><i data-lucide="x" class="w-3.5 h-3.5"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>


        <!-- TAB 4: WALL OF KOT SULTAN -->
        <div x-show="activeTab === 'wall'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">Manage Wall of Kot Sultan</h3>
                <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i> Add Person
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php foreach ($wall as $person): ?>
                <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center gap-3">
                    <img src="<?= $person['photo'] ?>" class="w-12 h-12 rounded-full object-cover">
                    <div class="flex-grow min-w-0">
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm truncate"><?= $person['name_en'] ?></h4>
                        <span class="text-xs text-slate-500 font-bold">Display Order: <?= $person['display_order'] ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>


        <!-- TAB 5: SETTINGS & LANGUAGES -->
        <div x-show="activeTab === 'settings'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4">Directory Settings & Languages</h3>
            
            <div class="space-y-4 max-w-xl text-xs">
                <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1">Active Languages</h4>
                    <p class="text-slate-500 dark:text-slate-400 mb-3">KotSultan.com supports English (EN) and Original Urdu (RTL).</p>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-emerald-600 text-white rounded-md font-bold">English (EN)</span>
                        <span class="px-3 py-1 bg-emerald-600 text-white rounded-md font-bold">اردو (Urdu RTL)</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
