@extends('layouts.app')

@section('content')
@php
    $totalTarif = $tarifParkirs->total();
    $totalAktif = $tarifParkirs->getCollection()->where('status_aktif', true)->count();
    $totalNonAktif = $tarifParkirs->getCollection()->where('status_aktif', false)->count();
@endphp

<div class="min-h-screen bg-slate-50 text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <nav class="mb-2 flex flex-wrap gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <span>Dashboard</span>
                    <span>/</span>
                    <span class="font-medium text-primary-600 dark:text-primary-400">Master Data</span>
                    <span>/</span>
                    <span class="font-medium text-primary-600 dark:text-primary-400">Tarif Parkir</span>
                </nav>
                <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">
                    Parking <span class="text-primary-600 dark:text-primary-400">Tariff</span>
                </h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Kelola tarif parkir berdasarkan lahan, jenis kendaraan, dan jenis pemilik.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <button
                    type="button"
                    onclick="openBerlakuModal()"
                    class="inline-flex items-center justify-center rounded-xl border border-primary-200 bg-white px-4 py-3 text-sm font-bold text-primary-600 shadow-sm transition hover:bg-primary-50 dark:border-primary-900/50 dark:bg-slate-900 dark:text-primary-400 dark:hover:bg-slate-800"
                >
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                    </svg>
                    Cek Tarif Berlaku
                </button>

                <button
                    type="button"
                    onclick="openCreateModal()"
                    class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-primary-700 active:scale-95"
                >
                    <svg class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
                    </svg>
                    Tambah Tarif
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700 dark:border-amber-900/40 dark:bg-amber-950/40 dark:text-amber-300">
                {{ session('warning') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/40 dark:bg-rose-950/40 dark:text-rose-300">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Tarif</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ $totalTarif }}</p>
            </div>

            <div class="rounded-2xl bg-primary-600 p-5 shadow-lg">
                <p class="text-xs font-bold uppercase tracking-wider text-primary-100">Tarif Aktif</p>
                <div class="mt-2 flex items-center gap-3">
                    <p class="text-3xl font-black text-white">{{ $totalAktif }}</p>
                    <span class="rounded-full bg-emerald-400/20 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-widest text-emerald-100">Running</span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tarif Nonaktif</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ $totalNonAktif }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 bg-slate-50/70 px-4 py-4 dark:border-slate-800 dark:bg-slate-950/40 sm:px-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-800 dark:text-slate-200">Daftar Tarif Parkir</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Histori tarif tetap tersimpan, perubahan tarif dilakukan dengan penambahan data baru.</p>
                    </div>

                    <form method="GET" action="{{ route('tarif-parkirs.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-5">
                        <input
                            type="text"
                            name="jenis_kendaraan_id"
                            value="{{ request('jenis_kendaraan_id') }}"
                            placeholder="ID Kendaraan"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        >
                        <input
                            type="text"
                            name="jenis_pemilik_id"
                            value="{{ request('jenis_pemilik_id') }}"
                            placeholder="ID Pemilik"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        >
                        <input
                            type="text"
                            name="lahan_id"
                            value="{{ request('lahan_id') }}"
                            placeholder="ID Lahan"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        >
                        <select
                            name="status_aktif"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        >
                            <option value="">Semua Status</option>
                            <option value="1" @selected(request('status_aktif') === '1')>Aktif</option>
                            <option value="0" @selected(request('status_aktif') === '0')>Nonaktif</option>
                        </select>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-primary-600 dark:hover:bg-primary-700">
                                Filter
                            </button>
                            <a href="{{ route('tarif-parkirs.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-widest text-slate-400 dark:border-slate-800">
                            <th class="w-16 px-6 py-4 text-center">No</th>
                            <th class="px-6 py-4">Lahan</th>
                            <th class="px-6 py-4">Kendaraan & Pemilik</th>
                            <th class="px-6 py-4">Tarif</th>
                            <th class="px-6 py-4">Periode</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                        @forelse($tarifParkirs as $item)
                            @php
                                $showData = [
                                    'id' => $item->id,
                                    'lahan' => optional($item->lahan)->nama_lahan ?: 'Default Semua Lahan',
                                    'jenis_kendaraan' => optional($item->jenisKendaraan)->nama_jenis_kendaraan ?: '-',
                                    'jenis_pemilik' => optional($item->jenisPemilik)->nama_jenis_pemilik ?: '-',
                                    'biaya_masuk' => number_format($item->biaya_masuk, 0, ',', '.'),
                                    'biaya_per_jam' => number_format($item->biaya_per_jam, 0, ',', '.'),
                                    'biaya_maksimal' => $item->biaya_maksimal ? number_format($item->biaya_maksimal, 0, ',', '.') : '-',
                                    'gratis_menit' => $item->gratis_menit,
                                    'status_aktif' => $item->status_aktif ? 'Aktif' : 'Nonaktif',
                                    'masa_berlaku' => \Carbon\Carbon::parse($item->masa_berlaku)->format('d M Y H:i'),
                                    'selesai_berlaku' => $item->selesai_berlaku
                                        ? \Carbon\Carbon::parse($item->selesai_berlaku)->format('d M Y H:i')
                                        : 'Selamanya / sampai ada tarif baru',
                                ];

                                $nonaktifData = [
                                    'id' => $item->id,
                                    'label' => (optional($item->lahan)->nama_lahan ?? 'Default Semua Lahan')
                                        . ' - ' .
                                        (optional($item->jenisKendaraan)->nama_jenis_kendaraan ?? '-')
                                        . ' / ' .
                                        (optional($item->jenisPemilik)->nama_jenis_pemilik ?? '-'),
                                ];
                            @endphp

                            <tr class="transition-colors hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                                <td class="px-6 py-5 text-center text-xs font-bold text-slate-400">
                                    {{ $tarifParkirs->firstItem() + $loop->index }}
                                </td>

                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ optional($item->lahan)->nama_lahan ?? 'Default Semua Lahan' }}
                                    </div>
                                    <div class="text-[10px] font-bold uppercase tracking-tight text-primary-500">
                                        {{ $item->lahan_id ? 'LAHAN KHUSUS' : 'DEFAULT' }}
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ optional($item->jenisKendaraan)->nama_jenis_kendaraan ?? '-' }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ optional($item->jenisPemilik)->nama_jenis_pemilik ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="space-y-1 text-xs text-slate-600 dark:text-slate-300">
                                        <div>Masuk: <span class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($item->biaya_masuk, 0, ',', '.') }}</span></div>
                                        <div>Per Jam: <span class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($item->biaya_per_jam, 0, ',', '.') }}</span></div>
                                        <div>Maks: <span class="font-bold text-slate-900 dark:text-white">{{ $item->biaya_maksimal ? 'Rp ' . number_format($item->biaya_maksimal, 0, ',', '.') : '-' }}</span></div>
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="text-xs text-slate-600 dark:text-slate-300">
                                        <div class="font-semibold text-slate-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($item->masa_berlaku)->format('d M Y H:i') }}
                                        </div>
                                        <div class="mt-1 text-slate-500 dark:text-slate-400">
                                            s/d
                                            {{ $item->selesai_berlaku ? \Carbon\Carbon::parse($item->selesai_berlaku)->format('d M Y H:i') : 'Selamanya / sampai ada tarif baru' }}
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-xs">
                                    @if($item->status_aktif)
                                        <span class="inline-flex items-center rounded-lg border border-emerald-100 bg-emerald-50 px-2.5 py-1 font-bold uppercase tracking-tight text-emerald-600 dark:border-emerald-900/40 dark:bg-emerald-950/40 dark:text-emerald-300">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1 font-bold uppercase tracking-tight text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            onclick='openShowModal(@json($showData))'
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50 hover:text-primary-600 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-primary-400"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        @if($item->status_aktif)
                                            <button
                                                type="button"
                                                onclick='openNonaktifModal(@json($nonaktifData))'
                                                class="inline-flex h-9 items-center justify-center rounded-xl border border-rose-100 px-3 text-xs font-bold text-rose-500 transition hover:bg-rose-50 hover:text-rose-600 dark:border-rose-900/40 dark:text-rose-300 dark:hover:bg-rose-950/30"
                                            >
                                                Nonaktifkan
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center font-medium text-slate-400 dark:text-slate-500">
                                    Belum ada data tarif parkir tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-4 p-4 md:hidden">
                @forelse($tarifParkirs as $item)
                    @php
                        $mobileShowData = [
                            'id' => $item->id,
                            'lahan' => optional($item->lahan)->nama_lahan ?? 'Default Semua Lahan',
                            'jenis_kendaraan' => optional($item->jenisKendaraan)->nama_jenis_kendaraan ?? '-',
                            'jenis_pemilik' => optional($item->jenisPemilik)->nama_jenis_pemilik ?? '-',
                            'biaya_masuk' => number_format($item->biaya_masuk, 0, ',', '.'),
                            'biaya_per_jam' => number_format($item->biaya_per_jam, 0, ',', '.'),
                            'biaya_maksimal' => $item->biaya_maksimal ? number_format($item->biaya_maksimal, 0, ',', '.') : '-',
                            'gratis_menit' => $item->gratis_menit,
                            'status_aktif' => $item->status_aktif ? 'Aktif' : 'Nonaktif',
                            'masa_berlaku' => \Carbon\Carbon::parse($item->masa_berlaku)->format('d M Y H:i'),
                            'selesai_berlaku' => $item->selesai_berlaku
                                ? \Carbon\Carbon::parse($item->selesai_berlaku)->format('d M Y H:i')
                                : 'Selamanya / sampai ada tarif baru',
                        ];

                        $mobileNonaktifData = [
                            'id' => $item->id,
                            'label' => (optional($item->lahan)->nama_lahan ?? 'Default Semua Lahan')
                                . ' - ' .
                                (optional($item->jenisKendaraan)->nama_jenis_kendaraan ?? '-')
                                . ' / ' .
                                (optional($item->jenisPemilik)->nama_jenis_pemilik ?? '-'),
                        ];
                    @endphp

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white">
                                    {{ optional($item->lahan)->nama_lahan ?? 'Default Semua Lahan' }}
                                </h3>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    {{ optional($item->jenisKendaraan)->nama_jenis_kendaraan ?? '-' }} • {{ optional($item->jenisPemilik)->nama_jenis_pemilik ?? '-' }}
                                </p>
                            </div>

                            @if($item->status_aktif)
                                <span class="rounded-lg border border-emerald-100 bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase text-emerald-600 dark:border-emerald-900/40 dark:bg-emerald-950/40 dark:text-emerald-300">Aktif</span>
                            @else
                                <span class="rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1 text-[10px] font-bold uppercase text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">Nonaktif</span>
                            @endif
                        </div>

                        <div class="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-300">
                            <div>Masuk: <span class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($item->biaya_masuk, 0, ',', '.') }}</span></div>
                            <div>Per Jam: <span class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($item->biaya_per_jam, 0, ',', '.') }}</span></div>
                            <div>Maksimal: <span class="font-bold text-slate-900 dark:text-white">{{ $item->biaya_maksimal ? 'Rp ' . number_format($item->biaya_maksimal, 0, ',', '.') : '-' }}</span></div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button
                                type="button"
                                onclick='openShowModal(@json($mobileShowData))'
                                class="flex-1 rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            >
                                Detail
                            </button>

                            @if($item->status_aktif)
                                <button
                                    type="button"
                                    onclick='openNonaktifModal(@json($mobileNonaktifData))'
                                    class="flex-1 rounded-xl bg-rose-500 px-4 py-2 text-sm font-bold text-white transition hover:bg-rose-600"
                                >
                                    Nonaktifkan
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center text-sm text-slate-400 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-500">
                        Belum ada data tarif parkir tersedia.
                    </div>
                @endforelse
            </div>

            <div class="border-t border-slate-100 px-4 py-4 dark:border-slate-800 sm:px-6">
                {{ $tarifParkirs->links() }}
            </div>
        </div>
    </div>
