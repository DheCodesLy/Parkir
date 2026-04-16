@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="mb-0">Form Kendaraan Keluar</h5>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <label class="form-label fw-bold">Scan QR atau Ketik Plat Nomor</label>
                <div class="input-group">
                    <input type="text" id="keyword_pencarian" class="form-control border-primary" placeholder="Contoh: D 1234 ABC / PKR-XXXX">
                    <button class="btn btn-primary" type="button" id="btnToggleScanner">
                        <i class="ri-camera-line"></i> Buka Kamera
                    </button>
                </div>

                <div id="scannerBox" class="mt-3 text-center" style="display:none; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <div id="qr-reader" class="mx-auto" style="width: 100%; max-width: 400px; border: 2px solid #0d6efd;"></div>
                    <button type="button" id="btnCloseScanner" class="btn btn-danger btn-sm mt-2">Tutup Kamera</button>
                </div>
            </div>

            <hr>

            <div id="formKeluar" style="display: none;">
                <form action="" method="POST" id="mainForm">
                    @csrf
                    <div class="alert alert-info py-2">
                        <p class="mb-1"><strong>Data Ditemukan:</strong></p>
                        <div id="info_kendaraan" class="small"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kondisi Kendaraan:</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kondisi_kendaraan" id="k_baik" value="baik" checked>
                                <label class="form-check-label" for="k_baik">Baik</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kondisi_kendaraan" id="k_rusak" value="rusak">
                                <label class="form-check-label" for="k_rusak">Rusak</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kondisi_kendaraan" id="k_hilang" value="hilang">
                                <label class="form-check-label" for="k_hilang">Hilang</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="tiket_hilang" id="tiket_hilang" value="1">
                            <label class="form-check-label fw-bold" for="tiket_hilang">Tiket Hilang (Denda Otomatis)</label>
                        </div>
                        <div id="input_denda_tiket" class="mt-2" style="display:none">
                            <input type="number" name="nominal_denda_tiket_hilang" class="form-control" placeholder="Nominal Denda (Rp)" value="20000">
                        </div>
                    </div>

                    <div class="mb-3 p-3 border rounded bg-light">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="pakai_denda_manual">
                            <label class="form-check-label fw-bold" for="pakai_denda_manual">Tambah Denda Manual</label>
                        </div>
                        <div id="box_denda_manual" style="display:none">
                            <input type="text" name="denda_manual[0][alasan]" class="form-control mb-2" placeholder="Alasan denda">
                            <input type="number" name="denda_manual[0][nominal]" class="form-control" placeholder="Nominal Rp">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Proses Keluar</button>
                </form>
            </div>

            <div id="placeholder_msg" class="text-center py-4 text-muted">
                Belum ada data yang dipilih.
            </div>
        </div>
    </div>
</div>
@endsection

{{-- JANGAN GUNAKAN @PUSH DULU, PAKAI SCRIPT BIASA UNTUK TESTING --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log("Halaman Ready, Script Dimuat."); // Cek di console

    let html5QrCode = null;

    // --- FUNGSI SCANNER ---
    $("#btnToggleScanner").on("click", function() {
        console.log("Tombol Buka Kamera diklik.");
        $("#scannerBox").toggle();

        if ($("#scannerBox").is(":visible")) {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            }

            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                console.log("Scan Sukses: " + decodedText);
                $("#keyword_pencarian").val(decodedText);
                stopScanner();
                fetchData(decodedText);
            }).catch(err => {
                alert("Gagal akses kamera: " + err);
            });
        } else {
            stopScanner();
        }
    });

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => { $("#scannerBox").hide(); });
        }
    }

    $("#btnCloseScanner").click(function() { stopScanner(); });

    // --- AUTOCOMPLETE ---
    $("#keyword_pencarian").autocomplete({
        source: "{{ route('transaksi-parkirs.autocomplete.plat') }}",
        minLength: 2,
        select: function(event, ui) {
            fetchData(ui.item.value);
        }
    });

    // --- FETCH DATA ---
    function fetchData(keyword) {
        $.ajax({
            url: "/parkir/cari/" + encodeURIComponent(keyword),
            method: "GET",
            success: function(res) {
                if(res.success) {
                    $("#placeholder_msg").hide();
                    $("#formKeluar").fadeIn();
                    $("#mainForm").attr("action", "/parkir/keluar/" + res.data.id);
                    $("#info_kendaraan").html(`
                        Plat: ${res.data.nomor_plat} | Jenis: ${res.data.jenis_kendaraan}<br>
                        Pemilik: ${res.data.nama_pemilik} | Masuk: ${res.data.waktu_masuk}
                    `);
                }
            },
            error: function() {
                Swal.fire("Error", "Data tidak ditemukan atau sudah keluar", "error");
            }
        });
    }

    // --- UI TOGGLE ---
    $("#tiket_hilang").change(function() {
        $("#input_denda_tiket").toggle(this.checked);
    });

    $("#pakai_denda_manual").change(function() {
        $("#box_denda_manual").toggle(this.checked);
    });
});
</script>
