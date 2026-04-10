<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembayaran')->unique();
            $table->foreignId('transaksi_parkir_id')->unique()->constrained('transaksi_parkirs')->restrictOnDelete();
            $table->foreignId('klaim_kompensasi_id')->nullable()->constrained('kompensasis')->cascadeOnDelete();
            $table->foreignId('metode_pembayaran_id')->constrained('metode_pembayarans')->restrictOnDelete();
            $table->decimal('tarif_dasar');
            $table->decimal('denda');
            $table->decimal('kompensasi');
            $table->decimal('total_bayar');
            $table->decimal('jumlah_dibayar');
            $table->decimal('kembalian');
            $table->enum('status_pembayaran', ['pending', 'dibayar', 'batal'])->default('pending');
            $table->dateTime('dibayar_pada')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