</div>

<div id="createModal" class="fixed inset-0 z-[100] hidden overflow-y-auto px-3 py-4 sm:px-4 sm:py-8">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeCreateModal()"></div>

    <div class="relative mx-auto w-full max-w-4xl rounded-3xl bg-white shadow-2xl dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 dark:border-slate-800">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Tambah Tarif Parkir</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tambahkan tarif baru untuk lahan, kendaraan, dan jenis pemilik.</p>
            </div>
            <button onclick="closeCreateModal()" class="text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('tarif-parkirs.store') }}" class="p-6">
            @csrf
            <input type="hidden" name="confirm_replace" value="{{ session('needs_confirmation') ? 1 : 0 }}">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        Pilih Lahan
                    </label>

                    <div class="relative">
                        <input
                            type="text"
                            id="lahanSearch"
                            placeholder="Cari nama lahan..."
                            class="mb-2 w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        >

                        <select
                            name="lahan_id"
                            id="lahan_id"
                            class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        >
                            <option value="" @selected(old('lahan_id') === null || old('lahan_id') === '')>Default Semua Lahan</option>
                            @foreach($lahans as $lahan)
                                <option value="{{ $lahan->id }}" @selected((string) old('lahan_id') === (string) $lahan->id)>
                                    {{ $lahan->nama_lahan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        Jenis Kendaraan
                    </label>
                    <select
                        name="jenis_kendaraan_id"
                        id="jenis_kendaraan_id"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        required
                    >
                        <option value="">Pilih Jenis Kendaraan</option>
                        @foreach($jenisKendaraans as $jenisKendaraan)
                            <option value="{{ $jenisKendaraan->id }}" @selected((string) old('jenis_kendaraan_id') === (string) $jenisKendaraan->id)>
                                {{ $jenisKendaraan->nama_jenis_kendaraan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                        Jenis Pemilik
                    </label>
                    <select
                        name="jenis_pemilik_id"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        required
                    >
                        <option value="">Pilih Jenis Pemilik</option>
                        @foreach($jenisPemiliks as $jenisPemilik)
                            <option value="{{ $jenisPemilik->id }}" @selected((string) old('jenis_pemilik_id') === (string) $jenisPemilik->id)>
                                {{ $jenisPemilik->nama_jenis_pemilik }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Gratis Menit</label>
                    <input
                        type="number"
                        name="gratis_menit"
                        value="{{ old('gratis_menit', 0) }}"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Biaya Masuk</label>
                    <input
                        type="number"
                        step="0.01"
                        name="biaya_masuk"
                        value="{{ old('biaya_masuk', 0) }}"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Biaya Per Jam</label>
                    <input
                        type="number"
                        step="0.01"
                        name="biaya_per_jam"
                        value="{{ old('biaya_per_jam') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        required
                    >
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Biaya Maksimal</label>
                    <input
                        type="number"
                        step="0.01"
                        name="biaya_maksimal"
                        value="{{ old('biaya_maksimal') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Mulai Berlaku</label>
                    <input
                        type="datetime-local"
                        name="masa_berlaku"
                        value="{{ old('masa_berlaku') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        required
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Selesai Berlaku</label>
                    <input
                        type="datetime-local"
                        name="selesai_berlaku"
                        value="{{ old('selesai_berlaku') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                    >
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 dark:border-slate-800 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    onclick="closeCreateModal()"
                    class="w-full rounded-xl border border-slate-200 px-6 py-3 text-sm font-bold text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 sm:w-auto"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="w-full rounded-xl bg-primary-600 px-8 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-primary-700 sm:w-auto"
                >
                    Simpan Tarif
                </button>
            </div>
        </form>
    </div>
</div>

<div id="showModal" class="fixed inset-0 z-[101] hidden overflow-y-auto px-3 py-4 sm:px-4 sm:py-8">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeShowModal()"></div>

    <div class="relative mx-auto w-full max-w-2xl rounded-3xl bg-white shadow-2xl dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 dark:border-slate-800">
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Detail Tarif Parkir</h2>
            <button onclick="closeShowModal()" class="text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Lahan</p>
                    <p id="show_lahan" class="mt-2 text-sm font-bold text-slate-900 dark:text-white"></p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Status</p>
                    <p id="show_status" class="mt-2 text-sm font-bold text-slate-900 dark:text-white"></p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Jenis Kendaraan</p>
                    <p id="show_jenis_kendaraan" class="mt-2 text-sm font-bold text-slate-900 dark:text-white"></p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Jenis Pemilik</p>
                    <p id="show_jenis_pemilik" class="mt-2 text-sm font-bold text-slate-900 dark:text-white"></p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Biaya Masuk</p>
                    <p id="show_biaya_masuk" class="mt-2 text-sm font-bold text-slate-900 dark:text-white"></p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Biaya Per Jam</p>
                    <p id="show_biaya_per_jam" class="mt-2 text-sm font-bold text-slate-900 dark:text-white"></p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Biaya Maksimal</p>
                    <p id="show_biaya_maksimal" class="mt-2 text-sm font-bold text-slate-900 dark:text-white"></p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Gratis Menit</p>
                    <p id="show_gratis_menit" class="mt-2 text-sm font-bold text-slate-900 dark:text-white"></p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60 md:col-span-2">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Periode Berlaku</p>
                    <p id="show_periode" class="mt-2 text-sm font-bold text-slate-900 dark:text-white"></p>
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t border-slate-100 pt-6 dark:border-slate-800">
                <button
                    type="button"
                    onclick="closeShowModal()"
                    class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-primary-600 dark:hover:bg-primary-700"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div id="nonaktifModal" class="fixed inset-0 z-[102] hidden overflow-y-auto px-3 py-4 sm:px-4 sm:py-8">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeNonaktifModal()"></div>

    <div class="relative mx-auto w-full max-w-lg rounded-3xl bg-white shadow-2xl dark:bg-slate-900">
        <div class="border-b border-slate-100 px-6 py-5 dark:border-slate-800">
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Nonaktifkan Tarif</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tarif akan dinonaktifkan dan tidak dipakai lagi setelah waktu yang ditentukan.</p>
        </div>

        <form id="nonaktifForm" method="POST" class="p-6">
            @csrf
            @method('PATCH')

            <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700 dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-300">
                <span class="font-bold">Tarif:</span>
                <span id="nonaktifLabel"></span>
            </div>

            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Waktu Nonaktif</label>
                <input
                    type="datetime-local"
                    name="waktu_nonaktif"
                    class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                >
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 dark:border-slate-800 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    onclick="closeNonaktifModal()"
                    class="w-full rounded-xl border border-slate-200 px-6 py-3 text-sm font-bold text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 sm:w-auto"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="w-full rounded-xl bg-rose-500 px-8 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-rose-600 sm:w-auto"
                >
                    Ya, Nonaktifkan
                </button>
            </div>
        </form>
    </div>
</div>

<div id="berlakuModal" class="fixed inset-0 z-[103] hidden overflow-y-auto px-3 py-4 sm:px-4 sm:py-8">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeBerlakuModal()"></div>

    <div class="relative mx-auto w-full max-w-3xl rounded-3xl bg-white shadow-2xl dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 dark:border-slate-800">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Cek Tarif Berlaku</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Gunakan untuk mencari tarif aktif berdasarkan lahan, jenis kendaraan, dan jenis pemilik pada waktu tertentu.</p>
            </div>
            <button onclick="closeBerlakuModal()" class="text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form method="GET" action="{{ route('tarif-parkirs.berlaku') }}" class="p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">ID Lahan</label>
                    <input
                        type="number"
                        name="lahan_id"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        placeholder="Kosongkan untuk tarif default"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Jenis Kendaraan</label>
                    <input
                        type="number"
                        name="jenis_kendaraan_id"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        required
                    >
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Jenis Pemilik</label>
                    <input
                        type="number"
                        name="jenis_pemilik_id"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                        required
                    >
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Waktu</label>
                    <input
                        type="datetime-local"
                        name="waktu"
                        class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:focus:ring-primary-950"
                    >
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 dark:border-slate-800 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    onclick="closeBerlakuModal()"
                    class="w-full rounded-xl border border-slate-200 px-6 py-3 text-sm font-bold text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 sm:w-auto"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="w-full rounded-xl bg-primary-600 px-8 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-primary-700 sm:w-auto"
                >
                    Cari Tarif
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const createModal = document.getElementById('createModal');
    const showModal = document.getElementById('showModal');
    const nonaktifModal = document.getElementById('nonaktifModal');
    const berlakuModal = document.getElementById('berlakuModal');
    const nonaktifForm = document.getElementById('nonaktifForm');
    const nonaktifLabel = document.getElementById('nonaktifLabel');

    const lahanSelect = document.getElementById('lahan_id');
    const lahanSearch = document.getElementById('lahanSearch');
    const jenisKendaraanSelect = document.getElementById('jenis_kendaraan_id');

    function lockBody() {
        document.body.style.overflow = 'hidden';
    }

    function unlockBody() {
        document.body.style.overflow = '';
    }

    function openCreateModal() {
        createModal.classList.remove('hidden');
        lockBody();
    }

    function closeCreateModal() {
        createModal.classList.add('hidden');
        unlockBody();
    }

    function openShowModal(data) {
        document.getElementById('show_lahan').innerText = data.lahan;
        document.getElementById('show_status').innerText = data.status_aktif;
        document.getElementById('show_jenis_kendaraan').innerText = data.jenis_kendaraan;
        document.getElementById('show_jenis_pemilik').innerText = data.jenis_pemilik;
        document.getElementById('show_biaya_masuk').innerText = 'Rp ' + data.biaya_masuk;
        document.getElementById('show_biaya_per_jam').innerText = 'Rp ' + data.biaya_per_jam;
        document.getElementById('show_biaya_maksimal').innerText = data.biaya_maksimal === '-' ? '-' : 'Rp ' + data.biaya_maksimal;
        document.getElementById('show_gratis_menit').innerText = data.gratis_menit + ' menit';
        document.getElementById('show_periode').innerText = data.masa_berlaku + ' s/d ' + data.selesai_berlaku;

        showModal.classList.remove('hidden');
        lockBody();
    }

    function closeShowModal() {
        showModal.classList.add('hidden');
        unlockBody();
    }

    function openNonaktifModal(data) {
        nonaktifForm.action = `/tarif-parkirs/${data.id}/nonaktifkan`;
        nonaktifLabel.innerText = data.label;
        nonaktifModal.classList.remove('hidden');
        lockBody();
    }

    function closeNonaktifModal() {
        nonaktifModal.classList.add('hidden');
        unlockBody();
    }

    function openBerlakuModal() {
        berlakuModal.classList.remove('hidden');
        lockBody();
    }

    function closeBerlakuModal() {
        berlakuModal.classList.add('hidden');
        unlockBody();
    }

    async function loadJenisKendaraanByLahan(lahanId, selectedId = null) {
        try {
            jenisKendaraanSelect.innerHTML = '<option value="">Memuat jenis kendaraan...</option>';

            const url = new URL("{{ route('tarif-parkirs.kendaraan-by-lahan') }}", window.location.origin);

            if (lahanId) {
                url.searchParams.set('lahan_id', lahanId);
            }

            const response = await fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            jenisKendaraanSelect.innerHTML = '<option value="">Pilih Jenis Kendaraan</option>';

            if (result.success && Array.isArray(result.data)) {
                result.data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nama_jenis_kendaraan;
                    jenisKendaraanSelect.appendChild(option);
                });

                if (selectedId) {
                    jenisKendaraanSelect.value = String(selectedId);
                } else if (result.auto_selected_id) {
                    jenisKendaraanSelect.value = String(result.auto_selected_id);
                }
            }
        } catch (error) {
            jenisKendaraanSelect.innerHTML = '<option value="">Gagal memuat jenis kendaraan</option>';
            console.error(error);
        }
    }

    if (lahanSelect) {
        lahanSelect.addEventListener('change', function () {
            const lahanId = this.value || null;
            loadJenisKendaraanByLahan(lahanId);
        });
    }

    if (lahanSearch && lahanSelect) {
        lahanSearch.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            const options = Array.from(lahanSelect.options);

            options.forEach((option, index) => {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                option.hidden = !option.text.toLowerCase().includes(keyword);
            });
        });
    }

    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeShowModal();
            closeNonaktifModal();
            closeBerlakuModal();
        }
    });

    @if($errors->any() || session('needs_confirmation'))
        openCreateModal();
    @endif

    @if(old('lahan_id') || old('jenis_kendaraan_id'))
        loadJenisKendaraanByLahan(
            @json(old('lahan_id')),
            @json(old('jenis_kendaraan_id'))
        );
    @endif
