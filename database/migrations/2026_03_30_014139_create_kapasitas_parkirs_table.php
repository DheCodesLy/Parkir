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
        Schema::create('kapasitas_parkirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lahan_parkir_id')->constrained('lahan_parkirs')->cascadeOnDelete();
            $table->foreignId('jenis_pemilik_id')->constrained('jenis_pemiliks')->cascadeOnDelete();
            $table->foreignId('jenis_kendaraan_id')->constrained('jenis_kendaraans')->cascadeOnDelete();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kapasitas_parkirs');
    }
};
