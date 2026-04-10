<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kompensasi extends Model
{
    protected $fillable = ['kode_klaim', 'tanggal_kejadian', 'transaksi_parkir_id', 'jenis_kompensasi', 'tipe_kompensasi', 'nominal_disetujui', 'bukti_pengaju', 'nama_pengaju', 'no_hp_pengaju', 'keterangan', 'status_pengajuan', 'dibuat_oleh', 'diverifikasi_oleh', 'tanggal_verifikasi', 'catatan_verifikator', 'bukti_kompensasi'];

    protected $casts = ['tanggal_kejadian' => 'datetime', 'tanggal_verifikasi' => 'datetime', 'nominal_disetujui' => 'decimal:2'];

    public function TransaksiParkir()
    {
        return $this->belongsTo(TransaksiParkir::class, 'transaksi_parkir_id');
    }

    public function DibuatOleh()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function DiverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
