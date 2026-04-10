<?php

namespace Database\Seeders;

use App\Models\JenisKendaraan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisKendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_jenis_kendaraan' => 'Mobil',
                'kode_jenis_kendaraan' => 'mobil',
                'deskripsi' => 'Kendaraan roda empat atau lebih',
                'status_aktif' => true,
            ],
            [
                'nama_jenis_kendaraan' => 'Motor',
                'kode_jenis_kendaraan' => 'motor',
                'deskripsi' => 'Kendaraan roda dua',
                'status_aktif' => true,
            ],
        ];

        foreach ($data as $item) {
            JenisKendaraan::updateOrCreate(
                ['kode_jenis_kendaraan' => $item['kode_jenis_kendaraan']],
                $item
            );
        }
    }
}
