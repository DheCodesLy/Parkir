<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Isi tabel master/induk dulu
            JenisKendaraanSeeder::class,
            JenisPemilikSeeder::class,
            LahanParkirSeeder::class,
            UserSeeder::class,

            // 2. Baru isi tabel yang punya relasi (Foreign Key)
            PemilikKendaraanSeeder::class,
            KendaraanSeeder::class,
            KapastitasParkirSeeder::class, // Harus setelah Lahan, Jenis Pemilik, dan Jenis Kendaraan
        ]);
    }
}
