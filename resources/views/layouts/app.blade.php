<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="layoutState()"
      x-init="init()"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartPark - Phoenix Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        primary: {
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .transition-sidebar { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-text { white-space: nowrap; overflow: hidden; }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100 overflow-x-hidden">
    <div class="min-h-screen">
        <div
            x-show="sidebarMobileOpen"
            x-cloak
            x-transition.opacity
            @click="closeMobileSidebar()"
            class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-[1px] lg:hidden"
        ></div>

        <aside
            :class="[
                isDesktop       
                    ? (sidebarDesktopOpen ? 'lg:w-64' : 'lg:w-20')
                    : 'w-72',
                sidebarMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
            class="fixed inset-y-0 left-0 z-50 flex flex-col border-r border-slate-200 bg-white transition-sidebar dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="flex h-16 items-center justify-between border-b border-slate-100 px-4 dark:border-slate-800">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-primary-600 font-bold text-white shadow-lg shadow-primary-500/30">
                        P
                    </div>
                    <span
                        x-show="isDesktop ? sidebarDesktopOpen : true"
                        x-transition.opacity.duration.300ms
                        class="sidebar-text text-xl font-bold tracking-tight text-slate-800 dark:text-white"
                    >
                        Phoenix
                    </span>
                </div>

                <button
                    @click="closeMobileSidebar()"
                    class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 lg:hidden"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 space-y-2 overflow-y-auto px-3 py-4">
                <a href="#"
                   class="flex items-center gap-4 rounded-xl bg-primary-50 px-3 py-2.5 font-semibold text-primary-600 dark:bg-primary-900/20 dark:text-primary-400">
                    <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span x-show="isDesktop ? sidebarDesktopOpen : true" x-transition.opacity.duration.300ms class="sidebar-text">Dashboard</span>
                </a>

                <div x-data="{ open: false }">
                    <button
                        @click="isDesktop && !sidebarDesktopOpen ? sidebarDesktopOpen = true : open = !open"
                        class="flex w-full items-center gap-4 rounded-xl px-3 py-2.5 text-slate-600 transition-colors hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800"
                    >
                        <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span x-show="isDesktop ? sidebarDesktopOpen : true" x-transition.opacity.duration.300ms class="sidebar-text flex-1 text-left">User</span>
                        <svg x-show="isDesktop ? sidebarDesktopOpen : true" :class="open ? 'rotate-180' : ''" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open && (isDesktop ? sidebarDesktopOpen : true)" x-collapse>
                        <div class="space-y-1 py-2 pl-12 pr-3">
                            <a href="#" class="block py-2 text-sm text-slate-500 hover:text-primary-600 dark:text-slate-400">Kelola Petugas</a>
                            <a href="#" class="block py-2 text-sm text-slate-500 hover:text-primary-600 dark:text-slate-400">Role</a>
                        </div>
                    </div>
                </div>

                <div x-data="{ open: false }">
                    <button
                        @click="isDesktop && !sidebarDesktopOpen ? sidebarDesktopOpen = true : open = !open"
                        class="flex w-full items-center gap-4 rounded-xl px-3 py-2.5 text-slate-600 transition-colors hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800"
                    >
                        <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span x-show="isDesktop ? sidebarDesktopOpen : true" x-transition.opacity.duration.300ms class="sidebar-text flex-1 text-left">Kelola Lahan</span>
                        <svg x-show="isDesktop ? sidebarDesktopOpen : true" :class="open ? 'rotate-180' : ''" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open && (isDesktop ? sidebarDesktopOpen : true)" x-collapse>
                        <div class="space-y-1 py-2 pl-12 pr-3">
                            <a href="#" class="block py-2 text-sm text-slate-500 hover:text-primary-600 dark:text-slate-400">Daftar Lahan</a>
                            <a href="#" class="block py-2 text-sm text-slate-500 hover:text-primary-600 dark:text-slate-400">Jenis Pemilik</a>
                            <a href="#" class="block py-2 text-sm text-slate-500 hover:text-primary-600 dark:text-slate-400">Jenis Kendaraan</a>
                        </div>
                    </div>
                </div>

                <a href="#" class="flex items-center gap-4 rounded-xl px-3 py-2.5 text-slate-600 transition-colors hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800">
                    <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span x-show="isDesktop ? sidebarDesktopOpen : true" x-transition.opacity.duration.300ms class="sidebar-text">Transaksi Parkir</span>
                </a>

                <a href="#" class="flex items-center gap-4 rounded-xl px-3 py-2.5 text-slate-600 transition-colors hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800">
                    <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-show="isDesktop ? sidebarDesktopOpen : true" x-transition.opacity.duration.300ms class="sidebar-text">Keuangan</span>
                </a>
            </div>

            <div class="hidden border-t border-slate-100 p-4 dark:border-slate-800 lg:block">
                <button
                    @click="sidebarDesktopOpen = !sidebarDesktopOpen"
                    class="flex w-full items-center justify-center rounded-xl bg-slate-50 p-2.5 text-slate-500 transition-colors hover:text-primary-600 dark:bg-slate-800"
                >
                    <svg class="h-6 w-6 transition-transform duration-300" :class="!sidebarDesktopOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>
        </aside>

        <div
            :class="isDesktop ? (sidebarDesktopOpen ? 'lg:pl-64' : 'lg:pl-20') : 'pl-0'"
            class="flex min-h-screen flex-col transition-sidebar"
        >
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/80 px-4 backdrop-blur-md transition-colors dark:border-slate-800 dark:bg-slate-900/80 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button
                        @click="toggleSidebar()"
                        class="rounded-xl p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <h2 class="text-[10px] font-bold uppercase tracking-widest text-slate-800 dark:text-white sm:text-xs">
                        Parkir Management
                    </h2>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    <button
                        @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')"
                        class="flex h-10 w-10 items-center justify-center rounded-full text-slate-500 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800"
                    >
                        <svg x-show="darkMode" class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486a1 1 0 11-1.414 1.414l-.707-.707a1 1 0 011.414-1.414l.707.707zM3 11a1 1 0 100-2H2a1 1 0 100 2h1z"></path>
                        </svg>
                        <svg x-show="!darkMode" class="h-5 w-5 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button>

                    <div class="relative" @click.away="notifOpen = false">
                        <button
                            @click="notifOpen = !notifOpen"
                            class="relative flex h-10 w-10 items-center justify-center rounded-full text-slate-500 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span x-show="notifications.length > 0" class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full border-2 border-white bg-rose-500 dark:border-slate-900"></span>
                        </button>

                        <div
                            x-show="notifOpen"
                            x-cloak
                            x-transition.origin.top.right
                            class="absolute right-0 mt-2 w-72 rounded-2xl border border-slate-100 bg-white p-4 shadow-xl dark:border-slate-700 dark:bg-slate-800"
                        >
                            <p class="text-center text-sm text-slate-500 dark:text-slate-400">Tidak ada notifikasi</p>
                        </div>
                    </div>

                    <div class="relative" @click.away="profileOpen = false">
                        <button
                            @click="profileOpen = !profileOpen"
                            class="flex items-center gap-2 rounded-xl p-1 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800"
                        >
                            <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" class="h-8 w-8 rounded-lg border border-slate-200 dark:border-slate-700" alt="Avatar">
                            <svg class="hidden h-4 w-4 text-slate-400 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div
                            x-show="profileOpen"
                            x-cloak
                            x-transition.origin.top.right
                            class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800"
                        >
                            <div class="border-b border-slate-50 p-4 dark:border-slate-700">
                                <p class="text-sm font-bold dark:text-white">Administrator</p>
                                <p class="text-[10px] text-slate-400">admin@smartpark.com</p>
                            </div>
                            <div class="p-2">
                                <a href="#" class="block rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-700">Setting</a>
                                <button class="mt-1 w-full rounded-lg px-4 py-2 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20">Sign out</button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden p-3 sm:p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function layoutState() {
            return {
                sidebarDesktopOpen: true,
                sidebarMobileOpen: false,
                darkMode: localStorage.getItem('theme') === 'dark',
                notifOpen: false,
                profileOpen: false,
                notifications: [],
                isDesktop: window.innerWidth >= 1024,

                init() {
                    this.handleResize()
                    window.addEventListener('resize', () => this.handleResize())
                },

                handleResize() {
                    this.isDesktop = window.innerWidth >= 1024
                    if (this.isDesktop) {
                        this.sidebarMobileOpen = false
                        document.body.style.overflow = ''
                    }
                },

                toggleSidebar() {
                    if (this.isDesktop) {
                        this.sidebarDesktopOpen = !this.sidebarDesktopOpen
                    } else {
                        this.sidebarMobileOpen = !this.sidebarMobileOpen
                        document.body.style.overflow = this.sidebarMobileOpen ? 'hidden' : ''
                    }
                },

                closeMobileSidebar() {
                    this.sidebarMobileOpen = false
                    document.body.style.overflow = ''
                }
            }
        }
    </script>
</body>
</html>
