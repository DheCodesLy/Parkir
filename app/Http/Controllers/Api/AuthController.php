<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\TransaksiParkir;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * TAHAP 1: Cek Status
     */
    public function checkStatus(Request $request)
{
    $request->validate([
        'type' => 'required|in:ticket,plate',
        'value' => 'required|string',
    ]);

    $type = $request->type;
    $value = $request->value;

    if ($type === 'ticket') {
        $transaksi = TransaksiParkir::with(['Kendaraan.Pemilik.User'])
            ->where('kode_tiket', $value)
            ->where('status_tiket', 'aktif')
            ->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Tiket tidak ditemukan atau sudah tidak aktif'], 404);
        }

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
        // --- PERBAIKAN DI SINI ---
        $cleanInput = strtoupper(str_replace(' ', '', $value));

        $kendaraan = Kendaraan::with(['Pemilik.User'])
            // Tambahkan UPPER agar pencarian di DB lebih akurat
            ->whereRaw("REPLACE(UPPER(no_polisi), ' ', '') = ?", [$cleanInput])
            ->first();

        if (!$kendaraan) {
            return response()->json(['message' => 'Plat nomor ' . $value . ' tidak ditemukan di sistem'], 404);
        }

        $user = $kendaraan->Pemilik->User ?? null;

        if (!$user) {
            return response()->json([
                'message' => 'Kendaraan ditemukan, tetapi belum terhubung ke akun user. Silakan login menggunakan tiket terlebih dahulu.',
            ], 404);
        }

        // --- INI YANG TADI HILANG ---
        return response()->json([
            'status' => 'LOGIN',
            'identifier' => $kendaraan->no_polisi,
            'type' => 'plate',
            'data' => [
                'no_polisi' => $kendaraan->no_polisi,
                'merk' => $kendaraan->merk,
            ]
        ], 200);
    }
}

    public function authenticate(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'identifier' => 'required|string',
            'type' => 'required|in:ticket,plate',
            'password' => 'required|string|min:6',
            'name'     => 'nullable|string|max:255', // Wajib jika Register
            'email'    => 'nullable|email',
        ]);

        try {
            $pemilik = null;

            // 2. Cari Pemilik berdasarkan Tipe (Ticket atau Plate)
            if ($request->type === 'ticket') {
                // Normalisasi Tiket: Kapital & Hapus Spasi
                $cleanTicket = strtoupper(str_replace(' ', '', $request->identifier));

                $transaksi = TransaksiParkir::with(['Kendaraan.Pemilik.User'])
                    ->whereRaw("REPLACE(UPPER(kode_tiket), ' ', '') = ?", [$cleanTicket])
                    ->where('status_tiket', 'aktif')
                    ->first();

                if (!$transaksi) {
                    return response()->json(['message' => 'Tiket tidak ditemukan atau sudah tidak aktif'], 404);
                }

                $pemilik = $transaksi->Kendaraan->Pemilik;
            } else {
                // Normalisasi Plat Nomor: Kapital & Hapus Spasi
                $cleanPlate = strtoupper(str_replace(' ', '', $request->identifier));

                $kendaraan = Kendaraan::with(['Pemilik.User'])
                    ->whereRaw("REPLACE(UPPER(no_polisi), ' ', '') = ?", [$cleanPlate])
                    ->first();

                if (!$kendaraan) {
                    return response()->json(['message' => 'Data kendaraan tidak ditemukan'], 404);
                }

                $pemilik = $kendaraan->Pemilik;
            }

            // Safety check jika relasi database bermasalah
            if (!$pemilik) {
                return response()->json(['message' => 'Data pemilik kendaraan gagal dimuat'], 404);
            }

            // 3. LOGIKA REGISTER (Jika Pemilik Belum Memiliki Akun User)
            if (!$pemilik->user_id) {
                // Validasi nama wajib untuk pengguna baru
                if (!$request->name) {
                    return response()->json(['message' => 'Nama lengkap wajib diisi untuk pendaftaran baru'], 422);
                }

                // Buat fallback email jika tidak diisi: d7890opm@parkirpro.com
                $identifierClean = strtolower(str_replace([' ', '-'], '', $request->identifier));
                $defaultEmail = $identifierClean . '@parkirpro.com';
                $emailToUse = $request->email ?? $defaultEmail;

                // Pastikan email unik, jika sudah ada, tambahkan timestamp
                if (User::where('email', $emailToUse)->exists()) {
                    $emailToUse = $identifierClean . '.' . time() . '@parkirpro.com';
                }

                // Database Transaction agar data User dan Pemilik sinkron
                $user = DB::transaction(function () use ($request, $emailToUse, $pemilik) {
                    $newUser = User::create([
                        'name' => $request->name,
                        'email' => $emailToUse,
                        'password' => Hash::make($request->password),
                        'status_aktif' => 1,
                    ]);

                    $pemilik->update(['user_id' => $newUser->id]);
                    return $newUser;
                });

                $message = 'Akun berhasil dibuat dan otomatis masuk';
            }

            // 4. LOGIKA LOGIN (Jika Sudah Memiliki Akun)
            else {
                $user = $pemilik->User;

                if (!$user) {
                    return response()->json(['message' => 'Gagal sinkronisasi data user'], 404);
                }

                // Cek Password
                if (!Hash::check($request->password, $user->password)) {
                    return response()->json(['message' => 'Password yang Anda masukkan salah'], 401);
                }

                $message = 'Berhasil masuk ke sistem';
            }

            // 5. Generate Sanctum Token
            $token = $user->createToken('parkirpro_auth_token')->plainTextToken;

            return response()->json([
                'message' => $message,
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ], 200);

        } catch (\Throwable $e) {
            // Log error untuk kebutuhan debugging backend
            \Log::error('Auth Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan sistem pada server',
                'error' => $e->getMessage() // Hapus line ini jika sudah production
            ], 500);
        }
    }
}
