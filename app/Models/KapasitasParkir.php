<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KapasitasParkir extends Model
{
    public $fillable = ['lahan_parkir_id', 'jenis_pemilik_id', 'jenis_kendaraan_id', 'status_aktif'];

    protected $casts = ['status_aktif' => 'boolean'];

    public function LahanParkir()
    {
        return $this->belongsTo(LahanParkir::class ,'lahan_parkir_id');
    }

    public function JenisPemilik ()
    {
        return $this->belongsTo(JenisPemilik::class , 'jenis_pemilik_id');
    }

    public function JenisKendaraan()
    {
        return $this->belongsTo(JenisKendaraan::class);
    }
}
