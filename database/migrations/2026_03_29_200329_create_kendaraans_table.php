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
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemilik_id')->constrained('pemilik_kendaraans')->cascadeOnDelete();
            $table->string('no_polisi')->nullable()->unique();
            $table->foreignId('jenis_kendaraan')->constrained('jenis_kendaraans')->cascadeOnDelete();
            $table->string('merk')->nullable();
            $table->string('warna')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
