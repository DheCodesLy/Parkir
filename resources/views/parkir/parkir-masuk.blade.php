@extends('layouts.app')

@section('content')
@php
    $ticketData = session('ticket_data');
    $currentLahanId = (int) old('lahan_parkir_id', $lahanTerpilihId);
    $selectedLahan = $daftarLahan->firstWhere('id', $currentLahanId);
    $selectedJenisId = (int) old('jenis_kendaraan_id', $selectedJenisKendaraanId);
    $selectedJenisItem = $jenisKendaraan->firstWhere('id', $selectedJenisId);
    $plateReady = $currentLahanId && $selectedJenisId;
@endphp

<div class="min-h-[calc(100vh-2rem)] bg-slate-50 px-4 py-4 dark:bg-slate-950 sm:px-6 lg:px-8">
    <div class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-7xl flex-col gap-5">

        <div class="rounded-[28px] border border-slate-200 bg-white px-6 py-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600 dark:text-blue-400">
                        Parkir Management
                    </p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                        Input Kendaraan Parkir
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                        Pilih lahan parkir, tentukan jenis kendaraan, lalu masukkan nomor polisi untuk membuat tiket masuk.
                    </p>
                </div>

                <div class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    Mode Manual Aktif
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-12 gap-5">
            <aside class="col-span-12 xl:col-span-4">
                <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">
                                Ringkasan Operasional
                            </p>
                            <h2 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">
                                Status Saat Ini
                            </h2>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
                                Lahan Terpilih
                            </p>
                            <p id="selectedLahanName" class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">
                                {{ $selectedLahan?->nama_lahan ?? 'Belum dipilih' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-xs font-medium uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
                                    Sisa Slot
                                </p>
                                <p id="selectedLahanSlots" class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                                    {{ $selectedLahan ? $selectedLahan->sisa_slot . ' / ' . $selectedLahan->kapasitas : '-' }}
                                </p>
                            </div>

                            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                                <p class="text-xs font-medium uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
                                    Jenis Aktif
                                </p>
                                <p id="selectedVehicleCount" class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                                    {{ $jenisKendaraan->count() ?: '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950">
                            <p class="text-xs font-medium uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
                                Jenis Kendaraan
                            </p>
                            <p id="summarySelectedJenis" class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">
                                {{ $selectedJenisItem?->nama ?? 'Belum dipilih' }}
                            </p>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="col-span-12 xl:col-span-8">
                <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                        <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Form Kendaraan Masuk
                        </h2>
                        <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">
                            Halaman ini dirancang sebagai workstation operator untuk proses kendaraan masuk.
                        </p>
                    </div>

                    <form action="{{ route('transaksi-parkirs.akses-masuk') }}" method="POST" id="formMasukParkir">
                        @csrf

                        <div class="space-y-5 px-6 py-6">
                            <section class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950">
                                <div class="mb-4">
                                    <label for="lahan_parkir_id" class="block text-sm font-semibold text-slate-900 dark:text-white">
                                        Lahan Parkir
                                    </label>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                        Pilih area parkir untuk menampilkan jenis kendaraan yang tersedia.
                                    </p>
                                </div>

                                <select
                                    id="lahan_parkir_id"
                                    name="lahan_parkir_id"
                                    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-blue-400"
                                >
                                    <option value="">Pilih lahan parkir...</option>
                                    @foreach ($daftarLahan as $lahan)
                                        <option
                                            value="{{ $lahan->id }}"
                                            data-name="{{ $lahan->nama_lahan }}"
                                            data-sisa="{{ $lahan->sisa_slot }}"
                                            data-kapasitas="{{ $lahan->kapasitas }}"
                                            @selected($currentLahanId === (int) $lahan->id)
                                        >
                                            {{ $lahan->nama_lahan }} — {{ $lahan->sisa_slot }}/{{ $lahan->kapasitas }} slot
                                        </option>
                                    @endforeach
                                </select>

                                <p id="lahanHelper" class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                                    Pilih lahan untuk memuat opsi kendaraan.
                                </p>

                                @error('lahan_parkir_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </section>

                            <section class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950">
                                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                            Jenis Kendaraan
                                        </p>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                            Pilih jenis kendaraan yang sesuai dengan area parkir.
                                        </p>
                                    </div>

                                    <span id="jenisHint" class="text-xs text-slate-500 dark:text-slate-400">
                                        Otomatis dipilih jika hanya tersedia satu opsi.
                                    </span>
                                </div>

                                <div id="jenisKendaraanContainer" class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    @forelse ($jenisKendaraan as $item)
                                        <label class="block cursor-pointer">
                                            <input
                                                type="radio"
                                                name="jenis_kendaraan_id"
                                                value="{{ $item->id }}"
                                                data-name="{{ $item->nama }}"
                                                class="peer sr-only"
                                                @checked($selectedJenisId === (int) $item->id)
                                            >
                                            <div class="rounded-3xl border border-slate-300 bg-white p-4 transition duration-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:peer-checked:border-blue-500 dark:peer-checked:bg-blue-950/30">
                                                <p class="text-base font-semibold text-slate-900 dark:text-white">
                                                    {{ $item->nama }}
                                                </p>
                                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                    Tersedia pada lahan yang dipilih
                                                </p>
                                            </div>
                                        </label>
                                    @empty
                                        <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-4 py-6 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400 sm:col-span-2">
                                            Belum ada jenis kendaraan. Pilih lahan terlebih dahulu.
                                        </div>
                                    @endforelse
                                </div>

                                @error('jenis_kendaraan_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </section>

                            <section class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950">
                                <div class="mb-4">
                                    <label for="no_polisi" class="block text-sm font-semibold text-slate-900 dark:text-white">
                                        Nomor Polisi
                                    </label>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                        Masukkan nomor polisi kendaraan dengan format standar.
                                    </p>
                                </div>

                                <input
                                    type="text"
                                    id="no_polisi"
                                    name="no_polisi"
                                    value="{{ old('no_polisi') }}"
                                    maxlength="12"
                                    autocomplete="off"
                                    placeholder="AB 5678 RT"
                                    @disabled(!$plateReady)
                                    class="w-full rounded-3xl border border-slate-300 bg-white px-5 py-5 text-center text-3xl font-bold uppercase tracking-[0.18em] text-slate-900 outline-none transition placeholder:text-slate-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-700 dark:focus:border-blue-400"
                                >

                                <div class="mt-3 flex flex-col gap-1 text-xs text-slate-500 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between">
                                    <p>Format akan dirapikan otomatis saat diketik.</p>
                                    <p class="font-medium">Contoh: AB 5678 RT</p>
                                </div>

                                @error('no_polisi')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </section>
                        </div>

                        <div class="border-t border-slate-200 bg-white px-6 py-5 dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                                <button
                                    type="submit"
                                    id="submitButton"
                                    class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-blue-500 dark:hover:bg-blue-400"
                                >
                                    Proses Kendaraan Masuk
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</div>

@if ($ticketData)
    <div id="ticketModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/55 px-4 py-4 backdrop-blur-[2px]">
        <div class="w-full max-w-[320px] rounded-[22px] border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-3 border-b border-slate-200 px-4 py-3 dark:border-slate-800">
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-blue-600 dark:text-blue-400">
                        Transaksi Berhasil
                    </p>
                    <h3 class="mt-1 text-sm font-bold text-slate-900 dark:text-white">
                        Tiket siap dicetak
                    </h3>
                    <p class="mt-1 text-[11px] leading-4 text-slate-500 dark:text-slate-400">
                        Preview ringkas.
                    </p>
                </div>

                <a
                    href="{{ route('transaksi-parkirs.masuk') }}"
                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                    aria-label="Tutup modal"
                >
                    ✕
                </a>
            </div>

            <div class="px-4 py-4">
                <div class="rounded-[18px] bg-slate-100 p-3 dark:bg-slate-950/50">
                    <div
                        id="ticketPrintArea"
                        data-kode_tiket="{{ data_get($ticketData, 'kode_tiket') }}"
                        data-no_polisi="{{ data_get($ticketData, 'no_polisi') }}"
                        data-jenis_kendaraan="{{ data_get($ticketData, 'jenis_kendaraan') }}"
                        data-nama_lahan="{{ data_get($ticketData, 'nama_lahan') }}"
                        data-waktu_masuk="{{ data_get($ticketData, 'waktu_masuk') }}"
                        class="mx-auto w-full max-w-[210px] overflow-hidden rounded-[18px] border border-slate-200 bg-white text-slate-900 shadow-sm"
                    >
                        <div class="border-b border-dashed border-slate-200 px-3 pb-2.5 pt-3 text-center">
                            <p class="text-[8px] font-semibold uppercase tracking-[0.30em] text-slate-400">
                                Tiket Masuk
                            </p>
                            <p class="mt-1.5 text-[10px] font-semibold text-slate-500">
                                {{ config('app.name', 'Parkir Management') }}
                            </p>
                            <p class="mt-2 break-all text-[10px] font-bold leading-4 text-slate-900">
                                {{ data_get($ticketData, 'kode_tiket') }}
                            </p>
                        </div>

                        <div class="px-3 py-3">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-1.5">
                                <canvas id="ticketQrCanvas" width="128" height="128" class="mx-auto block"></canvas>
                            </div>

                            <p class="mt-2 text-center text-[8px] font-medium tracking-[0.12em] text-slate-500">
                                SCAN QR SAAT KELUAR
                            </p>
                        </div>

                        <div class="border-t border-dashed border-slate-200 px-3 py-3 text-center">
                            <p class="text-[8px] uppercase tracking-[0.18em] text-slate-400">
                                Waktu Masuk
                            </p>
                            <p class="mt-1 text-[10px] font-semibold leading-4 text-slate-900">
                                {{ data_get($ticketData, 'waktu_masuk') }}
                            </p>
                        </div>

                        <div class="border-t border-dashed border-slate-200 px-3 py-3 text-center">
                            <p class="text-[9px] leading-4 text-slate-500">
                                Simpan tiket ini dengan baik.<br>
                                Tunjukkan saat keluar.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-[11px] leading-4 text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200">
                    Detail kendaraan disembunyikan untuk menjaga privasi pengguna.
                </div>
            </div>

            <div class="flex gap-2 border-t border-slate-200 px-4 py-3 dark:border-slate-800">
                <a
                    href="{{ route('transaksi-parkirs.masuk') }}"
                    class="inline-flex h-10 flex-1 items-center justify-center rounded-xl border border-slate-300 px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                >
                    Kembali
                </a>

                <button
                    type="button"
                    onclick="printTicket()"
                    class="inline-flex h-10 flex-1 items-center justify-center rounded-xl bg-blue-600 px-3 text-sm font-semibold text-white transition hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-400"
                >
                    Cetak Tiket
                </button>
            </div>
        </div>
    </div>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const lahanSelect = document.getElementById('lahan_parkir_id');
    const jenisContainer = document.getElementById('jenisKendaraanContainer');
    const plateInput = document.getElementById('no_polisi');
    const submitButton = document.getElementById('submitButton');
    const selectedLahanName = document.getElementById('selectedLahanName');
    const selectedLahanSlots = document.getElementById('selectedLahanSlots');
    const selectedVehicleCount = document.getElementById('selectedVehicleCount');
    const summarySelectedJenis = document.getElementById('summarySelectedJenis');
    const lahanHelper = document.getElementById('lahanHelper');
    const jenisHint = document.getElementById('jenisHint');
    const pilihLahanUrl = @json(route('transaksi-parkirs.pilih-lahan'));
    const csrfToken = @json(csrf_token());
    const appName = @json(config('app.name', 'Parkir Management'));

    function escapeHtml(value = '') {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getSelectedJenisInput() {
        return jenisContainer ? jenisContainer.querySelector('input[name="jenis_kendaraan_id"]:checked') : null;
    }

    function updateSelectedJenisText() {
        if (!summarySelectedJenis) return;
        const selected = getSelectedJenisInput();
        summarySelectedJenis.textContent = selected ? selected.dataset.name : 'Belum dipilih';
    }

    function normalizeSpaces(value) {
        return String(value).replace(/\s+/g, ' ').trim();
    }

    function formatNoPolisi(value) {
        const raw = String(value)
            .toUpperCase()
            .replace(/[^A-Z0-9]/g, '')
            .slice(0, 9);

        let depan = '';
        let angka = '';
        let belakang = '';

        const matchDepan = raw.match(/^[A-Z]{1,2}/);
        if (matchDepan) {
            depan = matchDepan[0];
        }

        const sisa1 = raw.slice(depan.length);

        const matchAngka = sisa1.match(/^\d{1,4}/);
        if (matchAngka) {
            angka = matchAngka[0];
        }

        const sisa2 = sisa1.slice(angka.length);

        const matchBelakang = sisa2.match(/^[A-Z]{1,3}/);
        if (matchBelakang) {
            belakang = matchBelakang[0];
        }

        return [depan, angka, belakang].filter(Boolean).join(' ').trim();
    }

    function isPlateComplete() {
        if (!plateInput) return false;

        const value = normalizeSpaces(plateInput.value).toUpperCase();
        return /^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/.test(value);
    }

    function syncButtonState() {
        const hasLahan = !!(lahanSelect && lahanSelect.value);
        const hasJenis = !!getSelectedJenisInput();

        if (plateInput) {
            plateInput.disabled = !(hasLahan && hasJenis);
        }

        if (submitButton) {
            submitButton.disabled = !(hasLahan && hasJenis && isPlateComplete());
        }
    }

    function updateSummaryFromSelect() {
        if (!lahanSelect || !selectedLahanName || !selectedLahanSlots) return;

        const option = lahanSelect.options[lahanSelect.selectedIndex];

        if (!option || !option.value) {
            selectedLahanName.textContent = 'Belum dipilih';
            selectedLahanSlots.textContent = '-';
            return;
        }

        selectedLahanName.textContent = option.dataset.name || 'Belum dipilih';
        selectedLahanSlots.textContent = `${option.dataset.sisa || 0} / ${option.dataset.kapasitas || 0}`;
    }

    function bindJenisRadioEvents() {
        if (!jenisContainer) return;

        const radios = jenisContainer.querySelectorAll('input[name="jenis_kendaraan_id"]');

        radios.forEach((radio) => {
            radio.addEventListener('change', function () {
                updateSelectedJenisText();
                syncButtonState();

                if (plateInput && !plateInput.disabled) {
                    plateInput.focus();
                }
            });
        });
    }

    function renderEmptyJenis(message) {
        if (!jenisContainer) return;

        jenisContainer.innerHTML = `
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-4 py-6 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400 sm:col-span-2">
                ${escapeHtml(message)}
            </div>
        `;

        if (selectedVehicleCount) {
            selectedVehicleCount.textContent = '-';
        }

        updateSelectedJenisText();
        syncButtonState();
    }

    function renderJenisKendaraan(items, selectedId = null) {
        if (!jenisContainer) return;

        if (!Array.isArray(items) || items.length === 0) {
            renderEmptyJenis('Tidak ada jenis kendaraan aktif pada lahan ini.');
            return;
        }

        jenisContainer.innerHTML = items.map((item) => {
            const checked = Number(selectedId) === Number(item.id);

            return `
                <label class="block cursor-pointer">
                    <input
                        type="radio"
                        name="jenis_kendaraan_id"
                        value="${item.id}"
                        data-name="${escapeHtml(item.nama)}"
                        class="peer sr-only"
                        ${checked ? 'checked' : ''}
                    >
                    <div class="rounded-3xl border border-slate-300 bg-white p-4 transition duration-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:peer-checked:border-blue-500 dark:peer-checked:bg-blue-950/30">
                        <p class="text-base font-semibold text-slate-900 dark:text-white">${escapeHtml(item.nama)}</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Tersedia pada lahan yang dipilih</p>
                    </div>
                </label>
            `;
        }).join('');

        if (selectedVehicleCount) {
            selectedVehicleCount.textContent = items.length;
        }

        bindJenisRadioEvents();
        updateSelectedJenisText();
        syncButtonState();
    }

    async function loadJenisKendaraan(lahanId) {
        if (!lahanId) {
            if (lahanHelper) {
                lahanHelper.textContent = 'Pilih lahan untuk memuat opsi kendaraan.';
            }

            if (jenisHint) {
                jenisHint.textContent = 'Otomatis dipilih jika hanya tersedia satu opsi.';
            }

            renderEmptyJenis('Belum ada jenis kendaraan. Pilih lahan terlebih dahulu.');
            return;
        }

        if (lahanHelper) {
            lahanHelper.textContent = 'Memuat jenis kendaraan...';
        }

        if (jenisHint) {
            jenisHint.textContent = 'Sedang mengambil data lahan...';
        }

        if (lahanSelect) {
            lahanSelect.disabled = true;
        }

        try {
            const response = await fetch(pilihLahanUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    lahan_parkir_id: lahanId
                }),
            });

            const result = await response.json();

            if (!response.ok) {
                const message =
                    result?.errors?.lahan_parkir_id?.[0] ||
                    result?.message ||
                    'Gagal memuat jenis kendaraan.';
                throw new Error(message);
            }

            renderJenisKendaraan(result.data.jenis_kendaraans, result.data.selected_id ?? null);

            if (selectedLahanName) {
                selectedLahanName.textContent = result.data.lahan.nama_lahan;
            }

            if (selectedLahanSlots) {
                selectedLahanSlots.textContent = `${result.data.lahan.sisa_slot} / ${result.data.lahan.kapasitas}`;
            }

            if (lahanHelper) {
                lahanHelper.textContent = 'Lahan berhasil dipilih.';
            }

            if (jenisHint) {
                jenisHint.textContent = result.data.auto_selected
                    ? 'Jenis kendaraan dipilih otomatis karena hanya tersedia satu opsi.'
                    : 'Pilih jenis kendaraan sebelum memasukkan nomor polisi.';
            }

            if (result.data.auto_selected && plateInput) {
                plateInput.focus();
            }
        } catch (error) {
            renderEmptyJenis(error.message || 'Gagal memuat jenis kendaraan.');

            if (lahanHelper) {
                lahanHelper.textContent = error.message || 'Gagal memuat jenis kendaraan.';
            }

            if (jenisHint) {
                jenisHint.textContent = 'Silakan pilih ulang lahan.';
            }
        } finally {
            if (lahanSelect) {
                lahanSelect.disabled = false;
            }

            updateSelectedJenisText();
            syncButtonState();
        }
    }

    function buildQrPayload(ticketArea) {
        const kodeTiket = ticketArea?.dataset?.kode_tiket || '';
        const waktuMasuk = ticketArea?.dataset?.waktu_masuk || '';

        return JSON.stringify({
            kode_tiket: kodeTiket,
            waktu_masuk: waktuMasuk,
        });
    }

    function renderTicketQr() {
        const ticketArea = document.getElementById('ticketPrintArea');
        const qrCanvas = document.getElementById('ticketQrCanvas');

        if (!ticketArea || !qrCanvas || typeof QRious === 'undefined') {
            return;
        }

        const payload = buildQrPayload(ticketArea);

        new QRious({
            element: qrCanvas,
            value: payload,
            size: 160,
            level: 'H',
            foreground: '#111827',
            background: '#ffffff',
        });
    }

    if (lahanSelect) {
        lahanSelect.addEventListener('change', function () {
            updateSummaryFromSelect();
            loadJenisKendaraan(this.value);
        });
    }

    if (plateInput) {
        plateInput.value = formatNoPolisi(plateInput.value);

        plateInput.addEventListener('input', function () {
            this.value = formatNoPolisi(this.value);
            syncButtonState();
        });

        plateInput.addEventListener('blur', function () {
            this.value = formatNoPolisi(this.value);
            syncButtonState();
        });
    }

    const formMasukParkir = document.getElementById('formMasukParkir');
    if (formMasukParkir) {
        formMasukParkir.addEventListener('submit', function () {
            if (plateInput) {
                plateInput.value = formatNoPolisi(plateInput.value);
            }
        });
    }

    bindJenisRadioEvents();
    updateSummaryFromSelect();
    updateSelectedJenisText();
    syncButtonState();
    renderTicketQr();

    window.__ticketAppName = appName;
});

function printTicket() {
    const ticket = document.getElementById('ticketPrintArea');
    const qrCanvas = document.getElementById('ticketQrCanvas');
    const appName = window.__ticketAppName || 'Parkir Management';

    if (!ticket || !qrCanvas) return;

    const qrDataUrl = qrCanvas.toDataURL('image/png');

    const data = {
        kode_tiket: ticket.dataset.kode_tiket || '',
        waktu_masuk: ticket.dataset.waktu_masuk || '',
    };

    function esc(value = '') {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    const printWindow = window.open('', '_blank', 'width=420,height=720');
    if (!printWindow) return;

    printWindow.document.open();
    printWindow.document.write(`
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cetak Tiket Parkir</title>
            <style>
                @page { size: 58mm auto; margin: 0; }
                * { box-sizing: border-box; }
                html, body {
                    margin: 0;
                    padding: 0;
                    width: 58mm;
                    background: #ffffff;
                    color: #111827;
                    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
                }
                body { display: flex; justify-content: center; }
                .sheet { width: 58mm; padding: 2mm 0; }
                .ticket {
                    width: 50mm;
                    margin: 0 auto;
                    background: #ffffff;
                    color: #111827;
                    font-size: 11px;
                    line-height: 1.45;
                }
                .center { text-align: center; }
                .muted { color: #6b7280; }
                .small { font-size: 9px; }
                .tiny { font-size: 8px; }
                .strong { font-weight: 700; }
                .brand {
                    font-size: 10px;
                    font-weight: 700;
                    margin-top: 4px;
                }
                .code {
                    margin-top: 6px;
                    font-size: 13px;
                    font-weight: 700;
                    word-break: break-word;
                }
                .separator {
                    border-top: 1px dashed #94a3b8;
                    margin-top: 8px;
                    padding-top: 8px;
                }
                .qr-wrap { text-align: center; }
                .qr-wrap img {
                    width: 30mm;
                    height: 30mm;
                    display: block;
                    margin: 0 auto;
                    image-rendering: pixelated;
                }
                .label {
                    font-size: 8px;
                    text-transform: uppercase;
                    letter-spacing: 0.16em;
                    color: #6b7280;
                }
                .value {
                    margin-top: 2px;
                    font-size: 11px;
                    font-weight: 700;
                    word-break: break-word;
                }
                .footer {
                    text-align: center;
                    font-size: 9px;
                    color: #6b7280;
                    line-height: 1.5;
                }
            </style>
        </head>
        <body>
            <div class="sheet">
                <div class="ticket">
                    <div class="center">
                        <div class="small strong muted" style="letter-spacing:0.3em;">TIKET MASUK</div>
                        <div class="brand">${esc(appName)}</div>
                        <div class="code">${esc(data.kode_tiket)}</div>
                    </div>

                    <div class="separator qr-wrap">
                        <img src="${qrDataUrl}" alt="QR Ticket">
                        <div class="tiny muted" style="margin-top:6px; letter-spacing:0.14em;">SCAN QR SAAT KELUAR</div>
                    </div>

                    <div class="separator center">
                        <div class="label">Waktu Masuk</div>
                        <div class="value">${esc(data.waktu_masuk)}</div>
                    </div>

                    <div class="separator footer">
                        Harap simpan tiket ini dengan baik.<br>
                        Tiket wajib ditunjukkan saat keluar parkiran.
                    </div>
                </div>
            </div>

            <script>
                window.onload = function () {
                    window.focus();
                    setTimeout(function () {
                        window.print();
                        window.close();
                    }, 250);
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
@endsection
