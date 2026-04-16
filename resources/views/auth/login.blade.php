<x-guest-layout>
    <div class="fixed inset-0 bg-slate-50 dark:bg-slate-950 z-0 transition-colors duration-500"></div>
    <div class="fixed inset-0 dot-pattern opacity-50 z-0"></div>

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-80 h-80 bg-emerald-500/15 rounded-full blur-3xl"></div>
    </div>

    <div class="fixed top-6 right-6 sm:top-8 sm:right-8 z-50">
        <button type="button" onclick="toggleTheme()" class="p-3 rounded-2xl bg-white/90 dark:bg-slate-800/90 backdrop-blur-md border border-slate-200 dark:border-slate-700 shadow-lg text-slate-600 dark:text-slate-300 hover:scale-110 hover:text-blue-500 transition-all duration-300 active:scale-95">
            <svg id="sun-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 18v1m9-9h1m-18 0h1m11.364-7.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            <svg id="moon-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        </button>
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col md:flex-row w-full max-w-5xl bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-200/50 dark:border-slate-700/50 transition-colors duration-500">

            <div class="w-full md:w-5/12 relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 flex flex-col justify-between min-h-[320px] md:min-h-full">

                <div class="absolute top-0 right-0 w-full h-full opacity-10 pointer-events-none transition-transform duration-1000 hover:scale-105">
                    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="absolute -right-20 -top-20 w-[150%] h-[150%]">
                        <path fill="#fef08a" d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,81.1,-46.3C90.4,-33.5,96.1,-18.1,96.4,-2.8C96.7,12.5,91.7,27.7,82.7,40.6C73.7,53.5,60.8,64.1,46.5,72.2C32.2,80.3,16.1,85.9,0.5,85.1C-15.1,84.3,-30.2,77.2,-44.2,68.8C-58.2,60.4,-71.1,50.7,-79.8,37.8C-88.5,24.9,-93.1,8.8,-91.1,-6.6C-89.1,-22,-80.6,-36.7,-70.2,-48.8C-59.8,-60.9,-47.5,-70.4,-33.9,-77.8C-20.3,-85.2,-5.4,-90.5,8.2,-87.3C21.8,-84.1,30.6,-83.6,44.7,-76.4Z" transform="translate(100 100)" />
                    </svg>
                </div>

                <div class="relative z-10 p-8 md:p-12">
                    <img src="{{ asset('logo/velova.svg') }}" alt="Velova Logo" class="h-10 w-auto mb-10 drop-shadow-lg">

                    <div id="text-slider" class="min-h-[120px] transition-all duration-500 ease-in-out transform opacity-100 translate-y-0">
                        <h2 id="slider-title" class="text-3xl md:text-4xl font-bold text-white leading-tight">Presisi &<br><span class="text-amber-300">Keamanan</span> Parkir.</h2>
                        <p id="slider-desc" class="text-blue-100/80 mt-4 text-sm font-light leading-relaxed max-w-xs">
                            Masuk untuk memantau aktivitas gate dan laporan pendapatan secara real-time.
                        </p>
                    </div>
                </div>

                <div class="relative z-10 p-8 md:p-12 mt-auto">
                    <div class="grid grid-cols-2 gap-3 bg-black/20 p-4 rounded-2xl backdrop-blur-sm border border-white/10 shadow-inner group cursor-default">
                        <div class="h-14 border border-dashed border-blue-300/30 rounded-xl flex items-center justify-center text-blue-100/50 text-[10px] font-bold tracking-widest transition-colors group-hover:border-blue-300/50">SLOT A1</div>
                        <div class="h-14 border border-emerald-400/40 bg-emerald-500/20 rounded-xl flex items-center justify-center relative overflow-hidden group-hover:bg-emerald-500/30 transition-colors">
                            <div class="absolute inset-0 bg-emerald-400/10"></div>
                            <div class="w-8 h-4 bg-amber-400 rounded-sm shadow-[0_0_12px_rgba(251,191,36,0.6)] animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-7/12 p-8 sm:p-12 lg:p-16 relative bg-white dark:bg-slate-900 transition-colors duration-500">
                <div class="max-w-md mx-auto">
                    <header class="mb-10">
                        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Selamat Datang</h1>
                        <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Silakan masuk menggunakan kredensial pengurus Anda.</p>
                    </header>

                    <x-auth-session-status class="mb-6" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div class="group relative transition-all duration-300 hover:-translate-y-1">
                            <label for="email" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                                {{ __('Email') }}
                            </label>
                            <div class="relative shadow-sm group-hover:shadow-md transition-shadow rounded-xl">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                                </div>
                                <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                                    placeholder="nama@parkirpro.com"
                                    class="w-full pl-11 pr-5 py-3.5 bg-slate-50 dark:bg-slate-800/80 rounded-xl outline-none border border-slate-200 dark:border-slate-700 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all dark:text-white text-sm">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="group relative transition-all duration-300 hover:-translate-y-1">
                            <div class="flex justify-between mb-2">
                                <label for="password" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest ml-1 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                                    {{ __('Kata Sandi') }}
                                </label>
                                @if (Route::has('password.request'))
                                    <a class="text-xs font-bold text-blue-600 hover:text-amber-500 dark:text-blue-400 dark:hover:text-amber-400 transition-colors" href="{{ route('password.request') }}">
                                        {{ __('Lupa?') }}
                                    </a>
                                @endif
                            </div>
                            <div class="relative shadow-sm group-hover:shadow-md transition-shadow rounded-xl">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                </div>
                                <input id="password" type="password" name="password" required autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="w-full pl-11 pr-5 py-3.5 bg-slate-50 dark:bg-slate-800/80 rounded-xl outline-none border border-slate-200 dark:border-slate-700 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all dark:text-white text-sm">
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center pt-2">
                            <label for="remember_me" class="relative inline-flex items-center cursor-pointer group">
                                <input id="remember_me" type="checkbox" name="remember" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 group-hover:shadow-sm"></div>
                                <span class="ml-3 text-sm font-medium text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-200 transition-colors">{{ __('Ingat sesi saya') }}</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-4 mt-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-1 transition-all duration-300 active:scale-[0.98] flex justify-center items-center gap-2 group">
                            <span>{{ __('Masuk ke Sistem') }}</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform group-hover:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </form>

                    <footer class="mt-12 text-center border-t border-slate-200 dark:border-slate-800 pt-6">
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-[0.2em]">
                            &copy; {{ date('Y') }} Sistem Manajemen Parkir
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
    </style>

    <script>
        // Tema Light/Dark Mulus
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

        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            document.getElementById('moon-icon')?.classList.add('hidden');
            document.getElementById('sun-icon')?.classList.remove('hidden');
        }

        // Script Animasi Slider Teks Panel Kiri
        document.addEventListener('DOMContentLoaded', () => {
            const texts = [
                {
                    title: 'Presisi &<br><span class="text-amber-300">Keamanan</span> Parkir.',
                    desc: 'Masuk untuk memantau aktivitas gate dan laporan pendapatan secara real-time.'
                },
                {
                    title: 'Manajemen<br><span class="text-emerald-300">Terintegrasi</span>.',
                    desc: 'Kelola data pelanggan dan slot kendaraan dengan mudah dalam satu pintu.'
                },
                {
                    title: 'Laporan<br><span class="text-amber-300">Akurat</span> & Cepat.',
                    desc: 'Pantau transaksi harian dengan analitik cerdas yang selalu terbarui.'
                }
            ];

            let currentIndex = 0;
            const titleEl = document.getElementById('slider-title');
            const descEl = document.getElementById('slider-desc');
            const sliderContainer = document.getElementById('text-slider');

            setInterval(() => {
                // Fade out & geser turun sedikit
                sliderContainer.classList.remove('opacity-100', 'translate-y-0');
                sliderContainer.classList.add('opacity-0', 'translate-y-4');

                setTimeout(() => {
                    // Update konten
                    currentIndex = (currentIndex + 1) % texts.length;
                    titleEl.innerHTML = texts[currentIndex].title;
                    descEl.innerHTML = texts[currentIndex].desc;

                    // Kembalikan ke atas perlahan (Fade in)
                    sliderContainer.classList.remove('translate-y-4');
                    sliderContainer.classList.add('-translate-y-2');

                    setTimeout(() => {
                        sliderContainer.classList.remove('opacity-0', '-translate-y-2');
                        sliderContainer.classList.add('opacity-100', 'translate-y-0');
                    }, 50);

                }, 500);
            }, 5000); // Ganti tiap 5 detik
        });
    </script>
</x-guest-layout>
