@extends('layouts.app')

@section('content')
@php
    $totalTransaksi = $transaksis->total() ?? 0;
    $parkirAktif = $transaksis->where('status_parkir', 'Inside')->count() ?? 0;
    $tiketAktif = $transaksis->where('status_tiket', 'Active')->count() ?? 0;
@endphp

<div x-data="parkirList()" class="p-4 sm:p-6 lg:p-8 transition-colors duration-300 min-h-screen bg-slate-50/50 dark:bg-slate-950">

    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <nav class="mb-2 flex flex-wrap gap-2 text-xs text-slate-500 dark:text-slate-400">
                <span>Dashboard</span>
                <span>/</span>
                <span class="font-medium text-primary-600 dark:text-primary-400">Transaksi</span>
                <span>/</span>
                <span class="font-medium text-primary-600 dark:text-primary-400">Parkir Area</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Kendaraan <span class="text-primary-600 dark:text-primary-500">Terparkir</span>
            </h1>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <button class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-4 sm:px-6 py-3 text-sm font-bold text-slate-600 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-800 active:scale-95">
                <svg class="mr-2 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Export Data
            </button>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Kendaraan Hari Ini</p>
            <p class="mt-2 text-3xl sm:text-4xl font-black text-slate-900 dark:text-white">{{ $totalTransaksi }}</p>
        </div>

        <div class="rounded-2xl bg-primary-600 p-5 shadow-lg shadow-primary-500/30 transition-colors">
            <p class="text-xs font-bold uppercase tracking-wider text-primary-100">Parkir Aktif (Inside)</p>
            <div class="mt-2 flex items-center gap-3">
                <p class="text-3xl sm:text-4xl font-black text-white">{{ $parkirAktif }}</p>
                <span class="rounded-full bg-emerald-400/20 px-2.5 py-0.5 text-[10px] font-bold text-emerald-100 uppercase tracking-widest ring-1 ring-inset ring-emerald-400/30">Live</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Tiket Valid</p>
            <div class="mt-2 flex items-center gap-3">
                <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white">{{ $tiketAktif }}</p>
                <span class="rounded-full bg-primary-50 dark:bg-primary-500/10 px-2.5 py-0.5 text-[10px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-widest ring-1 ring-inset ring-primary-500/20">Active</span>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl transition-colors">

        <div class="flex flex-col gap-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 px-4 py-4 sm:px-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-800 dark:text-slate-200">Daftar Status Kendaraan</h3>

                <div class="flex flex-col sm:flex-row gap-3">
                    <form action="{{ route('transaksi-parkirs.index') }}" method="GET" class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No Polisi..."
                            class="w-full sm:w-64 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-2 pl-9 pr-4 text-sm text-slate-900 dark:text-white outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:focus:ring-primary-500/20 transition">
                    </form>
                    <button class="inline-flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-700">
                        <svg class="mr-2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filter
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">
                        <th class="px-6 py-4">No Polisi & Tipe</th>
                        <th class="px-6 py-4">Jenis Pemilik</th>
                        <th class="px-6 py-4">Status Parkir</th>
                        <th class="px-6 py-4">Status Tiket</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    @forelse($transaksis as $item)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="font-black text-slate-900 dark:text-white">{{ $item->nomor_plat ?? '-' }}</div>
                                <div class="text-[10px] font-bold text-primary-500 uppercase tracking-tighter">{{ $item->jenis_kendaraan ?? '-' }}</div>
                            </td>

                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-700 dark:text-slate-300 text-sm">{{ $item->jenis_pemilik ?? '-' }}</div>
                            </td>

                            <td class="px-6 py-5">
                                @if($item->status_parkir === 'Inside')
                                    <span class="inline-flex items-center rounded-lg border border-emerald-100 dark:border-emerald-500/20 bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-tight">Inside</span>
                                @else
                                    <span class="inline-flex items-center rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-tight">{{ $item->status_parkir }}</span>
                                @endif
                            </td>

                            <td class="px-6 py-5">
                                @if($item->status_tiket === 'Active')
                                    <span class="inline-flex items-center rounded-lg border border-primary-100 dark:border-primary-500/20 bg-primary-50 dark:bg-primary-500/10 px-2.5 py-1 text-[11px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-tight">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-lg border border-rose-100 dark:border-rose-500/20 bg-rose-50 dark:bg-rose-500/10 px-2.5 py-1 text-[11px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-tight">{{ $item->status_tiket }}</span>
                                @endif
                            </td>

                            <td class="px-6 py-5 text-center">
                                <button @click="openDetail({{ $item->id }})" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-400 hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 dark:hover:bg-primary-500/10 dark:hover:text-primary-400 dark:hover:border-primary-500/30 transition shadow-sm bg-white dark:bg-slate-800 opacity-0 group-hover:opacity-100">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 mb-3">
                                    <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <p class="font-bold text-slate-900 dark:text-white">Belum ada data kendaraan</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kendaraan yang parkir akan otomatis muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
        <div class="border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 px-6 py-4">
            {{ $transaksis->withQueryString()->links() }}
        </div>
        @endif
    </div>

    <div x-show="detailModalOpen" x-cloak class="fixed inset-0 z-[100] hidden overflow-y-auto px-3 py-4 sm:px-4 sm:py-8" :class="{'hidden': !detailModalOpen}">

        <div x-show="detailModalOpen" x-transition.opacity.duration.300ms @click="closeModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

        <div x-show="detailModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             class="relative mx-auto w-full max-w-2xl rounded-3xl bg-white dark:bg-slate-900 shadow-2xl transition-all border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh]">

            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-5 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white">Detail Kendaraan</h2>
                </div>
                <button @click="closeModal()" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto">

                <div x-show="isLoading" class="flex flex-col items-center justify-center py-12">
                    <svg class="h-10 w-10 animate-spin text-primary-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <p class="mt-4 text-sm font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</p>
                </div>

                <div x-show="!isLoading && selectedData" class="space-y-6">

                    <div class="rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-5 text-center">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Kode Transaksi Tiket</p>
                        <p class="mt-1 font-mono text-3xl font-black tracking-tight text-slate-900 dark:text-white" x-text="selectedData?.kode_tiket"></p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">No Polisi</label>
                                <p class="mt-1 font-bold text-slate-900 dark:text-white text-lg" x-text="selectedData?.nomor_plat || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Tipe / Kendaraan</label>
                                <p class="mt-1 font-bold text-slate-700 dark:text-slate-300" x-text="selectedData?.jenis_kendaraan || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Merk & Warna</label>
                                <p class="mt-1 font-bold text-slate-700 dark:text-slate-300"><span x-text="selectedData?.merk || '-'"></span>, <span x-text="selectedData?.warna || '-'"></span></p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Nama / Tipe Pemilik</label>
                                <p class="mt-1 font-bold text-slate-700 dark:text-slate-300"><span x-text="selectedData?.nama_pemilik || '-'"></span> (<span x-text="selectedData?.jenis_pemilik || '-'"></span>)</p>
                            </div>
                        </div>

                        <div class="space-y-4 rounded-2xl bg-slate-50 dark:bg-slate-800/30 p-5 border border-slate-100 dark:border-slate-800">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Status Kehadiran</label>
                                <span class="inline-flex items-center rounded-lg bg-emerald-100 dark:bg-emerald-500/20 px-2.5 py-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-tight" x-text="selectedData?.status_parkir"></span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Waktu Masuk</label>
                                <p class="mt-1 font-bold text-slate-900 dark:text-white text-sm" x-text="selectedData?.waktu_masuk || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Waktu Keluar</label>
                                <p class="mt-1 font-bold text-slate-900 dark:text-white text-sm" x-text="selectedData?.waktu_keluar || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Denda Manual</label>
                                <p class="font-black text-rose-600 dark:text-rose-400 text-lg" x-text="'Rp ' + (selectedData?.denda_manual || '0')"></p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 dark:border-slate-800 p-4">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Catatan Tambahan & Keterangan</label>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400" x-text="selectedData?.catatan_kendaraan || 'Tidak ada catatan kondisi saat kendaraan masuk.'"></p>
                        <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-400" x-text="selectedData?.keterangan || '-'"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-800 px-6 py-4 shrink-0 flex justify-end bg-slate-50/50 dark:bg-slate-800/50">
                <button type="button" @click="closeModal()" class="w-full sm:w-auto rounded-xl bg-slate-900 dark:bg-white px-8 py-3 text-sm font-bold text-white dark:text-slate-900 transition hover:bg-slate-800 dark:hover:bg-slate-200 shadow-lg">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Konfigurasi Color Palette Tailwind Custom dari Kodemu Sebelumnya */
    :root {
        --color-primary-500: #6366f1;
        --color-primary-600: #4f46e5;
        --color-primary-700: #4338ca;
    }
    .text-primary-600 { color: var(--color-primary-600); }
    .text-primary-500 { color: var(--color-primary-500); }
    .bg-primary-600 { background-color: var(--color-primary-600); }
    .bg-primary-500 { background-color: var(--color-primary-500); }
    .focus\:border-primary-500:focus { border-color: var(--color-primary-500); }
    .focus\:ring-primary-50:focus { --tw-ring-color: rgba(99, 102, 241, 0.1); }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('parkirList', () => ({
            detailModalOpen: false,
            isLoading: false,
            selectedData: null,

            async openDetail(id) {
                // Mencegah scroll body belakang saat modal terbuka
                document.body.style.overflow = 'hidden';
                this.detailModalOpen = true;
                this.isLoading = true;

                try {
                    // Fetch data sesuai controller API kamu
                    const response = await fetch(`/api/transaksi-parkirs/${id}`);
                    const result = await response.json();

                    this.selectedData = result.data;
                    this.isLoading = false;
                } catch (error) {
                    console.error('Terjadi kesalahan fetch:', error);
                    this.isLoading = false;
                }
            },

            closeModal() {
                this.detailModalOpen = false;
                document.body.style.overflow = ''; // Kembalikan scroll
                // Kosongkan data setelah animasi selesai agar tidak berkedip
                setTimeout(() => {
                    this.selectedData = null;
                }, 300);
            }
        }));
    });
</script>
@endsection
