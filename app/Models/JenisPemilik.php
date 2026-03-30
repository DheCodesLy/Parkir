<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPemilik extends Model
{
    public $fillable = ['nama_jenis_pemilik', 'kode_jenis_pemilik', 'deskripsi', 'status_aktif'];

    public function KapasitasParkir()
    {
        return $this->hasMany(KapasitasParkir::class);
    }

    public function Kendaraan()
    {
        return $this->hasMany(Kendaraan::class);
    }

    public function PemilikKendaraan()
    {
        return $this->hasMany(PemilikKendaraan::class);
    }

    public function TarifParkir()
    {
        return $this->hasMany(TarifParkir::class);
    }
}
