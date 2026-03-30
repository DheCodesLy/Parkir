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
        Schema::create('tarif_parkirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_kendaraan_id')->constrained('jenis_kendaraans')->cascadeOnDelete();
            $table->foreignId('jenis_pemilik_id')->constrained('jenis_pemiliks')->cascadeOnDelete();
            $table->decimal('biaya_masuk')->default(0);
            $table->decimal('biaya_per_jam');
            $table->decimal('biaya_maksimal')->nullable();
            $table->integer('gratis_menit')->default(0);
            $table->boolean('status_aktif')->default(true);
            $table->dateTime('masa_berlaku');
            $table->datetime('selesai_berlaku');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarif_parkirs');
    }
};
