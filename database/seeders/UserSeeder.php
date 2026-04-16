<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allUsers = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('pass_admin'),
                'alamat' => 'Kantor Pusat',
                'status_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Petugas',
                'email' => 'petugas@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('pass_petugas'),
                'alamat' => 'Cabang 1',
                'status_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // 2. Daftar Pengguna Parkir (Customer)
        $customers = [
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'alamat' => 'Jl. Merdeka No. 10, Jakarta'],
            ['name' => 'Siti Aminah', 'email' => 'siti@example.com', 'alamat' => 'Sunter, Jakarta Utara'],
            ['name' => 'Agus Prayogo', 'email' => 'agus@example.com', 'alamat' => 'Dago, Bandung'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@example.com', 'alamat' => 'Sleman, Yogyakarta'],
            ['name' => 'Rian Hidayat', 'email' => 'rian@example.com', 'alamat' => 'Rungkut, Surabaya'],
            ['name' => 'Eka Putri', 'email' => 'eka@example.com', 'alamat' => 'Denpasar, Bali'],
            ['name' => 'Fajar Nugraha', 'email' => 'fajar@example.com', 'alamat' => 'Banjarmasin, Kalsel'],
            ['name' => 'Indah Permata', 'email' => 'indah@example.com', 'alamat' => 'Medan Baru, Medan'],
            ['name' => 'Hendra Wijaya', 'email' => 'hendra@example.com', 'alamat' => 'Makassar, Sulsel'],
            ['name' => 'Lani Marlina', 'email' => 'lani@example.com', 'alamat' => 'Bojongsoang, Bandung'],
        ];

        // 3. Masukkan data customer ke dalam array utama $allUsers
        foreach ($customers as $u) {
            $allUsers[] = [
                'name' => $u['name'],
                'email' => $u['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'), // Default password untuk customer
                'alamat' => $u['alamat'],
                'status_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 4. Eksekusi insert satu kali untuk semua data
        DB::table('users')->insert($allUsers);
    }
}
