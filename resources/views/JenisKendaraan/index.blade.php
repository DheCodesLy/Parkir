@extends('layouts.app')

@section('content')
@php
    $totalJenis = $jenisKendaraans->count();
    $totalAktif = $jenisKendaraans->where('status_aktif', 1)->count();
    $totalNonAktif = $totalJenis - $totalAktif;
@endphp

<div class="p-4 sm:p-6 lg:p-8 transition-colors duration-300 min-h-screen bg-slate-50/50">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <nav class="mb-2 flex flex-wrap gap-2 text-xs text-slate-500">
                <span>Dashboard</span>
                <span>/</span>
                <span class="font-medium text-primary-600">Master Data</span>
                <span>/</span>
                <span class="font-medium text-primary-600">Jenis Kendaraan</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Vehicle <span class="text-primary-600">Inventory</span>
            </h1>
        </div>

        <button
            type="button"
            onclick="openCreateModal()"
            class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-primary-600 px-4 sm:px-6 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-primary-700 active:scale-95"
        >
            <svg class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
            </svg>
            Tambah Jenis Baru
        </button>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Jenis</p>
            <p class="mt-2 text-3xl sm:text-4xl font-black text-slate-900">{{ $totalJenis }}</p>
        </div>

        <div class="rounded-2xl bg-primary-600 p-5 shadow-lg transition-colors">
            <p class="text-xs font-bold uppercase tracking-wider text-primary-100">Status Aktif</p>
            <div class="mt-2 flex items-center gap-3">
                <p class="text-3xl sm:text-4xl font-black text-white">{{ $totalAktif }}</p>
                <span class="rounded-full bg-emerald-400/20 px-2.5 py-0.5 text-[10px] font-bold text-emerald-100 uppercase tracking-widest">Online</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Non-Aktif</p>
            <p class="mt-2 text-3xl sm:text-4xl font-black text-slate-900">{{ $totalNonAktif }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl transition-colors">
        <div class="flex flex-col gap-4 border-b border-slate-100 bg-slate-50/50 px-4 py-4 sm:px-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-800">Daftar Inventaris Kendaraan</h3>
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                        <th class="px-6 py-4 text-center w-16">No</th>
                        <th class="px-6 py-4">Informasi Jenis</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($jenisKendaraans as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-5 text-center text-xs font-bold text-slate-400">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900">{{ $item->nama_jenis_kendaraan }}</div>
                                <div class="text-[10px] font-bold text-primary-500 uppercase tracking-tighter">ID: {{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-xs text-slate-500 line-clamp-1 max-w-xs">{{ $item->deskripsi ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-5 text-xs">
                                @if($item->status_aktif)
                                    <span class="inline-flex items-center rounded-lg border border-emerald-100 bg-emerald-50 px-2.5 py-1 font-bold text-emerald-600 uppercase tracking-tight">Aktif</span>
                                @else
                                    <span class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1 font-bold text-slate-500 uppercase tracking-tight">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <button
                                        onclick="openEditModal({{ json_encode($item) }})"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-primary-600 transition"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('jenis-kendaraan.destroy', $item->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-100 text-rose-400 hover:bg-rose-50 hover:text-rose-600 shadow-sm" onclick="return confirm('Hapus data?')">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center font-medium text-slate-400">Belum ada data tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="mainModal" class="fixed inset-0 z-[100] hidden overflow-y-auto px-3 py-4 sm:px-4 sm:py-8">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal()"></div>

    <div class="relative mx-auto w-full max-w-2xl rounded-3xl bg-white shadow-2xl transition-all">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
            <h2 id="modalTitle" class="text-xl font-black text-slate-900">Konfigurasi Jenis</h2>
            <button onclick="closeModal()" class="text-slate-400 transition hover:text-slate-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="mainForm" method="POST" action="" class="p-6">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">

            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Identitas Jenis</label>
                        <input type="text" id="nama_input" name="nama_jenis_kendaraan" class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-50 transition" placeholder="Misal: Sedan Mewah" required>
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Status Aktif</label>
                        <select id="status_input" name="status_aktif" class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none focus:border-primary-500 transition">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="rounded-2xl border border-dashed border-slate-100 bg-slate-50 p-4">
                    <p class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Petunjuk</p>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-primary-500"></span>
                            Nama akan otomatis dikonversi menjadi kode slug unik.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-primary-500"></span>
                            Status nonaktif akan menyembunyikan jenis dari pilihan.
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mb-6">
                <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Deskripsi Tambahan</label>
                <textarea id="deskripsi_input" name="deskripsi" rows="3" class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 outline-none focus:border-primary-500 transition" placeholder="Berikan keterangan singkat..."></textarea>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeModal()" class="w-full sm:w-auto rounded-xl border border-slate-200 px-6 py-3 text-sm font-bold text-slate-500 transition hover:bg-slate-50">Batal</button>
                <button type="submit" class="w-full sm:w-auto rounded-xl bg-slate-900 px-8 py-3 text-sm font-bold text-white transition hover:bg-slate-800 shadow-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('mainModal');
    const mainForm = document.getElementById('mainForm');
    const formMethod = document.getElementById('formMethod');
    const modalTitle = document.getElementById('modalTitle');

    // Input Fields
    const namaInput = document.getElementById('nama_input');
    const statusInput = document.getElementById('status_input');
    const deskripsiInput = document.getElementById('deskripsi_input');

    function openCreateModal() {
        mainForm.reset();
        modalTitle.innerText = 'Tambah Jenis Baru';
        formMethod.value = 'POST';
        mainForm.action = "{{ route('jenis-kendaraan.store') }}";

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function openEditModal(data) {
        modalTitle.innerText = 'Edit Jenis: ' + data.nama_jenis_kendaraan;
        formMethod.value = 'PUT';
        mainForm.action = `/jenis-kendaraan/${data.id}`;

        // Fill data
        namaInput.value = data.nama_jenis_kendaraan;
        statusInput.value = data.status_aktif;
        deskripsiInput.value = data.deskripsi || '';

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close on Escape
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
</script>

<style>
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
@endsection
