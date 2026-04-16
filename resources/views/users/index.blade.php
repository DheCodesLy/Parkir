@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
@php
    $oldEditPayload = null;

    if (old('form_type') === 'edit' && old('edit_user_id')) {
        $oldRoleId = old('role_id', data_get($modalData, 'selected_role_id'));
        $oldRole = $roles->firstWhere('id', (int) $oldRoleId);
        $oldHasVehicle = old('tambah_kendaraan') !== null
            ? (bool) old('tambah_kendaraan')
            : (bool) data_get($modalData, 'has_vehicle', false);

        $oldEditPayload = [
            'user' => [
                'id' => (int) old('edit_user_id'),
                'name' => old('name', data_get($modalData, 'user.name')),
                'email' => old('email', data_get($modalData, 'user.email')),
                'alamat' => old('alamat', data_get($modalData, 'user.alamat')),
                'status_aktif' => (bool) data_get($modalData, 'user.status_aktif', true),
            ],
            'selected_role_id' => $oldRoleId,
            'selected_role_nama' => optional($oldRole)->nama_role,
            'has_vehicle' => $oldHasVehicle,
            'kendaraan' => $oldHasVehicle ? [
                'no_polisi' => old('no_polisi', data_get($modalData, 'kendaraan.no_polisi')),
                'jenis_kendaraan_id' => old('jenis_kendaraan_id', data_get($modalData, 'kendaraan.jenis_kendaraan_id')),
                'jenis_kendaraan_nama' => data_get($modalData, 'kendaraan.jenis_kendaraan_nama'),
                'merk' => old('merk', data_get($modalData, 'kendaraan.merk')),
                'warna' => old('warna', data_get($modalData, 'kendaraan.warna')),
                'catatan' => old('catatan', data_get($modalData, 'kendaraan.catatan')),
                'status_aktif' => (bool) data_get($modalData, 'kendaraan.status_aktif', true),
            ] : null,
        ];
    }

    $serverEditUrl = isset($modalData['user']['id']) ? route('users.update', $modalData['user']['id']) : '';
    $oldEditUrl = old('edit_user_id') ? route('users.update', old('edit_user_id')) : '';
@endphp

<div
    x-data="userPage({
        serverModalType: @js($modalType),
        serverModalData: @js($modalData),
        serverEditUrl: @js($serverEditUrl),
        oldEditPayload: @js($oldEditPayload),
        oldEditUrl: @js($oldEditUrl),
        initialCreateVehicle: @js((bool) old('tambah_kendaraan'))
    })"
    x-init="init()"
    x-cloak
    @keydown.escape.window="closeAllModal()"
    class="space-y-6"
