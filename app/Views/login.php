<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="min-h-screen w-full flex bg-slate-50 dark:bg-slate-900 pt-16 transition-colors duration-200">
    <!-- Left Side: Branding (Hidden on mobile) -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-emerald-900 dark:bg-slate-950 overflow-hidden items-center justify-center p-12 transition-colors duration-200">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[70%] h-[70%] rounded-full bg-emerald-500/20 blur-[120px]"></div>
            <div class="absolute bottom-[0%] right-[0%] w-[60%] h-[60%] rounded-full bg-emerald-400/20 blur-[100px]"></div>
        </div>

        <!-- Branding Card -->
        <div class="relative z-10 bg-white/10 dark:bg-slate-800/40 backdrop-blur-md border border-white/20 dark:border-slate-700/50 p-12 rounded-2xl shadow-xl max-w-xl text-center">
            <div class="w-16 h-16 mx-auto rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-lg mb-6">
                <i data-lucide="map-pin" class="w-8 h-8"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight mb-4">
                Kot Sultan<span class="text-emerald-400">.com</span>
            </h1>
            <p class="text-sm text-slate-200 dark:text-slate-300 leading-relaxed">
                Official local community directory for Kot Sultan, Pakistan. Sign in to manage directory listings and community information.
            </p>
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 xl:p-24 relative overflow-hidden bg-slate-50 dark:bg-slate-900">
        
        <div class="w-full max-w-md relative z-10">
            
            <div class="mb-8">
                <h2 class="blur-reveal text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-1">Directory Login</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Enter your credentials to access your account</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white dark:bg-slate-800 rounded-xl p-8 shadow-xs border border-slate-200 dark:border-slate-700">
                <form action="#" method="POST" class="space-y-5">
                    
                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-bold text-slate-900 dark:text-white">Email Address</label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-3.5 rtl:right-3.5 rtl:left-auto top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="email" id="email" placeholder="hello@example.com" class="w-full pl-10 rtl:pr-10 rtl:pl-3 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white outline-none focus:border-emerald-600">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-bold text-slate-900 dark:text-white">Password</label>
                            <a href="#" class="text-xs font-bold text-emerald-600 hover:underline">Forgot password?</a>
                        </div>
                        <div class="relative" x-data="{ show: false }">
                            <i data-lucide="lock" class="absolute left-3.5 rtl:right-3.5 rtl:left-auto top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input :type="show ? 'text' : 'password'" id="password" placeholder="••••••••" class="w-full pl-10 rtl:pr-10 rtl:pl-3 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white outline-none focus:border-emerald-600">
                            <button type="button" @click="show = !show" class="absolute right-3.5 rtl:left-3.5 rtl:right-auto top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                <i data-lucide="eye" x-show="!show" class="w-4 h-4"></i>
                                <i data-lucide="eye-off" x-show="show" x-cloak class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full btn btn-lg btn-primary">
                        <span>Sign In</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

            <p class="text-center text-xs font-medium text-slate-500 dark:text-slate-400 mt-6">
                Need help logging in? Contact directory administration at <span class="font-bold text-slate-700 dark:text-slate-200">info@kotsultan.com</span>
            </p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
