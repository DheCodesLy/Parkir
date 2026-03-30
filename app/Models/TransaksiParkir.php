<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiParkir extends Model
{
    public $fillable = ['kendaraan_id', 'kode_tiket', 'waktu_masuk', 'waktu_keluar', 'status_parkir', 'status_tiket', 'kondisi_kendaraan', 'alasan_denda', 'denda_manual', 'keterangan', 'dibuat_oleh', 'diperbarui_oleh'];

    public function Kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class, ['dibuat_oleh', 'diperbarui_oleh']);
    }
}
