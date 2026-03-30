<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    public $fillable = ['user_id', 'role_id'];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
