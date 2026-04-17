<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\TransaksiParkir;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function checkStatus(Request $request)
    {
        // 1. Validasi Input (Otomatis return error 422 JSON jika gagal)
        $request->validate([
            'type' => 'required|in:ticket,plate',
            'value' => 'required|string', // berisi kode_tiket atau no_polisi
        ]);

        // 2. Definisikan variabel agar aman digunakan
        $type = $request->type;
        $value = $request->value;

        if ($type === 'ticket') {
            // Cari transaksi yang tiketnya aktif
            $transaksi = TransaksiParkir::with(['Kendaraan.Pemilik.User'])
                ->where('kode_tiket', $value)
                ->where('status_tiket', 'aktif')
                ->first();

            if (!$transaksi) {
                return response()->json(['message' => 'Tiket tidak ditemukan atau sudah tidak aktif'], 404);
            }

            // Ambil data user jika sudah ada
            $user = $transaksi->Kendaraan->Pemilik->User ?? null;

            return response()->json([
                'status' => $user ? 'LOGIN' : 'REGISTER',
                'identifier' => $value,
                'type' => 'ticket',
                'data' => [
                    'no_polisi' => $transaksi->Kendaraan->no_polisi,
                    'merk' => $transaksi->Kendaraan->merk,
                ]
            ]);

        } else {
            // Logika Plat Nomor (Hanya Login)
            $kendaraan = Kendaraan::with(['Pemilik.User'])
                ->where('no_polisi', $value)
                ->first();

            if (!$kendaraan || !$kendaraan->Pemilik->User) {
                return response()->json(['message' => 'Plat nomor tidak terdaftar sebagai user'], 404);
            }

            return response()->json([
                'status' => 'LOGIN',
                'identifier' => $value,
                'type' => 'plate',
                'data' => [
                    'no_polisi' => $kendaraan->no_polisi,
                    'merk' => $kendaraan->merk,
                ]
            ]);
        }
    }

    /**
     * TAHAP 2: Eksekusi Login atau Register
     */
    public function authenticate(Request $request)
    {
        // 1. Validasi dasar
        $request->validate([
            'identifier' => 'required|string',
            'type' => 'required|in:ticket,plate',
            'password' => 'required|string|min:6',
        ]);

        // 2. Cari pemilik kendaraan berdasarkan tipe login
        if ($request->type === 'ticket') {
            $transaksi = TransaksiParkir::with('Kendaraan.Pemilik')
                ->where('kode_tiket', $request->identifier)
                ->where('status_tiket', 'aktif')
                ->firstOrFail();
            $pemilik = $transaksi->Kendaraan->Pemilik;
        } else {
            $kendaraan = Kendaraan::with('Pemilik')
                ->where('no_polisi', $request->identifier)
                ->firstOrFail();
            $pemilik = $kendaraan->Pemilik;
        }

        // 3. PROSES REGISTER (Jika tiket baru & belum ada user_id)
        if (!$pemilik->user_id) {
            // Wajibkan input nama khusus saat register
            $request->validate(['name' => 'required|string']);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email ?? $request->identifier . '@parkirpro.com', // fallback email jika tidak diisi
                'password' => Hash::make($request->password),
                'status_aktif' => 1
            ]);

            // Update user_id di tabel pemilik_kendaraan
            $pemilik->update(['user_id' => $user->id]);

        } else {
            // 4. PROSES LOGIN
            $user = $pemilik->User;
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Password salah'], 401);
            }
        }

        // 5. Generate Token Sanctum
        $token = $user->createToken('parkir_token')->plainTextToken;

        return response()->json([
            'message' => 'Autentikasi Berhasil',
            'user' => $user,
            'token' => $token
        ]);
    }
}
