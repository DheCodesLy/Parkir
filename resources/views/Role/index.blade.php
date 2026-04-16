@extends('layouts.app')

@section('content')
@php
    $totalRole = $roles->count();
    $totalDeskripsi = $roles->filter(fn($role) => filled($role->deskripsi))->count();
    $totalTanpaDeskripsi = $totalRole - $totalDeskripsi;

    $rolePayload = $roles->mapWithKeys(function ($role) {
        return [
            $role->id => [
                'id' => $role->id,
                'nama_role' => $role->nama_role,
                'kode_role' => $role->kode_role,
                'deskripsi' => $role->deskripsi,
            ]
        ];
    });
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 dark:border-slate-800 md:flex-row md:items-center md:justify-between">
        <div>
            <nav class="mb-2 flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                <a href="#" class="hover:text-blue-600 dark:hover:text-blue-400">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-300">Manajemen User</span>
                <span>/</span>
                <span class="text-blue-600 dark:text-blue-400">Role</span>
            </nav>

            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Role Management
                </h1>
                <span class="inline-flex items-center rounded-md bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                    {{ $totalRole }} Total
                </span>
            </div>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Kelola data role pengguna dan hak akses sistem.
            </p>
        </div>

        <button
            type="button"
            onclick="openCreateModal()"
            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 w-full sm:w-auto"
        >
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Role
        </button>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50/50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/20 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                Daftar Role Sistem
            </h2>

            @if($totalTanpaDeskripsi > 0)
                <div class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-600 dark:text-amber-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ $totalTanpaDeskripsi }} Role perlu deskripsi
                </div>
            @endif
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-800/40">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="px-6 py-3 text-center w-16">No</th>
                        <th class="px-6 py-3">Informasi Role</th>
                        <th class="px-6 py-3">Deskripsi</th>
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                    @forelse ($roles as $item)
                        <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                            <td class="px-6 py-4 text-center text-sm text-slate-500 dark:text-slate-400">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900 dark:text-white">
                                    {{ $item->nama_role }}
                                </div>
                                <div class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    ID: {{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <p class="max-w-md text-sm text-slate-600 dark:text-slate-300">
                                    {{ $item->deskripsi ?: '-' }}
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $item->kode_role }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        onclick="openEditModal({{ $item->id }})"
                                        class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition"
                                        title="Edit Role"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <form action="{{ route('role.destroy', $item->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            onclick="return confirm('Yakin ingin menghapus role ini?')"
                                            class="text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition"
                                            title="Hapus Role"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                Belum ada data role.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 p-4 md:hidden">
            @forelse ($roles as $item)
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-medium text-slate-900 dark:text-white">{{ $item->nama_role }}</h3>
                            <p class="mt-1 text-xs text-slate-500">{{ $item->kode_role }}</p>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="openEditModal({{ $item->id }})" class="text-slate-400 hover:text-blue-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('role.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus role ini?')" class="text-slate-400 hover:text-rose-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-slate-600 dark:text-slate-300">
                        {{ $item->deskripsi ?: 'Tidak ada deskripsi' }}
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700">
                    Belum ada data role.
                </div>
            @endforelse
        </div>
    </div>
</div>

<div id="roleModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeRoleModal()"></div>

    <div class="relative flex min-h-screen items-center justify-center p-4">
        <div class="w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl dark:bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-800">
                <h2 id="modalTitle" class="text-lg font-semibold text-slate-900 dark:text-white">Tambah Role Baru</h2>
                <button type="button" onclick="closeRoleModal()" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="roleForm" method="POST" action="{{ route('role.store') }}" class="p-6">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">
                <input type="hidden" id="formType" name="form_type" value="{{ old('form_type', 'create') }}">
                <input type="hidden" id="roleIdInput" name="role_id" value="{{ old('role_id') }}">

                <div class="space-y-4">
                    <div>
                        <label for="nama_role" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Role <span class="text-rose-500">*</span></label>
                        <input type="text" id="nama_role" name="nama_role" value="{{ old('nama_role') }}" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm placeholder-slate-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500" required>
                        @error('nama_role') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm placeholder-slate-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeRoleModal()" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">Batal</button>
                    <button type="submit" id="submitButton" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const roleData = @json($rolePayload);

    const roleModal = document.getElementById('roleModal');
    const roleForm = document.getElementById('roleForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitButton = document.getElementById('submitButton');
    const formMethod = document.getElementById('formMethod');
    const formType = document.getElementById('formType');
    const roleIdInput = document.getElementById('roleIdInput');
    const namaRoleInput = document.getElementById('nama_role');
    const deskripsiInput = document.getElementById('deskripsi');

    const updateRouteTemplate = @json(route('role.update', ['role' => '__ROLE_ID__']));

    function buildUpdateRoute(id) {
        return updateRouteTemplate.replace('__ROLE_ID__', id);
    }

    function showModal() {
        roleModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRoleModal() {
        roleModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openCreateModal(preserveOld = false) {
        modalTitle.textContent = 'Tambah Role Baru';
        submitButton.textContent = 'Simpan';
        roleForm.action = "{{ route('role.store') }}";
        formMethod.value = 'POST';
        formType.value = 'create';
        roleIdInput.value = '';

        if (!preserveOld) {
            namaRoleInput.value = '';
            deskripsiInput.value = '';
        }

        showModal();
    }

    function openEditModal(id, preserveOld = false) {
        const data = roleData[id];
        if (!data) return;

        modalTitle.textContent = 'Edit Role';
        submitButton.textContent = 'Update';
        roleForm.action = buildUpdateRoute(data.id);
        formMethod.value = 'PUT';
        formType.value = 'edit';
        roleIdInput.value = data.id;

        if (!preserveOld) {
            namaRoleInput.value = data.nama_role ?? '';
            deskripsiInput.value = data.deskripsi ?? '';
        }

        showModal();
    }

    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeRoleModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->any())
            const oldFormType = @json(old('form_type', 'create'));
            const oldRoleId = @json(old('role_id'));

            if (oldFormType === 'edit' && oldRoleId) {
                modalTitle.textContent = 'Edit Role';
                submitButton.textContent = 'Update';
                roleForm.action = buildUpdateRoute(oldRoleId);
                formMethod.value = 'PUT';
                formType.value = 'edit';
                roleIdInput.value = oldRoleId;
                showModal();
            } else {
                openCreateModal(true);
            }
        @endif
    });
</script>
@endsection
