<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LahanParkir extends Model
{
    public $fillable = ['nama_lahan', 'kapasitas', 'sisa_slot', 'status_aktif'];

    protected $casts = ['status_aktif' => 'boolean'];

    public function KapasitasParkir()
    {
        return $this->hasMany(KapasitasParkir::class);
    }

    public function TarifParkir()
    {
        return $this->hasMany(TarifParkir::class);
    }

    public function transaksiParkirs()
    {
        return $this->hasMany(TransaksiParkir::class);
    }
}
