<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifParkir extends Model
{
    public $fillable = ['lahan_id', 'jenis_kendaraan_id', 'jenis_pemilik_id', 'biaya_masuk', 'biaya_per_jam', 'biaya_maksimal', 'gratis_menit', 'status_aktif', 'masa_berlaku', 'selesai_berlaku'];

    public $casts = ['masa_berlaku', 'selesai_berlaku'];

    public function JenisKendaraan()
    {
        return $this->belongsTo(JenisKendaraan::class, 'jenis_kendaraan_id');
    }

    public function LahanParkir()
    {
        return $this->belongsTo(LahanParkir::class, 'lahan_id');
    }

    public function JenisPemilik()
    {
        return $this->belongsTo(JenisPemilik::class, 'jenis_pemilik_id');
    }
}
