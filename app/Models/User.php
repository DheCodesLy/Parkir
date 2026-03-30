<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password', 'alamat', 'status_aktif'];

    public function PemilikKendaraan()
    {
        return $this->hasOne(PemilikKendaraan::class);
    }

    public function UserRole()
    {
        return $this->hasMany(UserRole::class);
    }

    public function TransaksiParkir()
    {
        return $this->hasMany(TransaksiParkir::class);
    }
}
