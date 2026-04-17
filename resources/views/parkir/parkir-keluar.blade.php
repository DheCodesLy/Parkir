@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-6 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-l-4 border-blue-600 p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="ri-logout-box-r-line text-blue-600 dark:text-blue-400"></i>
                    Workstation Keluar
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pindai tiket atau masukkan plat nomor untuk menyelesaikan transaksi parkir.</p>
            </div>
            <div class="inline-flex items-center px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 text-sm font-semibold shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 dark:bg-blue-400 mr-2 animate-pulse"></span>
                Sistem Aktif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <div class="lg:col-span-4 flex flex-col">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex-grow overflow-hidden flex flex-col">
                    <div class="bg-blue-600 px-5 py-4">
                        <h3 class="font-semibold text-white flex items-center gap-2">
                            <i class="ri-file-list-3-line text-blue-200"></i> Rincian Kendaraan
                        </h3>
                    </div>

                    <div class="p-5 flex-grow flex flex-col">
                        <div id="sidebar_placeholder" class="flex-grow flex flex-col items-center justify-center text-center py-10">
                            <div class="w-20 h-20 bg-blue-50 dark:bg-slate-700 rounded-full flex items-center justify-center mb-4">
                                <i class="ri-car-line text-4xl text-blue-300 dark:text-slate-500"></i>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Menunggu input pencarian.<br>Data kendaraan akan tampil di sini.</p>
                        </div>

                        <div id="sidebar_data" style="display: none;" class="space-y-4">
                            <div class="bg-blue-50 dark:bg-slate-700/50 rounded-lg p-4 border border-blue-100 dark:border-slate-600 text-center">
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Nomor Polisi</span>
                                <h3 id="txt_plat" class="text-3xl font-black text-slate-800 dark:text-white tracking-widest mt-1">D 1234 ABC</h3>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-3">
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Jenis</span>
                                    <p id="txt_jenis" class="text-lg font-semibold text-slate-800 dark:text-white mt-0.5">Mobil</p>
                                </div>
                                <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-3">
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Tiket</span>
                                    <p id="txt_tiket" class="text-lg font-semibold text-slate-800 dark:text-white mt-0.5 text-truncate">PKR-123</p>
                                </div>
                            </div>

                            <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-3">
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Pemilik</span>
                                <p id="txt_pemilik" class="text-base font-semibold text-slate-800 dark:text-white mt-0.5 flex items-center gap-2">
                                    <i class="ri-user-line text-blue-500"></i> <span>Umum / Tamu</span>
                                </p>
                            </div>

                            <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-3 bg-slate-50 dark:bg-slate-700/30">
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Waktu Masuk</span>
                                <p id="txt_masuk" class="text-base font-medium text-slate-800 dark:text-white mt-0.5">27 Okt 2023, 10:00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 flex flex-col space-y-6">

                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Scan Tiket / Ketik Plat Nomor</label>

                    <div class="flex gap-3">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="ri-search-line text-blue-500 text-xl"></i>
                            </div>
                            <input type="text" id="keyword_pencarian"
                                class="w-full pl-12 pr-4 py-4 text-2xl font-bold tracking-widest uppercase text-blue-800 dark:text-blue-100 bg-blue-50/50 dark:bg-slate-900 border-2 border-blue-200 dark:border-slate-600 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-600 dark:focus:border-blue-500 transition-all placeholder:text-blue-300 dark:placeholder:text-slate-600"
                                placeholder="D 1234 ABC" autocomplete="off">
                        </div>
                        <button type="button" id="btnToggleScanner" class="flex-none px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md transition-colors focus:ring-4 focus:ring-blue-500/30 flex items-center justify-center gap-2">
                            <i class="ri-qr-scan-line text-2xl"></i>
                            <span class="hidden sm:inline font-semibold">Kamera</span>
                        </button>
                    </div>
                    <div class="mt-2 flex justify-between text-xs text-slate-500 dark:text-slate-400 px-1">
                        <span>Otomatis diubah ke huruf besar (Uppercase)</span>
                        <span>Tekan <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-md font-mono text-slate-600 dark:text-slate-300">Enter</kbd> untuk mencari</span>
                    </div>

                    <div id="scannerBox" style="display:none;" class="mt-4 p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 text-center">
                        <div id="qr-reader" class="mx-auto max-w-sm rounded-lg overflow-hidden border-2 border-dashed border-blue-500"></div>
                        <button type="button" id="btnCloseScanner" class="mt-4 px-4 py-2 bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 text-sm font-semibold rounded-lg hover:bg-rose-200 dark:hover:bg-rose-900/50 transition-colors">
                            Tutup Kamera
                        </button>
                    </div>
                </div>

                <div id="formKeluar" style="display: none;" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <form action="" method="POST" id="mainForm">
                        @csrf

                        <div class="mb-8">
                            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3 border-b border-slate-100 dark:border-slate-700 pb-2">Kondisi Kendaraan Saat Keluar</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="relative">
                                    <input type="radio" name="kondisi_kendaraan" id="k_baik" value="baik" class="peer sr-only" checked>
                                    <label for="k_baik" class="block p-4 border-2 border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer transition-all hover:border-blue-400 peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center peer-checked:bg-blue-600 text-blue-600 peer-checked:text-white transition-colors">
                                                <i class="ri-check-line text-lg"></i>
                                            </div>
                                            <div>
                                                <h5 class="font-bold text-slate-800 dark:text-white">Baik</h5>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">Kondisi normal</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div class="relative">
                                    <input type="radio" name="kondisi_kendaraan" id="k_rusak" value="rusak" class="peer sr-only">
                                    <label for="k_rusak" class="block p-4 border-2 border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer transition-all hover:border-amber-400 peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 peer-checked:shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center text-amber-600 transition-colors">
                                                <i class="ri-error-warning-line text-lg"></i>
                                            </div>
                                            <div>
                                                <h5 class="font-bold text-slate-800 dark:text-white">Rusak</h5>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">Ada lecet/rusak</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div class="relative">
                                    <input type="radio" name="kondisi_kendaraan" id="k_hilang" value="hilang" class="peer sr-only">
                                    <label for="k_hilang" class="block p-4 border-2 border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer transition-all hover:border-rose-400 peer-checked:border-rose-500 peer-checked:bg-rose-50 dark:peer-checked:bg-rose-900/20 peer-checked:shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-900/50 flex items-center justify-center text-rose-600 transition-colors">
                                                <i class="ri-close-line text-lg"></i>
                                            </div>
                                            <div>
                                                <h5 class="font-bold text-slate-800 dark:text-white">Hilang</h5>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">Tidak ditemukan</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3 border-b border-slate-100 dark:border-slate-700 pb-2">Penyesuaian Biaya & Denda</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                                    <label class="flex items-center cursor-pointer mb-1">
                                        <div class="relative">
                                            <input type="checkbox" name="tiket_hilang" id="tiket_hilang" value="1" class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </div>
                                        <span class="ml-3 font-semibold text-slate-800 dark:text-white">Tiket Fisik Hilang</span>
                                    </label>

                                    <div id="input_denda_tiket" style="display:none;" class="mt-4">
                                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Nominal Denda</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-slate-500 font-medium">Rp</span>
                                            </div>
                                            <input type="number" name="nominal_denda_tiket_hilang" class="w-full pl-10 pr-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:text-white" value="20000">
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                                    <label class="flex items-center cursor-pointer mb-1">
                                        <div class="relative">
                                            <input type="checkbox" id="pakai_denda_manual" class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </div>
                                        <span class="ml-3 font-semibold text-slate-800 dark:text-white">Tambah Denda Lain</span>
                                    </label>

                                    <div id="box_denda_manual" style="display:none;" class="mt-4 space-y-3">
                                        <div>
                                            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Keterangan Denda</label>
                                            <input type="text" name="denda_manual[0][alasan]" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:text-white" placeholder="Contoh: Kendaraan menginap">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Nominal</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-slate-500 font-medium">Rp</span>
                                                </div>
                                                <input type="number" name="denda_manual[0][nominal]" class="w-full pl-10 pr-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:text-white" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i class="ri-check-double-line text-xl"></i>
                                Selesaikan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

