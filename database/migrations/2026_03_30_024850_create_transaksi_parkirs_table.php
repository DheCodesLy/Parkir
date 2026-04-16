

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
            Schema::create('transaksi_parkirs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kendaraan_id')->constrained('kendaraans');
                $table->foreignId('lahan_parkir_id')->constrained('lahan_parkirs')->restrictOnDelete();
                $table->string('kode_tiket')->unique();
                $table->dateTime('waktu_masuk');
                $table->dateTime('waktu_keluar')->nullable();
                $table->enum('status_parkir',['parkir', 'keluar', 'batal'])->default('parkir');
                $table->enum('status_tiket', ['aktif', 'nonaktif', 'invalid', 'hilang'])->default('aktif');
                $table->enum('kondisi_kendaraan', ['baik', 'rusak', 'hilang'])->default('baik');
                $table->text('alasan_denda')->nullable();
                $table->decimal('denda_manual')->default(0);
                $table->text('keterangan')->nullable();
                $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
                $table->foreignId('diperbarui_oleh')->nullable()->constrained('users')->restrictOnDelete();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_parkirs');
    }
};
