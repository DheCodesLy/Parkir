<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LahanParkir extends Model
{
    public $fillable = ['nama_lahan', 'kapasitas', 'sisa_slot', 'status_aktif'];
    
    public function KapasitasParkir()
    {
        return $this->hasMany(KapasitasParkir::class);
    }
}