</script>

<style>
    :root {
        --color-primary-400: #60a5fa;
        --color-primary-500: #3b82f6;
        --color-primary-600: #2563eb;
        --color-primary-700: #1d4ed8;
    }

    .text-primary-400 { color: var(--color-primary-400); }
    .text-primary-500 { color: var(--color-primary-500); }
    .text-primary-600 { color: var(--color-primary-600); }
    .bg-primary-600 { background-color: var(--color-primary-600); }
    .hover\:bg-primary-700:hover { background-color: var(--color-primary-700); }
    .border-primary-200 { border-color: #bfdbfe; }
    .dark .dark\:text-primary-400 { color: var(--color-primary-400); }
    .dark .dark\:border-primary-900\/50 { border-color: rgba(30, 58, 138, 0.5); }
    .focus\:border-primary-500:focus { border-color: var(--color-primary-500); }
    .focus\:ring-primary-50:focus { --tw-ring-color: rgba(59, 130, 246, 0.12); }
    .dark .dark\:focus\:ring-primary-950:focus { --tw-ring-color: rgba(23, 37, 84, 0.5); }
    .dark .dark\:bg-primary-600 { background-color: var(--color-primary-600); }
    .dark .dark\:hover\:bg-primary-700:hover { background-color: var(--color-primary-700); }
</style>
@endsection
