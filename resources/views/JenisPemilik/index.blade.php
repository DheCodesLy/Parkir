@extends('layouts.app')

@section('title', 'Jenis Pemilik')

@section('content')
<div x-data="jenisPemilikPage()" x-cloak class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Jenis Pemilik</h1>
                <p class="mt-2 text-sm text-slate-500">Kelola data jenis pemilik secara rapi, cepat, dan profesional.</p>
            </div>

            <button
                type="button"
                @click="openCreateModal()"
                class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-300"
            >
                + Tambah Data
            </button>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <div class="mb-2 font-semibold">Terjadi kesalahan:</div>
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Daftar Jenis Pemilik</h2>
                        <p class="text-sm text-slate-500">Total data: {{ $jenisPemiliks->count() }}</p>
                    </div>
                </div>
            </div>

            @if ($jenisPemiliks->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Nama Jenis Pemilik</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Kode</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Deskripsi</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($jenisPemiliks as $index => $item)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-700">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-semibold text-slate-900">
                                            {{ $item->nama_jenis_pemilik }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex rounded-xl bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                            {{ $item->kode_jenis_pemilik }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ $item->deskripsi ?: '-' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($item->status_aktif)
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-200">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                data-id="{{ $item->id }}"
                                                data-nama="{{ $item->nama_jenis_pemilik }}"
                                                data-deskripsi="{{ $item->deskripsi }}"
                                                data-status="{{ (int) $item->status_aktif }}"
                                                data-update-url="{{ route('jenis-pemilik.update', $item->id) }}"
                                                @click="openEditModal($el)"
                                                class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100"
                                            >
                                                Edit
                                            </button>

                                            <button
                                                type="button"
                                                data-nama="{{ $item->nama_jenis_pemilik }}"
                                                data-delete-url="{{ route('jenis-pemilik.destroy', $item->id) }}"
                                                @click="openDeleteModal($el)"
                                                class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900">Belum ada data</h3>
                    <p class="mt-2 text-sm text-slate-500">Tambahkan jenis pemilik pertama untuk mulai mengelola data.</p>
                    <button
                        type="button"
                        @click="openCreateModal()"
                        class="mt-6 inline-flex items-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        Tambah Data
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div
        x-show="createModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4 py-6"
        style="display: none;"
    >
        <div @click.outside="closeAllModal()" class="w-full max-w-2xl rounded-3xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Tambah Jenis Pemilik</h3>
                    <p class="mt-1 text-sm text-slate-500">Masukkan data baru dengan lengkap.</p>
                </div>
                <button type="button" @click="closeAllModal()" class="text-slate-400 transition hover:text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('jenis-pemilik.store') }}" method="POST" class="space-y-5 px-6 py-6">
                @csrf

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Jenis Pemilik</label>
                    <input
                        type="text"
                        name="nama_jenis_pemilik"
                        value="{{ old('nama_jenis_pemilik') }}"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                        placeholder="Contoh: Perorangan"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
                    <textarea
                        name="deskripsi"
                        rows="4"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                        placeholder="Tulis deskripsi singkat..."
                    >{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
                    <select
                        name="status_aktif"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    >
                        <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="button"
                        @click="closeAllModal()"
                        class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div
        x-show="editModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4 py-6"
        style="display: none;"
    >
        <div @click.outside="closeAllModal()" class="w-full max-w-2xl rounded-3xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Edit Jenis Pemilik</h3>
                    <p class="mt-1 text-sm text-slate-500">Perbarui data yang dipilih.</p>
                </div>
                <button type="button" @click="closeAllModal()" class="text-slate-400 transition hover:text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form :action="editForm.update_url" method="POST" class="space-y-5 px-6 py-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Jenis Pemilik</label>
                    <input
                        type="text"
                        name="nama_jenis_pemilik"
                        x-model="editForm.nama_jenis_pemilik"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                        placeholder="Contoh: Perorangan"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
                    <textarea
                        name="deskripsi"
                        rows="4"
                        x-model="editForm.deskripsi"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                        placeholder="Tulis deskripsi singkat..."
                    ></textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
                    <select
                        name="status_aktif"
                        x-model="editForm.status_aktif"
                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    >
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="button"
                        @click="closeAllModal()"
                        class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div
        x-show="deleteModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4 py-6"
        style="display: none;"
    >
        <div @click.outside="closeAllModal()" class="w-full max-w-md rounded-3xl bg-white shadow-2xl">
            <div class="px-6 py-6">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                    </svg>
                </div>

                <h3 class="text-center text-xl font-bold text-slate-900">Hapus Data</h3>
                <p class="mt-3 text-center text-sm leading-6 text-slate-500">
                    Data
                    <span class="font-semibold text-slate-800" x-text="deleteForm.nama_jenis_pemilik"></span>
                    akan dihapus permanen.
                </p>

                <form :action="deleteForm.delete_url" method="POST" class="mt-6 flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')

                    <button
                        type="button"
                        @click="closeAllModal()"
                        class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700"
                    >
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<script>
    function jenisPemilikPage() {
        return {
            createModal: false,
            editModal: false,
            deleteModal: false,

            editForm: {
                id: '',
                nama_jenis_pemilik: '',
                deskripsi: '',
                status_aktif: '1',
                update_url: ''
            },

            deleteForm: {
                nama_jenis_pemilik: '',
                delete_url: ''
            },

            openCreateModal() {
                this.closeAllModal()
                this.createModal = true
            },

            openEditModal(element) {
                this.closeAllModal()

                this.editForm = {
                    id: element.dataset.id || '',
                    nama_jenis_pemilik: element.dataset.nama || '',
                    deskripsi: element.dataset.deskripsi || '',
                    status_aktif: String(element.dataset.status || '1'),
                    update_url: element.dataset.updateUrl || ''
                }

                this.editModal = true
            },

            openDeleteModal(element) {
                this.closeAllModal()

                this.deleteForm = {
                    nama_jenis_pemilik: element.dataset.nama || '',
                    delete_url: element.dataset.deleteUrl || ''
                }

                this.deleteModal = true
            },

            closeAllModal() {
                this.createModal = false
                this.editModal = false
                this.deleteModal = false
            }
        }
    }
</script>
@endsection
