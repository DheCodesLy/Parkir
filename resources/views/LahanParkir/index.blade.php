@extends('layouts.app')

@section('content')
@php
    $totalLahan = collect($lahanParkirs)->count();
    $totalKapasitas = collect($lahanParkirs)->sum('kapasitas');
    $totalSisaSlot = collect($lahanParkirs)->sum('sisa_slot');
    $totalAktif = collect($lahanParkirs)->where('status_aktif', true)->count();
@endphp

<div class="p-4 sm:p-6 lg:p-8 transition-colors duration-300">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <nav class="mb-2 flex flex-wrap gap-2 text-xs text-slate-500 dark:text-slate-400">
                <span>Dashboard</span>
                <span>/</span>
                <span class="font-medium text-primary-600 dark:text-primary-400">Lahan Parkir</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Parkir <span class="text-primary-600">Overview</span>
            </h1>
        </div>

        <button
            type="button"
            id="openCreateModal"
            class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-primary-600 px-4 sm:px-6 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-primary-700 active:scale-95"
        >
            <svg class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
            </svg>
            Tambah Lahan Baru
        </button>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Lahan</p>
            <p class="mt-2 text-3xl sm:text-4xl font-black text-slate-900 dark:text-white">{{ $totalLahan }}</p>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Kapasitas</p>
            <p class="mt-2 text-3xl sm:text-4xl font-black text-slate-900 dark:text-white">{{ $totalKapasitas }}</p>
        </div>

        <div class="rounded-2xl bg-primary-600 p-5 shadow-lg transition-colors">
            <p class="text-xs font-bold uppercase tracking-wider text-primary-100">Sisa Slot Tersedia</p>
            <p class="mt-2 text-3xl sm:text-4xl font-black text-white">{{ $totalSisaSlot }}</p>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Status Aktif</p>
            <div class="mt-2 flex items-center gap-3">
                <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white">{{ $totalAktif }}</p>
                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">Online</span>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl transition-colors dark:border-slate-700 dark:bg-slate-800">
        <div class="flex flex-col gap-4 border-b border-slate-100 bg-slate-50/50 px-4 py-4 sm:px-6 dark:border-slate-700 dark:bg-slate-800/50">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-800 dark:text-slate-200">Daftar Inventaris Lahan</h3>
                <div class="relative w-full sm:w-72">
                    <input
                        id="searchInput"
                        type="text"
                        placeholder="Cari lahan..."
                        class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-900 outline-none focus:border-primary-400 focus:ring-4 focus:ring-primary-50 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:focus:ring-primary-900/20"
                    >
                    <svg class="absolute left-3 top-3 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse" id="parkingTable">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-widest text-slate-400 dark:border-slate-700 dark:text-slate-500">
                        <th class="px-6 py-4">Informasi Lahan</th>
                        <th class="px-6 py-4">Okupansi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Target Pengguna</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                    @forelse($lahanParkirs as $item)
                        @php
                            $searchBlob = strtolower($item['nama_lahan'].' '.collect($item['jenis_pemilik'])->implode(' '));
                            $percent = $item['kapasitas'] > 0 ? (($item['kapasitas'] - $item['sisa_slot']) / $item['kapasitas']) * 100 : 0;
                        @endphp
                        <tr class="parking-row hover:bg-slate-50/80 dark:hover:bg-slate-700/30" data-search="{{ $searchBlob }}">
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900 dark:text-slate-200">{{ $item['nama_lahan'] }}</div>
                                <div class="text-[10px] font-medium text-slate-400 dark:text-slate-500">ID-{{ str_pad($item['id'], 5, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 w-24 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                                        <div class="h-full bg-primary-500" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $item['sisa_slot'] }}/{{ $item['kapasitas'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-xs">
                                @if($item['status_aktif'])
                                    <span class="inline-flex items-center rounded-lg border border-emerald-100 bg-emerald-50 px-2.5 py-1 font-bold text-emerald-600 dark:border-emerald-800/50 dark:bg-emerald-900/20 dark:text-emerald-400">Aktif</span>
                                @else
                                    <span class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1 font-bold text-slate-500 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-400">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item['jenis_pemilik'] as $p)
                                        <span class="rounded border border-blue-100 bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-600 dark:border-blue-800/50 dark:bg-blue-900/20 dark:text-blue-400">{{ $p }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <button
                                        class="editButton inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-primary-600 dark:border-slate-600 dark:text-slate-400 dark:hover:bg-slate-700"
                                        data-id="{{ $item['id'] }}"
                                        data-nama_lahan="{{ $item['nama_lahan'] }}"
                                        data-kapasitas="{{ $item['kapasitas'] }}"
                                        data-pengguna='@json($item['pengguna_parkir'])'
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('LahanParkir.destroy', $item['id']) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-100 text-rose-400 hover:bg-rose-50 hover:text-rose-600 dark:border-rose-900/50 dark:hover:bg-rose-900/20"
                                            onclick="return confirm('Hapus lahan?')"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center font-medium text-slate-400 dark:bg-slate-800">Belum ada data tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 gap-4 p-4 md:hidden" id="parkingCards">
            @forelse($lahanParkirs as $item)
                @php
                    $searchBlob = strtolower($item['nama_lahan'].' '.collect($item['jenis_pemilik'])->implode(' '));
                    $percent = $item['kapasitas'] > 0 ? (($item['kapasitas'] - $item['sisa_slot']) / $item['kapasitas']) * 100 : 0;
                @endphp
                <div class="parking-card rounded-2xl border border-slate-200 p-4 dark:border-slate-700" data-search="{{ $searchBlob }}">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-slate-100">{{ $item['nama_lahan'] }}</h4>
                            <p class="text-[10px] font-medium text-slate-400 dark:text-slate-500">ID-{{ str_pad($item['id'], 5, '0', STR_PAD_LEFT) }}</p>
                        </div>

                        @if($item['status_aktif'])
                            <span class="inline-flex items-center rounded-lg border border-emerald-100 bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-600 dark:border-emerald-800/50 dark:bg-emerald-900/20 dark:text-emerald-400">Aktif</span>
                        @else
                            <span class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-500 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-400">Nonaktif</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <div class="mb-1 flex items-center justify-between text-xs font-bold text-slate-600 dark:text-slate-300">
                            <span>Okupansi</span>
                            <span>{{ $item['sisa_slot'] }}/{{ $item['kapasitas'] }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                            <div class="h-full bg-primary-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-1">
                        @foreach($item['jenis_pemilik'] as $p)
                            <span class="rounded border border-blue-100 bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-600 dark:border-blue-800/50 dark:bg-blue-900/20 dark:text-blue-400">{{ $p }}</span>
                        @endforeach
                    </div>

                    <div class="flex gap-2">
                        <button
                            class="editButton flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700"
                            data-id="{{ $item['id'] }}"
                            data-nama_lahan="{{ $item['nama_lahan'] }}"
                            data-kapasitas="{{ $item['kapasitas'] }}"
                            data-pengguna='@json($item['pengguna_parkir'])'
                        >
                            Edit
                        </button>

                        <form action="{{ route('LahanParkir.destroy', $item['id']) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="w-full rounded-xl border border-rose-100 px-3 py-2 text-sm font-bold text-rose-500 hover:bg-rose-50 dark:border-rose-900/50 dark:hover:bg-rose-900/20"
                                onclick="return confirm('Hapus lahan?')"
                            >
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center text-sm text-slate-400 dark:border-slate-700">
                    Belum ada data tersedia.
                </div>
            @endforelse
        </div>
    </div>
</div>

<div id="parkingModal" class="fixed inset-0 z-[100] hidden overflow-y-auto px-3 py-4 sm:px-4 sm:py-8">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <div class="relative mx-auto w-full max-w-2xl rounded-2xl sm:rounded-3xl bg-white shadow-2xl transition-all dark:bg-slate-800">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-4 sm:px-6 sm:py-5 dark:border-slate-700">
            <h2 id="modalTitle" class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">Konfigurasi Lahan</h2>
            <button id="closeModal" class="text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="parkingForm" method="POST" action="{{ route('LahanParkir.store') }}" class="p-4 sm:p-6">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Identitas Lahan</label>
                        <input
                            type="text"
                            id="namaLahan"
                            name="nama_lahan"
                            class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:ring-primary-900/20"
                            placeholder="Contoh: Area Parkir Basement A"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Total Kapasitas (Slot)</label>
                        <input
                            type="number"
                            id="kapasitasInput"
                            name="kapasitas"
                            class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-50 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:ring-primary-900/20"
                        >
                    </div>
                </div>

                <div class="rounded-2xl border border-dashed border-slate-100 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                    <p class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Petunjuk</p>
                    <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-primary-500"></span>
                            Pastikan kapasitas sesuai marka fisik di lapangan.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-primary-500"></span>
                            Tentukan kombinasi pengguna.
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mb-6">
                <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-2 dark:border-slate-700">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-slate-900 dark:text-white">Kombinasi Pengguna</h3>
                    <button type="button" id="addPenggunaButton" class="text-xs font-bold text-primary-600 hover:text-primary-700">+ Tambah Baris</button>
                </div>
                <div id="penggunaContainer" class="space-y-3"></div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end dark:border-slate-700">
                <button
                    type="button"
                    id="cancelModal"
                    class="w-full sm:w-auto rounded-xl border border-slate-200 px-6 py-3 text-sm font-bold text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-700"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="w-full sm:w-auto rounded-xl bg-slate-900 px-6 sm:px-8 py-3 text-sm font-bold text-white transition hover:bg-slate-800 dark:bg-primary-600 dark:hover:bg-primary-700"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<template id="penggunaTemplate">
    <div class="pengguna-item rounded-2xl border border-slate-100 bg-slate-50/50 p-4 transition dark:border-slate-700 dark:bg-slate-900/50">
        <input type="hidden" class="pengguna-id-input">

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_1fr_auto] sm:items-center">
            <div>
                <select class="jenis-pemilik-select w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 focus:ring-primary-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                    <option value="">Jenis Pemilik</option>
                    @foreach($jenisPemiliks as $pemilik)
                        <option value="{{ $pemilik->id }}">{{ $pemilik->nama_jenis_pemilik }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select class="jenis-kendaraan-select w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 focus:ring-primary-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                    <option value="">Jenis Kendaraan</option>
                    @foreach($jenisKendaraans as $kendaraan)
                        <option value="{{ $kendaraan->id }}">{{ $kendaraan->nama_jenis_kendaraan }}</option>
                    @endforeach
                </select>
            </div>

            <button type="button" class="removePenggunaButton inline-flex h-10 w-full sm:w-10 items-center justify-center rounded-xl border border-rose-100 text-rose-400 hover:bg-rose-50 hover:text-rose-600 dark:border-rose-900/50 dark:hover:bg-rose-900/20">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"></path>
                </svg>
            </button>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('parkingModal');
    const openCreateModal = document.getElementById('openCreateModal');
    const closeModalButtons = [document.getElementById('closeModal'), document.getElementById('cancelModal')];
    const addPenggunaButton = document.getElementById('addPenggunaButton');
    const penggunaContainer = document.getElementById('penggunaContainer');
    const template = document.getElementById('penggunaTemplate');
    const parkingForm = document.getElementById('parkingForm');
    const modalTitle = document.getElementById('modalTitle');
    const namaLahan = document.getElementById('namaLahan');
    const kapasitasInput = document.getElementById('kapasitasInput');
    const formMethod = document.getElementById('formMethod');
    const searchInput = document.getElementById('searchInput');

    function toggleModal(show) {
        modal.classList.toggle('hidden', !show);
        document.body.style.overflow = show ? 'hidden' : '';
    }

    function resetFormForCreate() {
        parkingForm.reset();
        modalTitle.innerText = 'Tambah Lahan Baru';
        formMethod.value = 'POST';
        parkingForm.action = "{{ route('LahanParkir.store') }}";
        penggunaContainer.innerHTML = '';
        addPenggunaRow();
    }

    function reindexPenggunaRows() {
        const items = penggunaContainer.querySelectorAll('.pengguna-item');

        items.forEach((item, index) => {
            item.querySelector('.jenis-pemilik-select').name = `pengguna_parkir[${index}][jenis_pemilik_id]`;
            item.querySelector('.jenis-kendaraan-select').name = `pengguna_parkir[${index}][jenis_kendaraan_id]`;
            item.querySelector('.pengguna-id-input').name = `pengguna_parkir[${index}][id]`;
        });
    }

    function addPenggunaRow(data = null) {
        const clone = template.content.cloneNode(true);
        const wrapper = clone.querySelector('.pengguna-item');
        const pemilikSelect = wrapper.querySelector('.jenis-pemilik-select');
        const kendaraanSelect = wrapper.querySelector('.jenis-kendaraan-select');
        const penggunaIdInput = wrapper.querySelector('.pengguna-id-input');
        const removeButton = wrapper.querySelector('.removePenggunaButton');

        if (data) {
            pemilikSelect.value = data.jenis_pemilik_id ?? '';
            kendaraanSelect.value = data.jenis_kendaraan_id ?? '';
            penggunaIdInput.value = data.id ?? '';
        }

        removeButton.addEventListener('click', function () {
            wrapper.remove();
            reindexPenggunaRows();
        });

        penggunaContainer.appendChild(wrapper);
        reindexPenggunaRows();
    }

    function openEditModal(button) {
        const data = button.dataset;
        modalTitle.innerText = `Edit Lahan: ${data.nama_lahan}`;
        namaLahan.value = data.nama_lahan || '';
        kapasitasInput.value = data.kapasitas || '';
        formMethod.value = 'PUT';
        parkingForm.action = `/LahanParkir/${data.id}`;
        penggunaContainer.innerHTML = '';

        let pengguna = [];
        try {
            pengguna = JSON.parse(data.pengguna || '[]');
        } catch (error) {
            pengguna = [];
        }

        if (pengguna.length > 0) {
            pengguna.forEach(item => addPenggunaRow(item));
        } else {
            addPenggunaRow();
        }

        toggleModal(true);
    }

    function bindEditButtons() {
        document.querySelectorAll('.editButton').forEach(button => {
            button.addEventListener('click', function () {
                openEditModal(this);
            });
        });
    }

    function applySearch() {
        const keyword = searchInput.value.toLowerCase();

        document.querySelectorAll('.parking-row').forEach(row => {
            row.style.display = row.dataset.search.includes(keyword) ? '' : 'none';
        });

        document.querySelectorAll('.parking-card').forEach(card => {
            card.style.display = card.dataset.search.includes(keyword) ? '' : 'none';
        });
    }

    openCreateModal.addEventListener('click', function () {
        resetFormForCreate();
        toggleModal(true);
    });

    closeModalButtons.forEach(button => {
        button.addEventListener('click', function () {
            toggleModal(false);
        });
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            toggleModal(false);
        }
    });

    addPenggunaButton.addEventListener('click', function () {
        addPenggunaRow();
    });

    searchInput.addEventListener('input', applySearch);

    bindEditButtons();
});
</script>
@endsection