>
    {{-- Page header --}}
    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-primary-600 dark:text-primary-400">
                    Administrasi
                </p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                    Manajemen User
                </h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 dark:text-slate-400">
                    Kelola akun user, role sistem, dan keterkaitan kendaraan parkir dalam satu halaman yang terstruktur.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="inline-flex items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300">
                    Total User:
                    <span class="ml-2 font-bold text-slate-900 dark:text-white">{{ $users->total() }}</span>
                </div>

                <button
                    type="button"
                    @click="openCreateModal()"
                    class="inline-flex h-11 items-center justify-center rounded-2xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah User
                </button>
            </div>
        </div>
    </section>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-300">
            <div class="mb-2 font-semibold">Terjadi kesalahan:</div>
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Table section --}}
    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Daftar User</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Data akun dan status kendaraan pengguna sistem.
                </p>
            </div>
        </div>

        @if ($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 dark:bg-slate-950/50">
                        <tr class="border-b border-slate-200 dark:border-slate-800">
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">No</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">User</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Role</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Kendaraan</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach ($users as $index => $user)
                            @php
                                $role = $user->UserRole->first();
                                $pemilik = $user->PemilikKendaraan;
                                $kendaraan = optional($pemilik)->Kendaraan;

                                $payload = [
                                    'user' => [
                                        'id' => $user->id,
                                        'name' => $user->name,
                                        'email' => $user->email,
                                        'alamat' => $user->alamat,
                                        'status_aktif' => (bool) $user->status_aktif,
                                    ],
                                    'selected_role_id' => optional($role)->role_id,
                                    'selected_role_nama' => optional(optional($role)->Role)->nama_role,
                                    'has_vehicle' => (bool) ($kendaraan && $kendaraan->status_aktif),
                                    'pemilik' => $pemilik ? [
                                        'id' => $pemilik->id,
                                        'status_aktif' => (bool) $pemilik->status_aktif,
                                    ] : null,
                                    'kendaraan' => $kendaraan ? [
                                        'id' => $kendaraan->id,
                                        'no_polisi' => $kendaraan->no_polisi,
                                        'jenis_kendaraan_id' => $kendaraan->jenis_kendaraan_id,
                                        'jenis_kendaraan_nama' => optional($kendaraan->JenisKendaraan)->nama_jenis_kendaraan,
                                        'merk' => $kendaraan->merk,
                                        'warna' => $kendaraan->warna,
                                        'catatan' => $kendaraan->catatan,
                                    ] : null,
                                    'update_url' => route('users.update', $user->id),
                                    'delete_url' => route('users.destroy', $user->id),
                                ];

                                $rowNumber = ($users->currentPage() - 1) * $users->perPage() + $index + 1;
                            @endphp

                            <tr class="align-top transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40 {{ !$user->status_aktif ? 'opacity-70' : '' }}">
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">
                                    {{ $rowNumber }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="space-y-1">
                                        <div class="font-semibold text-slate-900 dark:text-white">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400">
                                            {{ $user->email }}
                                        </div>
                                        <div class="max-w-xs truncate text-xs text-slate-400 dark:text-slate-500">
                                            {{ $user->alamat ?: 'Tidak ada alamat' }}
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center rounded-xl border border-primary-200 bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700 dark:border-primary-900/40 dark:bg-primary-900/20 dark:text-primary-300">
                                        {{ optional(optional($role)->Role)->nama_role ?: '-' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    @if ($kendaraan && $kendaraan->status_aktif)
                                        <div class="space-y-1">
                                            <div class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                                                {{ $kendaraan->no_polisi }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ optional($kendaraan->JenisKendaraan)->nama_jenis_kendaraan ?: '-' }}
                                                @if ($kendaraan->merk)
                                                    • {{ $kendaraan->merk }}
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm italic text-slate-400 dark:text-slate-500">Tidak ada kendaraan</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($user->status_aktif)
                                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-950/40 dark:text-emerald-400">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 dark:border-rose-900/40 dark:bg-rose-950/40 dark:text-rose-400">
                                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <button
                                            type="button"
                                            @click="openShowModal(@js($payload))"
                                            class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                        >
                                            Detail
                                        </button>

                                        <button
                                            type="button"
                                            @click="openEditModal(@js($payload))"
                                            class="inline-flex items-center rounded-xl border border-primary-200 bg-primary-50 px-3 py-2 text-sm font-medium text-primary-700 transition hover:bg-primary-100 dark:border-primary-900/40 dark:bg-primary-900/20 dark:text-primary-300 dark:hover:bg-primary-900/30"
                                        >
                                            Edit
                                        </button>

                                        @if ($user->status_aktif)
                                            <button
                                                type="button"
                                                @click="openDeactivateModal(@js($payload))"
                                                class="inline-flex items-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-100 dark:border-rose-900/40 dark:bg-rose-950/40 dark:text-rose-400 dark:hover:bg-rose-900/50"
                                            >
                                                Cabut
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Menampilkan
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $users->firstItem() }}</span>
                        -
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $users->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $users->total() }}</span>
                        data
                    </p>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="px-6 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 dark:bg-primary-900/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-primary-500 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m8-10a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-bold text-slate-900 dark:text-white">Belum ada data user</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Tambahkan user pertama untuk mulai mengelola akun dan relasi kendaraan.
                </p>
                <button
                    type="button"
                    @click="openCreateModal()"
                    class="mt-6 inline-flex h-11 items-center justify-center rounded-2xl bg-primary-600 px-5 text-sm font-semibold text-white transition hover:bg-primary-700"
                >
                    Tambah User
                </button>
            </div>
        @endif
    </section>

    {{-- Create Modal --}}
    <div
        x-show="createModal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
    >
        <div
            @click.outside="closeAllModal()"
            class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Tambah User Baru</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Masukkan informasi akun dan data kendaraan bila diperlukan.
                    </p>
                </div>
                <button type="button" @click="closeAllModal()" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('users.store') }}" method="POST" class="flex-1 overflow-y-auto px-6 py-6">
                @csrf
                <input type="hidden" name="form_type" value="create">

                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama user"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Password</label>
                            <input type="password" name="password" placeholder="Minimal 8 karakter"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Role Sistem</label>
                            <select name="role_id"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="">Pilih role...</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->nama_role }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" placeholder="Masukkan alamat lengkap"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/60">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-base font-semibold text-slate-900 dark:text-white">Registrasi Kendaraan</h4>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    Aktifkan jika user juga didaftarkan sebagai pemilik kendaraan.
                                </p>
                            </div>

                            <label class="inline-flex cursor-pointer items-center gap-3">
                                <input type="hidden" name="tambah_kendaraan" value="0">
                                <input type="checkbox" name="tambah_kendaraan" value="1" x-model="createForm.tambah_kendaraan" class="peer sr-only">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Hubungkan</span>
                                <span class="relative h-7 w-12 rounded-full transition-colors"
                                    :class="createForm.tambah_kendaraan ? 'bg-primary-600' : 'bg-slate-300 dark:bg-slate-700'">
                                    <span class="absolute top-1 h-5 w-5 rounded-full bg-white shadow transition-all"
                                        :class="createForm.tambah_kendaraan ? 'left-6' : 'left-1'"></span>
                                </span>
                            </label>
                        </div>

                        <div x-show="createForm.tambah_kendaraan" x-collapse>
                            <div class="mt-6 grid grid-cols-1 gap-5 border-t border-slate-200 pt-6 dark:border-slate-800 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Nomor Polisi</label>
                                    <input type="text" name="no_polisi" value="{{ old('no_polisi') }}" placeholder="B 1234 XYZ"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold uppercase tracking-wider text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Jenis Kendaraan</label>
                                    <select name="jenis_kendaraan_id"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                        <option value="">Pilih jenis kendaraan...</option>
                                        @foreach ($jenisKendaraans as $jenis)
                                            <option value="{{ $jenis->id }}" {{ old('jenis_kendaraan_id') == $jenis->id ? 'selected' : '' }}>
                                                {{ $jenis->nama_jenis_kendaraan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Merk / Model</label>
                                    <input type="text" name="merk" value="{{ old('merk') }}" placeholder="Honda Vario"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Warna</label>
                                    <input type="text" name="warna" value="{{ old('warna') }}" placeholder="Hitam"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Catatan</label>
                                    <textarea name="catatan" rows="2" placeholder="Catatan tambahan..."
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('catatan') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-200 pt-5 dark:border-slate-800">
                    <button type="button" @click="closeAllModal()" class="inline-flex h-11 items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-2xl bg-primary-600 px-6 text-sm font-semibold text-white transition hover:bg-primary-700">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div
        x-show="editModal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
    >
        <div
            @click.outside="closeAllModal()"
            class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Edit Data User</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Perbarui data akun, role, dan relasi kendaraan.
                    </p>
                </div>
                <button type="button" @click="closeAllModal()" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form :action="editForm.update_url" method="POST" class="flex-1 overflow-y-auto px-6 py-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="edit_user_id" :value="editForm.id">

                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Nama Lengkap</label>
                            <input type="text" name="name" x-model="editForm.name"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Email</label>
                            <input type="email" name="email" x-model="editForm.email"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Reset Password</label>
                            <input type="password" name="password" x-model="editForm.password" placeholder="Biarkan kosong bila tidak diubah"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Role Sistem</label>
                            <select name="role_id" x-model="editForm.role_id"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="">Pilih role...</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->nama_role }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" x-model="editForm.alamat"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/60">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-base font-semibold text-slate-900 dark:text-white">Status Kendaraan</h4>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    Nonaktifkan bila user tidak lagi terkait dengan kendaraan aktif.
                                </p>
                            </div>

                            <label class="inline-flex cursor-pointer items-center gap-3">
                                <input type="hidden" name="tambah_kendaraan" value="0">
                                <input type="checkbox" name="tambah_kendaraan" value="1" x-model="editForm.tambah_kendaraan" class="peer sr-only">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Aktifkan Kendaraan</span>
                                <span class="relative h-7 w-12 rounded-full transition-colors"
                                    :class="editForm.tambah_kendaraan ? 'bg-primary-600' : 'bg-slate-300 dark:bg-slate-700'">
                                    <span class="absolute top-1 h-5 w-5 rounded-full bg-white shadow transition-all"
                                        :class="editForm.tambah_kendaraan ? 'left-6' : 'left-1'"></span>
                                </span>
                            </label>
                        </div>

                        <div x-show="editForm.tambah_kendaraan" x-collapse>
                            <div class="mt-6 grid grid-cols-1 gap-5 border-t border-slate-200 pt-6 dark:border-slate-800 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Nomor Polisi</label>
                                    <input type="text" name="no_polisi" x-model="editForm.no_polisi"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold uppercase tracking-wider text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Jenis Kendaraan</label>
                                    <select name="jenis_kendaraan_id" x-model="editForm.jenis_kendaraan_id"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                        <option value="">Pilih jenis...</option>
                                        @foreach ($jenisKendaraans as $jenis)
                                            <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis_kendaraan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Merk / Model</label>
                                    <input type="text" name="merk" x-model="editForm.merk"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Warna</label>
                                    <input type="text" name="warna" x-model="editForm.warna"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-200">Catatan</label>
                                    <textarea name="catatan" rows="2" x-model="editForm.catatan"
                                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-200 pt-5 dark:border-slate-800">
                    <button type="button" @click="closeAllModal()" class="inline-flex h-11 items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-2xl bg-primary-600 px-6 text-sm font-semibold text-white transition hover:bg-primary-700">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Show Modal --}}
    <div
        x-show="showModal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
    >
        <div
            @click.outside="closeAllModal()"
            class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Detail User</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Informasi identitas, akses, dan kendaraan terkait.
                    </p>
                </div>
                <button type="button" @click="closeAllModal()" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto px-6 py-6">
                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-950/50">
                        <h4 class="mb-5 text-xs font-bold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">
                            Informasi Akun
                        </h4>

                        <div class="space-y-4">
                            <div>
                                <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Nama</div>
                                <div class="mt-1 text-base font-semibold text-slate-900 dark:text-white" x-text="showData.user?.name || '-'"></div>
                            </div>

                            <div>
                                <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Email</div>
                                <div class="mt-1 text-sm text-slate-700 dark:text-slate-300" x-text="showData.user?.email || '-'"></div>
                            </div>

                            <div>
                                <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Role</div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center rounded-xl border border-primary-200 bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700 dark:border-primary-900/40 dark:bg-primary-900/20 dark:text-primary-300" x-text="showData.selected_role_nama || '-'"></span>
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</div>
                                <div class="mt-2">
                                    <template x-if="showData.user?.status_aktif">
                                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-950/40 dark:text-emerald-400">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    </template>
                                    <template x-if="!showData.user?.status_aktif">
                                        <span class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 dark:border-rose-900/40 dark:bg-rose-950/40 dark:text-rose-400">
                                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                            Nonaktif
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Alamat</div>
                                <div class="mt-1 whitespace-pre-line text-sm text-slate-700 dark:text-slate-300" x-text="showData.user?.alamat || '-'"></div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-950/50">
                        <h4 class="mb-5 text-xs font-bold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">
                            Informasi Kendaraan
                        </h4>

                        <template x-if="showData.has_vehicle && showData.kendaraan">
                            <div class="space-y-4">
                                <div>
                                    <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Nomor Polisi</div>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-1.5 text-sm font-bold uppercase tracking-wider text-slate-900 shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white" x-text="showData.kendaraan?.no_polisi || '-'"></span>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Jenis</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white" x-text="showData.kendaraan?.jenis_kendaraan_nama || '-'"></div>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Merk / Model</div>
                                        <div class="mt-1 text-sm text-slate-700 dark:text-slate-300" x-text="showData.kendaraan?.merk || '-'"></div>
                                    </div>

                                    <div>
                                        <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Warna</div>
                                        <div class="mt-1 text-sm text-slate-700 dark:text-slate-300" x-text="showData.kendaraan?.warna || '-'"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Catatan</div>
                                    <div class="mt-1 whitespace-pre-line text-sm text-slate-700 dark:text-slate-300" x-text="showData.kendaraan?.catatan || '-'"></div>
                                </div>
                            </div>
                        </template>

                        <template x-if="!showData.has_vehicle || !showData.kendaraan">
                            <div class="flex min-h-[220px] flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white text-center dark:border-slate-700 dark:bg-slate-900/50">
                                <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9.75 17.25h4.5m-8.25-6h12.75m-14.25 0 .621-3.106A2.25 2.25 0 0 1 7.58 6.75h8.84a2.25 2.25 0 0 1 2.209 1.394L19.25 11.25m-13.5 0v4.5A1.5 1.5 0 0 0 7.25 17.25h9.5a1.5 1.5 0 0 0 1.5-1.5v-4.5" />
                                    </svg>
                                </div>
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white">Tidak ada data kendaraan</h4>
                                <p class="mt-1 max-w-[220px] text-xs text-slate-500 dark:text-slate-400">
                                    User ini belum memiliki kendaraan aktif yang terhubung.
                                </p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-200 pt-5 dark:border-slate-800">
                    <button type="button" @click="closeAllModal()" class="inline-flex h-11 items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        Tutup
                    </button>
                    <button type="button" @click="openEditFromShow()" class="inline-flex h-11 items-center justify-center rounded-2xl bg-primary-600 px-6 text-sm font-semibold text-white transition hover:bg-primary-700">
                        Edit User
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Deactivate Modal --}}
    <div
        x-show="deactivateModal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
    >
        <div
            @click.outside="closeAllModal()"
            class="w-full max-w-md rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="px-6 py-8 text-center">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Cabut akses user?</h3>
                <p class="mt-3 text-sm leading-6 text-slate-500 dark:text-slate-400">
                    Akun
                    <span class="font-semibold text-slate-900 dark:text-white" x-text="deactivateForm.name"></span>
                    akan dinonaktifkan dari sistem beserta relasi kendaraan aktifnya.
                </p>

                <form :action="deactivateForm.delete_url" method="POST" class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    @csrf
                    @method('DELETE')

                    <button type="button" @click="closeAllModal()" class="inline-flex h-11 w-full items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 sm:w-auto">
                        Batal
                    </button>

                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-2xl bg-rose-600 px-6 text-sm font-semibold text-white transition hover:bg-rose-700 sm:w-auto">
                        Ya, Cabut Akses
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    function userPage(config) {
        return {
            createModal: false,
            editModal: false,
            showModal: false,
            deactivateModal: false,

            serverModalType: config.serverModalType || null,
            serverModalData: config.serverModalData || null,
            serverEditUrl: config.serverEditUrl || '',
            oldEditPayload: config.oldEditPayload || null,
            oldEditUrl: config.oldEditUrl || '',
            initialCreateVehicle: !!config.initialCreateVehicle,

            createForm: {
                tambah_kendaraan: false,
            },

            editForm: {
                id: '',
                name: '',
                email: '',
                password: '',
                alamat: '',
                role_id: '',
                tambah_kendaraan: false,
                no_polisi: '',
                jenis_kendaraan_id: '',
                merk: '',
                warna: '',
                catatan: '',
                update_url: ''
            },

            showData: {
                user: null,
                selected_role_id: '',
                selected_role_nama: '',
                has_vehicle: false,
                pemilik: null,
                kendaraan: null,
                update_url: '',
                delete_url: ''
            },

            deactivateForm: {
                name: '',
                delete_url: ''
            },

            init() {
                this.createForm.tambah_kendaraan = this.initialCreateVehicle;

                if (this.oldEditPayload) {
                    this.openEditModal({ ...this.oldEditPayload, update_url: this.oldEditUrl });
                    return;
                }

                if (this.serverModalType === 'edit' && this.serverModalData) {
                    this.openEditModal({ ...this.serverModalData, update_url: this.serverEditUrl });
                    this.clearModalQuery();
                    return;
                }

                if (this.serverModalType === 'show' && this.serverModalData) {
                    this.openShowModal(this.serverModalData);
                    this.clearModalQuery();
                    return;
                }

                if (this.serverModalType === 'create') {
                    this.openCreateModal();
                    this.clearModalQuery();
                }
            },

            clearModalQuery() {
                const url = new URL(window.location.href);
                url.searchParams.delete('modal');
                url.searchParams.delete('selected_user');
                window.history.replaceState({}, document.title, url.toString());
            },

            normalizePayload(payload = {}) {
                return {
                    user: {
                        id: payload.user?.id ?? '',
                        name: payload.user?.name ?? '',
                        email: payload.user?.email ?? '',
                        alamat: payload.user?.alamat ?? '',
                        status_aktif: payload.user?.status_aktif ?? true
                    },
                    selected_role_id: payload.selected_role_id ?? '',
                    selected_role_nama: payload.selected_role_nama ?? '-',
                    has_vehicle: !!payload.has_vehicle,
                    pemilik: payload.pemilik ?? null,
                    kendaraan: payload.kendaraan ? {
                        id: payload.kendaraan.id ?? '',
                        pemilik_id: payload.kendaraan.pemilik_id ?? '',
                        no_polisi: payload.kendaraan.no_polisi ?? '',
                        jenis_kendaraan_id: payload.kendaraan.jenis_kendaraan_id ?? '',
                        jenis_kendaraan_nama: payload.kendaraan.jenis_kendaraan_nama ?? '',
                        merk: payload.kendaraan.merk ?? '',
                        warna: payload.kendaraan.warna ?? '',
                        catatan: payload.kendaraan.catatan ?? '',
                        status_aktif: payload.kendaraan.status_aktif ?? true
                    } : null,
                    update_url: payload.update_url ?? '',
                    delete_url: payload.delete_url ?? ''
                };
            },

            openCreateModal() {
                this.closeAllModal();
                this.createModal = true;
                document.body.style.overflow = 'hidden';
            },

            openShowModal(payload) {
                this.closeAllModal();
                this.showData = this.normalizePayload(payload);
                this.showModal = true;
                document.body.style.overflow = 'hidden';
            },

            openEditModal(payload) {
                this.closeAllModal();
                const data = this.normalizePayload(payload);

                this.editForm = {
                    id: data.user.id ?? '',
                    name: data.user.name ?? '',
                    email: data.user.email ?? '',
                    password: '',
                    alamat: data.user.alamat ?? '',
                    role_id: data.selected_role_id ? String(data.selected_role_id) : '',
                    tambah_kendaraan: !!data.has_vehicle,
                    no_polisi: data.kendaraan?.no_polisi ?? '',
                    jenis_kendaraan_id: data.kendaraan?.jenis_kendaraan_id ? String(data.kendaraan.jenis_kendaraan_id) : '',
                    merk: data.kendaraan?.merk ?? '',
                    warna: data.kendaraan?.warna ?? '',
                    catatan: data.kendaraan?.catatan ?? '',
                    update_url: data.update_url ?? ''
                };

                this.editModal = true;
                document.body.style.overflow = 'hidden';
            },

            openEditFromShow() {
                this.openEditModal({
                    ...this.showData,
                    update_url: this.showData.update_url || ''
                });
            },

            openDeactivateModal(payload) {
                const data = this.normalizePayload(payload);
                this.closeAllModal();
                this.deactivateForm = {
                    name: data.user.name ?? '',
                    delete_url: data.delete_url ?? ''
                };
                this.deactivateModal = true;
                document.body.style.overflow = 'hidden';
            },

            closeAllModal() {
                this.createModal = false;
                this.editModal = false;
                this.showModal = false;
                this.deactivateModal = false;
                document.body.style.overflow = '';
            }
        }
    }
</script>
@endsection
