<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKendaraan extends Model
{
    public $fillable = ['nama_jenis_kendaraan', 'kode_jenis_kendaraan', 'deskripsi', 'status_aktif'];

    public function KapasitasParkir()
    {
        return $this->hasMany(KapasitasParkir::class);
    }

    public function Kendaraan()
    {
        return $this->hasMany(Kendaraan::class);
    }

    public function TarifParkir()
    {
        return $this->hasMany(TarifParkir::class);
    }
}
