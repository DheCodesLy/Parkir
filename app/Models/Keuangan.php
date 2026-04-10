<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keuangan extends Model
{
    use SoftDeletes;

    protected $fillable = ['kode_referensi', 'pembayaran_id', 'tipe_transaksi', 'sumber_transaksi', 'jumlah', 'tanggal_transaksi', 'status', 'deskripsi', 'dibuat_oleh'];

    protected function casts()
    {
        return ['jumlah' => 'decimal:2', 'tanggal_transaksi' => 'date'];
    }

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
