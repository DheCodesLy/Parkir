
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
        Schema::create('kompensasis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_klaim')->unique();
            $table->dateTime('tanggal_kejadian')->nullable();
            $table->foreignId('transaksi_parkir_id')->constrained('transaksi_parkirs')->cascadeOnDelete();
            $table->enum('jenis_kompensasi', ['rusak', 'kehilangan']);
            $table->enum('tipe_kompensasi', ['uang', 'barang', 'lainnya']);
            $table->decimal('nominal_disetujui', 12, 2)->nullable();
            $table->string('bukti_pengaju')->nullable();
            $table->string('nama_pengaju');
            $table->string('no_hp_pengaju')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status_pengajuan', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->text('catatan_verifikator')->nullable();
            $table->string('bukti_kompensasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kompensasis');
    }
};
