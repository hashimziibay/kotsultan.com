<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
    $isUrdu = ($lang === 'ur');
?>

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-8 transition-colors duration-200" x-data="{ sidebarOpen: false, currentTab: 'profile' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar -->
            <aside class="w-full md:w-64 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 flex flex-col justify-between shadow-xs">
                <div>
                    <div class="flex items-center gap-3 pb-6 mb-6 border-b border-slate-200 dark:border-slate-700">
                        <div class="w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 dark:text-white">Shopkeeper Account</h3>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Kot Sultan Directory</span>
                        </div>
                    </div>

                    <nav class="space-y-1">
                        <button @click="currentTab = 'profile'" 
                                :class="currentTab === 'profile' ? 'btn-primary' : 'btn-ghost'" 
                                class="w-full btn btn-sm justify-start">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            <span>My Profile</span>
                        </button>
                        <button @click="currentTab = 'saved'" 
                                :class="currentTab === 'saved' ? 'btn-primary' : 'btn-ghost'" 
                                class="w-full btn btn-sm justify-start">
                            <i data-lucide="store" class="w-4 h-4"></i>
                            <span>My Business Listing</span>
                        </button>
                    </nav>
                </div>

                <div class="pt-6 mt-6 border-t border-slate-200 dark:border-slate-700">
                    <a href="<?= base_url('login') ?>" class="w-full btn btn-sm btn-ghost text-rose-600 dark:text-rose-400 justify-start">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Log Out</span>
                    </a>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-grow">
                
                <!-- PROFILE TAB -->
                <div x-show="currentTab === 'profile'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1">Account Information</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Manage your local directory contact details.</p>

                    <form action="#" method="POST" class="space-y-4 max-w-xl text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                            <input type="text" value="Muhammad Rashid" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Phone Number</label>
                            <input type="text" value="0302-8877665" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                            <input type="email" value="rashid.bismillah@example.com" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white outline-none">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="btn btn-md btn-primary">
                                Save Profile
                            </button>
                        </div>
                    </form>
                </div>

                <!-- MY BUSINESS TAB -->
                <div x-show="currentTab === 'saved'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-xs">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1">Bismillah Bakers & Sweet House</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Registered Listing in Kot Sultan Directory</p>

                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs space-y-2 max-w-xl">
                        <p><span class="font-bold">Category:</span> Restaurants & Sweets</p>
                        <p><span class="font-bold">Address:</span> Main Bazaar Market, Kot Sultan</p>
                        <p><span class="font-bold">Status:</span> <span class="text-emerald-600 font-bold">Active Listing</span></p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
