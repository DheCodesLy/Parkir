<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiParkir extends Model
{
    public $fillable = ['kendaraan_id', 'lahan_parkir_id', 'kode_tiket', 'waktu_masuk', 'waktu_keluar', 'status_parkir', 'status_tiket', 'kondisi_kendaraan', 'alasan_denda', 'denda_manual', 'keterangan', 'dibuat_oleh', 'diperbarui_oleh'];

    public $casts = ['waktu_masuk', 'waktu_keluar'];

    public function Kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function lahanParkir()
    {
        return $this->belongsTo(LahanParkir::class, 'lahan_parkir_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, ['dibuat_oleh', 'diperbarui_oleh']);
    }

    public function Kompensasi()
    {
        return $this->hasOne(Kompensasi::class);
    }

    public function Pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}
