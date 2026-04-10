<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = ['kode_pembayaran', 'transaksi_parkir_id', 'klaim_kompensasi_id', 'metode_pembayaran_id', 'tarif_dasar', 'denda', 'kompensasi', 'total_bayar', 'jumlah_dibayar', 'kembalian', 'status_pembayaran', 'dibayar_pada', 'keterangan', 'diproses_oleh'];

    protected $casts = ['transaksi_parkir_id' => 'integer', 'klaim_kompensasi_id' => 'integer', 'metode_pembayaran_id' => 'integer', 'tarif_dasar' => 'decimal:2', 'denda' => 'decimal:2', 'kompensasi' => 'decimal:2', 'total_bayar' => 'decimal:2', 'jumlah_dibayar' => 'decimal:2', 'kembalian' => 'decimal:2', 'dibayar_pada' => 'datetime', 'diproses_oleh' => 'integer'];

    public function transaksiParkir()
    {
        return $this->belongsTo(TransaksiParkir::class, 'transaksi_parkir_id');
    }

    public function klaimKompensasi()
    {
        return $this->belongsTo(Kompensasi::class, 'klaim_kompensasi_id');
    }

    public function metodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class, 'metode_pembayaran_id');
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