{{-- Pemuatan Script HANYA SATU KALI untuk menghindari bentrok --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // --- STATE & GLOBAL VARIABLES ---
    let html5QrCode = null;
    let idTerpilih = null;

    // --- 1. UTILITY FUNCTIONS ---
    // Format Plat Nomor otomatis
    const formatInput = (value) => {
        let val = String(value).toUpperCase();
        if (val.startsWith('TKT')) return val.replace(/\s+/g, '');

        const raw = val.replace(/[^A-Z0-9]/g, '').slice(0, 9);
        let depan = '', angka = '', belakang = '';
        const matchDepan = raw.match(/^[A-Z]{1,2}/);
        if (matchDepan) depan = matchDepan[0];
        const sisa1 = raw.slice(depan.length);
        const matchAngka = sisa1.match(/^\d{1,4}/);
        if (matchAngka) angka = matchAngka[0];
        const sisa2 = sisa1.slice(angka.length);
        const matchBelakang = sisa2.match(/^[A-Z]{1,3}/);
        if (matchBelakang) belakang = matchBelakang[0];

        return [depan, angka, belakang].filter(Boolean).join(' ').trim();
    };

    // UI Reset - Sembunyikan form jika pencarian gagal/baru
    const resetUI = () => {
        idTerpilih = null;
        $("#formKeluar").slideUp(300);
        $("#sidebar_data").hide();
        $("#sidebar_placeholder").fadeIn(300);
    };

    // --- 2. EVENT HANDLERS (PENCARIAN) ---
    $('#keyword_pencarian').on('input', function() {
        $(this).val(formatInput($(this).val()));
    });

    $('#keyword_pencarian').on('keypress', function (e) {
        if(e.which === 13){
            e.preventDefault();
            fetchData($(this).val());
        }
    });

    $("#keyword_pencarian").autocomplete({
        source: "{{ route('transaksi-parkirs.autocomplete.plat') }}",
        minLength: 2,
        select: (event, ui) => fetchData(ui.item.value)
    });

    // --- 3. SCANNER LOGIC ---
    const stopScanner = () => {
        if (html5QrCode) {
            html5QrCode.stop().then(() => $("#scannerBox").slideUp(300)).catch(() => $("#scannerBox").slideUp(300));
        } else {
            $("#scannerBox").slideUp(300);
        }
    };

    $("#btnToggleScanner").on("click", function() {
        let scannerBox = $("#scannerBox");
        if (scannerBox.is(":hidden")) {
            scannerBox.slideDown(300);
            if (!html5QrCode) html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 },
                (decodedText) => {
                    let finalKeyword = decodedText;
                    try {
                        let parsed = JSON.parse(decodedText);
                        finalKeyword = parsed.kode_tiket || decodedText;
                    } catch (e) {}
                    $("#keyword_pencarian").val(finalKeyword.toUpperCase());
                    stopScanner();
                    fetchData(finalKeyword);
                }
            ).catch(() => {
                Swal.fire('Error', 'Kamera tidak bisa diakses', 'error');
                scannerBox.slideUp(300);
            });
        } else {
            stopScanner();
        }
    });

    $("#btnCloseScanner").click(stopScanner);

    // --- 4. AJAX: CARI DATA ---
    function fetchData(keyword) {
        if(!keyword.trim()) return;

        Swal.fire({
            title: 'Mencari data...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: `{{ url('/parkir/cari') }}/${encodeURIComponent(keyword)}`,
            method: "GET",
            success: function(res) {
                Swal.close();
                if(res.success) {
                    idTerpilih = res.data.id;
                    // Update Informasi di UI
                    $("#txt_plat").text(res.data.nomor_plat || '-');
                    $("#txt_jenis").text(res.data.jenis_kendaraan || '-');
                    $("#txt_tiket").text(res.data.kode_tiket || '-');
                    $("#txt_pemilik").html(`<i class="ri-user-line text-blue-500"></i> ${res.data.nama_pemilik || 'Umum / Tamu'}`);

                    let msk = res.data.waktu_masuk ? new Date(res.data.waktu_masuk).toLocaleString('id-ID') : '-';
                    $("#txt_masuk").text(msk);

                    $("#sidebar_placeholder").hide();
                    $("#sidebar_data").fadeIn(400);
                    $("#formKeluar").slideDown(400);
                }
            },
            error: function(xhr) {
                resetUI();
                let msg = xhr.responseJSON?.message || "Data tidak ditemukan atau sudah keluar.";
                Swal.fire('Gagal', msg, 'error');
            }
        });
    }

    // --- 5. AJAX: SIMPAN DATA (SUBMIT) ---
    $("#mainForm").on("submit", function(e) {
        e.preventDefault();

        if (!idTerpilih) {
            Swal.fire('Peringatan', 'Cari data kendaraan terlebih dahulu!', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi',
            text: "Selesaikan transaksi parkir ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Selesai',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                $.ajax({
                    url: `{{ url('/transaksi-parkirs/keluar') }}/${idTerpilih}`,
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || "Terjadi kesalahan sistem.";
                        Swal.fire('Gagal', msg, 'error');
                    }
                });
            }
        });
    });

    // --- 6. UI INTERACTION ---
    $("#tiket_hilang").change(function() {
        this.checked ? $("#input_denda_tiket").slideDown(250) : $("#input_denda_tiket").slideUp(250);
    });

    $("#pakai_denda_manual").change(function() {
        this.checked ? $("#box_denda_manual").slideDown(250) : $("#box_denda_manual").slideUp(250);
    });
});
</script>
