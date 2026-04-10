<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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

    public function Kompensasi()
    {
        return $this->hasMany(Kompensasi::class);
    }
}
