<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KapastitasParkirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua ID yang tersedia di tabel induk
        $lahanIds = DB::table('lahan_parkirs')->pluck('id')->toArray();
        $jenisPemilikIds = DB::table('jenis_pemiliks')->pluck('id')->toArray();
        $jenisKendaraanIds = DB::table('jenis_kendaraans')->pluck('id')->toArray();

        // Pastikan tabel induk tidak kosong sebelum seeding
        if (empty($lahanIds) || empty($jenisPemilikIds) || empty($jenisKendaraanIds)) {
            return;
        }

        foreach ($lahanIds as $id) {
            DB::table('kapasitas_parkirs')->insert([
                [
                    'lahan_parkir_id' => $id,
                    // Mengambil ID acak dari data yang benar-benar ada di database
                    'jenis_pemilik_id' => $jenisPemilikIds[array_rand($jenisPemilikIds)],
                    'jenis_kendaraan_id' => $jenisKendaraanIds[array_rand($jenisKendaraanIds)],
                    'status_aktif' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'lahan_parkir_id' => $id,
                    'jenis_pemilik_id' => $jenisPemilikIds[array_rand($jenisPemilikIds)],
                    'jenis_kendaraan_id' => $jenisKendaraanIds[array_rand($jenisKendaraanIds)],
                    'status_aktif' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}
