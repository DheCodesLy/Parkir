<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    public $fillable = ['pemilik_id', 'no_polisi', 'jenis_kendaraan_id', 'merk', 'warna', 'catatan', 'status_aktif'];

    public function Pemilik()
    {
        return $this->belongsTo(PemilikKendaraan::class, 'pemilik_id');
    }

    public function JenisKendaraan()
    {
        return $this->belongsTo(JenisKendaraan::class, 'jenis_kendaraan_id');
    }

    public function TransaksiParkir()
    {
        return $this->hasMany(TransaksiParkir::class);
    }
}
