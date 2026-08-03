<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="min-h-screen w-full flex items-center justify-center bg-background p-4 relative overflow-hidden pt-24" x-data="{ role: 'user' }">
    
    <!-- Ambient Background -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-primary/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-accent/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-xl relative z-10">
        <!-- Logo -->
        <div class="flex justify-center items-center gap-2 mb-8">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white shadow-lg shadow-primary/30">
                <i data-lucide="map-pin" class="w-6 h-6"></i>
            </div>
            <span class="font-black text-3xl tracking-tight text-textMain">Kot Sultan<span class="text-primary">.com</span></span>
        </div>

        <!-- Glass Card -->
        <div class="bg-card dark:bg-[#111827]/80 backdrop-blur-xl rounded-[2.5rem] p-8 sm:p-12 shadow-2xl border border-borderBase relative overflow-hidden">
            
            <div class="text-center mb-10">
                <h1 class="text-3xl font-black text-textMain tracking-tight mb-2">Create an account</h1>
                <p class="text-textMuted font-medium">Join the community and start exploring.</p>
            </div>

            <!-- Role Selector -->
            <div class="flex p-1.5 bg-black/5 dark:bg-white/5 rounded-2xl mb-8 relative">
                <button @click="role = 'user'" 
                        :class="role === 'user' ? 'bg-card text-textMain shadow-sm border border-borderBase' : 'text-textMuted hover:text-textMain'" 
                        class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                    <i data-lucide="user" class="w-4 h-4"></i> Normal User
                </button>
                <button @click="role = 'business'" 
                        :class="role === 'business' ? 'bg-card text-textMain shadow-sm border border-borderBase' : 'text-textMuted hover:text-textMain'" 
                        class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                    <i data-lucide="store" class="w-4 h-4"></i> Business Owner
                </button>
            </div>

            <form action="#" method="POST" class="space-y-5">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- First Name -->
                    <div class="space-y-1.5">
                        <label class="block text-sm font-bold text-textMain">First Name</label>
                        <input type="text" placeholder="John" class="w-full px-4 py-3.5 bg-background border border-borderBase rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-textMain font-medium">
                    </div>
                    <!-- Last Name -->
                    <div class="space-y-1.5">
                        <label class="block text-sm font-bold text-textMain">Last Name</label>
                        <input type="text" placeholder="Doe" class="w-full px-4 py-3.5 bg-background border border-borderBase rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-textMain font-medium">
                    </div>
                </div>

                <!-- Business Name (Only shows if role === 'business') -->
                <div x-show="role === 'business'" x-collapse>
                    <div class="space-y-1.5 pb-2">
                        <label class="block text-sm font-bold text-textMain">Business Name</label>
                        <div class="relative">
                            <i data-lucide="building-2" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-textMuted"></i>
                            <input type="text" placeholder="The Rustic Spoon" class="w-full pl-11 pr-4 py-3.5 bg-background border border-borderBase rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-textMain font-medium">
                        </div>
                    </div>
                </div>

                <!-- Email Address -->
                <div class="space-y-1.5">
                    <label class="block text-sm font-bold text-textMain">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-textMuted"></i>
                        <input type="email" placeholder="hello@example.com" class="w-full pl-11 pr-4 py-3.5 bg-background border border-borderBase rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-textMain font-medium">
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <label class="block text-sm font-bold text-textMain">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-textMuted"></i>
                        <input :type="show ? 'text' : 'password'" placeholder="Create a strong password" class="w-full pl-11 pr-12 py-3.5 bg-background border border-borderBase rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-textMain font-medium">
                        <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-textMuted hover:text-textMain transition-colors">
                            <i data-lucide="eye" x-show="!show" class="w-5 h-5"></i>
                            <i data-lucide="eye-off" x-show="show" x-cloak class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <p class="text-xs text-textMuted pt-2">
                    By creating an account, you agree to our <a href="#" class="text-primary hover:underline">Terms of Service</a> and <a href="#" class="text-primary hover:underline">Privacy Policy</a>.
                </p>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 mt-4 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/30 flex items-center justify-center gap-2">
                    Create Account <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </button>

            </form>

            <div class="mt-8 text-center text-sm font-medium text-textMuted">
                Already have an account? <a href="<?= base_url('login') ?>" class="text-primary font-bold hover:underline">Sign in instead</a>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
