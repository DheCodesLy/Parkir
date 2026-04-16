<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LahanParkirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lahan = [
            ['nama_lahan' => 'Basement Utama (B1)', 'kapasitas' => 100, 'sisa_slot' => 45],
            ['nama_lahan' => 'Lantai G (Drop Off)', 'kapasitas' => 20, 'sisa_slot' => 2],
            ['nama_lahan' => 'Area Parkir Terbuka Utara', 'kapasitas' => 150, 'sisa_slot' => 120],
            ['nama_lahan' => 'Parkir VIP Sayap Barat', 'kapasitas' => 15, 'sisa_slot' => 5],
            ['nama_lahan' => 'Gedung Parkir Lt. 2', 'kapasitas' => 80, 'sisa_slot' => 30],
            ['nama_lahan' => 'Gedung Parkir Lt. 3', 'kapasitas' => 80, 'sisa_slot' => 80],
            ['nama_lahan' => 'Area Karyawan (Belakang)', 'kapasitas' => 50, 'sisa_slot' => 10],
            ['nama_lahan' => 'Basement Khusus Motor (B2)', 'kapasitas' => 300, 'sisa_slot' => 150],
            ['nama_lahan' => 'Loading Dock Logistik', 'kapasitas' => 10, 'sisa_slot' => 8],
            ['nama_lahan' => 'Parkir Disabilitas (Depan)', 'kapasitas' => 5, 'sisa_slot' => 3],
        ];

        foreach ($lahan as $item) {
            DB::table('lahan_parkirs')->insert([
                'nama_lahan' => $item['nama_lahan'],
                'kapasitas' => $item['kapasitas'],
                'sisa_slot' => $item['sisa_slot'],
                'status_aktif' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
