<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemilikKendaraan extends Model
{
    public $fillable = ['user_id', 'jenis_pemilik_id', 'status_aktif'];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function JenisPemilik()
    {
        return $this->belongsTo(JenisPemilik::class, 'jenis_pemilik_id');
    }

    public function Kendaraan()
    {
        return $this->hasOne(Kendaraan::class);
    }
}
