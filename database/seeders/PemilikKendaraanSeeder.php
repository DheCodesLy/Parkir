<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PemilikKendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // Ambil ID jenis pemilik 'tamu'
    $jenisTamu = DB::table('jenis_pemiliks')->where('kode_jenis_pemilik', 'tamu')->first();

    // Ambil semua user kecuali Admin/Petugas (asumsi ID > 2 atau berdasarkan email)
    $users = DB::table('users')->whereNotIn('email', ['admin@example.com', 'petugas@example.com'])->get();

    foreach ($users as $user) {
        DB::table('pemilik_kendaraans')->insert([
            'user_id' => $user->id,
            'jenis_pemilik_id' => $jenisTamu->id,
            'status_aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
}
