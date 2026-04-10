<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['nama_role', 'kode_role', 'deskripsi'];

    public function UserRole()
    {
        return $this->hasMany(UserRole::class);
    }
}
