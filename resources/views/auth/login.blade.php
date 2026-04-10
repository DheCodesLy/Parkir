<x-guest-layout>
    <div class="fixed inset-0 dot-pattern z-0"></div>
    <div class="fixed -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl z-0"></div>
    <div class="fixed -bottom-24 -right-24 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl z-0"></div>

    <div class="fixed top-8 right-8 z-50">
        <button type="button" onclick="toggleTheme()" class="p-3 rounded-2xl glass shadow-lg text-slate-600 dark:text-slate-300 hover:scale-110 transition-all active:scale-95">
            <svg id="sun-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 18v1m9-9h1m-18 0h1m11.364-7.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            <svg id="moon-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        </button>
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center p-6">
        <div class="flex flex-col md:flex-row w-full max-w-4xl glass rounded-[40px] shadow-2xl overflow-hidden border border-white/20 dark:border-slate-800">

            <div class="hidden md:flex w-2/5 bg-blue-600 dark:bg-blue-900 p-12 flex-col justify-between relative overflow-hidden">
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-white/20 rounded-2xl backdrop-blur-md flex items-center justify-center mb-8">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                    </div>
                    <h2 class="text-3xl font-bold text-white leading-tight">Keamanan & Presisi Parkir.</h2>
                    <p class="text-blue-100/70 mt-4 text-sm font-light">Masuk untuk memantau aktivitas gate dan laporan pendapatan hari ini secara real-time.</p>
                </div>

                <div class="relative z-10 grid grid-cols-2 gap-3 opacity-40">
                    <div class="h-16 border-2 border-dashed border-white/30 rounded-xl flex items-center justify-center text-white/40 text-[10px] font-bold">SLOT A1</div>
                    <div class="h-16 border-2 border-white/30 bg-white/10 rounded-xl flex items-center justify-center">
                        <div class="w-8 h-4 bg-blue-400 rounded-sm animate-pulse"></div>
                    </div>
                    <div class="h-16 border-2 border-white/30 bg-white/10 rounded-xl flex items-center justify-center">
                        <div class="w-8 h-4 bg-white/80 rounded-sm"></div>
                    </div>
                    <div class="h-16 border-2 border-dashed border-white/30 rounded-xl flex items-center justify-center text-white/40 text-[10px] font-bold">SLOT B2</div>
                </div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/5 rounded-full"></div>
            </div>

            <div class="flex-1 p-8 md:p-16 bg-white/50 dark:bg-transparent">
                <div class="max-w-sm mx-auto">
                    <header class="mb-10 text-center md:text-left">
                        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Login Pengurus</h1>
                        <p class="text-slate-500 dark:text-slate-400 mt-2">Selamat bekerja kembali di ParkirPro.</p>
                    </header>

                    <x-auth-session-status class="mb-6" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div class="group">
                            <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-blue-500 transition-colors">
                                {{ __('Email') }}
                            </label>
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                                placeholder="nama@parkirpro.com"
                                class="w-full px-5 py-4 glass bg-slate-50/50 dark:bg-slate-800/50 rounded-2xl outline-none border border-transparent focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all dark:text-white text-sm">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="group">
                            <div class="flex justify-between mb-2">
                                <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1 group-focus-within:text-blue-500 transition-colors">
                                    {{ __('Kata Sandi') }}
                                </label>
                                @if (Route::has('password.request'))
                                    <a class="text-xs font-bold text-blue-600 hover:text-blue-500 transition-colors" href="{{ route('password.request') }}">
                                        {{ __('Lupa?') }}
                                    </a>
                                @endif
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full px-5 py-4 glass bg-slate-50/50 dark:bg-slate-800/50 rounded-2xl outline-none border border-transparent focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all dark:text-white text-sm">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center">
                            <label for="remember_me" class="relative inline-flex items-center cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Ingat saya') }}</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-2xl shadow-xl shadow-blue-500/30 hover:shadow-blue-500/50 transition-all active:scale-[0.98]">
                            {{ __('Login') }}
                        </button>
                    </form>

                    <footer class="mt-12 text-center">
                        <p class="text-[10px] text-slate-400 dark:text-slate-600 font-bold uppercase tracking-[0.2em]">
                            Sistem Manajemen Parkir
                        </p>
                    </footer>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dot-pattern {
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
        }
        .dark .dot-pattern {
            background-image: radial-gradient(#1e293b 1px, transparent 1px);
        }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.6);
        }
    </style>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const moonIcon = document.getElementById('moon-icon');
            const sunIcon = document.getElementById('sun-icon');

            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                moonIcon.classList.remove('hidden');
                sunIcon.classList.add('hidden');
                localStorage.theme = 'light';
            } else {
                html.classList.add('dark');
                moonIcon.classList.add('hidden');
                sunIcon.classList.remove('hidden');
                localStorage.theme = 'dark';
            }
        }

        // Apply theme on load
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            document.getElementById('moon-icon')?.classList.add('hidden');
            document.getElementById('sun-icon')?.classList.remove('hidden');
        }
    </script>
</x-guest-layout>
