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
        Schema::create('keuangans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_referensi')->unique();
            $table->foreignId('pembayaran_id')->nullable()->constrained('pembayarans')->nullOnDelete();
            $table->enum('tipe_transaksi', ['pemasukan', 'pengeluaran']);
            $table->enum('sumber_transaksi', ['parkir', 'kompensasi', 'operasional', 'lainnya']);
            $table->decimal('jumlah');
            $table->date('tanggal_transaksi');
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('posted');
            $table->text('deskripsi')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangans');
    }
};
