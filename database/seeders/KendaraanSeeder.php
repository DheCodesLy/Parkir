<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mobilId = DB::table('jenis_kendaraans')->where('kode_jenis_kendaraan', 'mobil')->value('id');
        $motorId = DB::table('jenis_kendaraans')->where('kode_jenis_kendaraan', 'motor')->value('id');
        $pemiliks = DB::table('pemilik_kendaraans')->get();

        $dataKendaraan = [
            ['no' => 'B 1234 AB', 'merk' => 'Toyota Avanza', 'warna' => 'Hitam', 'tipe' => $mobilId],
            ['no' => 'D 9981 XYZ', 'merk' => 'Honda Vario', 'warna' => 'Merah', 'tipe' => $motorId],
            ['no' => 'F 4421 BC', 'merk' => 'Mitsubishi Xpander', 'warna' => 'Putih', 'tipe' => $mobilId],
            ['no' => 'B 3030 SSS', 'merk' => 'Yamaha NMAX', 'warna' => 'Abu-abu', 'tipe' => $motorId],
            ['no' => 'L 1122 AA', 'merk' => 'Honda HR-V', 'warna' => 'Silver', 'tipe' => $mobilId],
            ['no' => 'B 6789 KLO', 'merk' => 'Honda Beat', 'warna' => 'Biru', 'tipe' => $motorId],
            ['no' => 'D 2231 VFF', 'merk' => 'Suzuki Ertiga', 'warna' => 'Cokelat', 'tipe' => $mobilId],
            ['no' => 'F 9012 GG', 'merk' => 'Kawasaki Ninja', 'warna' => 'Hijau', 'tipe' => $motorId],
            ['no' => 'B 5543 QW', 'merk' => 'Daihatsu Sigra', 'warna' => 'Hitam', 'tipe' => $mobilId],
            ['no' => 'D 8872 PH', 'merk' => 'Honda PCX', 'warna' => 'Putih', 'tipe' => $motorId],
        ];

        foreach ($pemiliks as $index => $pemilik) {
            // Setiap pemilik mendapatkan 1 kendaraan dari list di atas
            if (isset($dataKendaraan[$index])) {
                DB::table('kendaraans')->insert([
                    'pemilik_id' => $pemilik->id,
                    'no_polisi' => $dataKendaraan[$index]['no'],
                    'jenis_kendaraan_id' => $dataKendaraan[$index]['tipe'],
                    'merk' => $dataKendaraan[$index]['merk'],
                    'warna' => $dataKendaraan[$index]['warna'],
                    'catatan' => 'Parkir rutin',
                    'status_aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
