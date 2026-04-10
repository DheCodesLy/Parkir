<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetodePembayaran extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['nama_metode', 'kode_metode', 'kategori', 'status_aktif', 'urutan'];

    protected $casts = ['status_aktif' => 'boolean', 'urutan' => 'integer'];

}
