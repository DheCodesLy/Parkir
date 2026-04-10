<?php

namespace Database\Seeders;

use App\Models\JenisPemilik;
use Illuminate\Database\Seeder;

class JenisPemilikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_jenis_pemilik' => 'Tamu',
                'kode_jenis_pemilik' => 'tamu',
                'deskripsi' => 'Khusus pengunjung',
                'status_aktif' => true,
            ],
            [
                'nama_jenis_pemilik' => 'Petugas',
                'kode_jenis_pemilik' => 'Petugas',
                'deskripsi' => 'Khusus pengelola    ',
                'status_aktif' => true,
            ]
        ];

        foreach ($data as $key) {
            JenisPemilik::updateOrCreate(
                ['kode_jenis_pemilik' => $key['kode_jenis_pemilik']],
                $key
            );
        }
    }
}
