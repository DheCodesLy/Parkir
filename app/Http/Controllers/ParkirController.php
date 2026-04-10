<?php

namespace App\Http\Controllers;

use App\Models\JenisPemilik;
use App\Models\Kendaraan;
use App\Models\PemilikKendaraan;
use App\Models\TransaksiParkir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ParkirController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaksiParkir::query()
            ->with([
                'kendaraan:id,pemilik_id,no_polisi,jenis_kendaraan_id',
                'kendaraan.jenisKendaraan:id,nama_jenis_kendaraan',
                'kendaraan.pemilik:id,jenis_pemilik_id',
                'kendaraan.pemilik.jenisPemilik:id,nama_jenis_pemilik',
            ]);

        // Filter Status Parkir
        if ($request->filled('status_parkir')) {
            $query->where('status_parkir', $request->status_parkir);
        }

        // Filter Status Tiket
        if ($request->filled('status_tiket')) {
            $query->where('status_tiket', $request->status_tiket);
        }

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('kode_tiket', 'like', "%{$search}%")
                    ->orWhereHas('kendaraan', function ($kendaraanQuery) use ($search) {
                        $kendaraanQuery->where('no_polisi', 'like', "%{$search}%");
                    });
            });
        }

        // Ambil data dengan Pagination dan rapikan format datanya
        $transaksis = $query
            ->latest('id')
            ->paginate($request->get('per_page', 10))
            ->through(function ($item) {
                return (object) [ // Gunakan (object) agar di Blade bisa dipanggil dengan panah ->
                    'id' => $item->id,
                    'nomor_plat' => $item->kendaraan?->no_polisi,
                    'jenis_kendaraan' => $item->kendaraan?->jenisKendaraan?->nama_jenis_kendaraan,
                    'jenis_pemilik' => $item->kendaraan?->pemilik?->jenisPemilik?->nama_jenis_pemilik,
                    'kode_tiket' => $item->kode_tiket,
                    'status_parkir' => $item->status_parkir,
                    'status_tiket' => $item->status_tiket,
                ];
            });

        // PENTING: Ganti return response()->json(...) menjadi return view(...)
        // Asumsinya file blade kamu ada di folder resources/views/parkir/index.blade.php
        return view('parkir.index', compact('transaksis'));
    }

    public function show($id)
    {
        $transaksi = TransaksiParkir::with([
            'kendaraan',
            'kendaraan.jenisKendaraan',
            'kendaraan.pemilik',
            'kendaraan.pemilik.User:id,name', // Tambahan relasi untuk mengambil nama dari tabel users
            'kendaraan.pemilik.jenisPemilik',
            'dibuatOleh:id,name,email',
            'diperbaruiOleh:id,name,email',
        ])->findOrFail($id);

        return response()->json([
            'message' => 'Detail transaksi parkir berhasil diambil',
            'data' => [
                'id' => $transaksi->id,
                'kode_tiket' => $transaksi->kode_tiket,
                'nomor_plat' => $transaksi->kendaraan?->no_polisi,
                'jenis_kendaraan' => $transaksi->kendaraan?->jenisKendaraan?->nama_jenis_kendaraan,
                'nama_pemilik' => $transaksi->kendaraan?->pemilik?->User?->name, // Mengambil Nama Pemilik
                'jenis_pemilik' => $transaksi->kendaraan?->pemilik?->jenisPemilik?->nama_jenis_pemilik,
                'merk' => $transaksi->kendaraan?->merk,
                'warna' => $transaksi->kendaraan?->warna,
                'catatan_kendaraan' => $transaksi->kendaraan?->catatan,
                'waktu_masuk' => $transaksi->waktu_masuk,
                'waktu_keluar' => $transaksi->waktu_keluar,
                'status_parkir' => $transaksi->status_parkir,
                'status_tiket' => $transaksi->status_tiket,
                'kondisi_kendaraan' => $transaksi->kondisi_kendaraan,
                'alasan_denda' => $this->decodeAlasanDenda($transaksi->alasan_denda),
                'denda_manual' => (float) $transaksi->denda_manual,
                'keterangan' => $transaksi->keterangan,
                'dibuat_oleh' => $transaksi->dibuatOleh,
                'diperbarui_oleh' => $transaksi->diperbaruiOleh,
                'created_at' => $transaksi->created_at,
                'updated_at' => $transaksi->updated_at,
            ],
        ]);
    }

    public function masuk(Request $request)
    {
        $validated = $request->validate([
            'no_polisi' => ['required', 'string', 'max:255'],
            'jenis_kendaraan_id' => ['required', 'exists:jenis_kendaraans,id'],
        ]);

        $transaksi = DB::transaction(function () use ($validated) {
            $noPolisi = $this->normalizeNoPolisi($validated['no_polisi']);

            $kendaraan = Kendaraan::with(['pemilik', 'jenisKendaraan'])
                ->whereRaw('UPPER(REPLACE(no_polisi, " ", "")) = ?', [$noPolisi])
                ->first();

            if (!$kendaraan) {
                $jenisPemilikTamu = JenisPemilik::query()
                    ->where(function ($query) {
                        $query->where('kode_jenis_pemilik', 'tamu')
                            ->orWhereRaw('LOWER(nama_jenis_pemilik) = ?', ['tamu']);
                    })
                    ->firstOrFail();

                $pemilik = PemilikKendaraan::create([
                    'user_id' => null,
                    'jenis_pemilik_id' => $jenisPemilikTamu->id,
                    'status_aktif' => 1,
                ]);

                $kendaraan = Kendaraan::create([
                    'pemilik_id' => $pemilik->id,
                    'no_polisi' => $noPolisi,
                    'jenis_kendaraan_id' => $validated['jenis_kendaraan_id'],
                    'merk' => null,
                    'warna' => null,
                    'catatan' => null,
                    'status_aktif' => 1,
                ]);
            }

            $transaksiAktif = TransaksiParkir::query()
                ->where('kendaraan_id', $kendaraan->id)
                ->where('status_parkir', 'parkir')
                ->whereIn('status_tiket', ['aktif', 'invalid'])
                ->first();

            if ($transaksiAktif) {
                abort(response()->json([
                    'message' => 'Kendaraan ini masih memiliki transaksi parkir aktif',
                ], 422));
            }

            return TransaksiParkir::create([
                'kendaraan_id' => $kendaraan->id,
                'kode_tiket' => $this->generateKodeTiket(),
                'waktu_masuk' => now(),
                'waktu_keluar' => null,
                'status_parkir' => 'parkir',
                'status_tiket' => 'aktif',
                'kondisi_kendaraan' => 'baik',
                'alasan_denda' => null,
                'denda_manual' => 0,
                'keterangan' => null,
                'dibuat_oleh' => Auth::id(),
                'diperbarui_oleh' => null,
            ]);
        });

        $transaksi->load([
            'kendaraan',
            'kendaraan.jenisKendaraan',
            'kendaraan.pemilik',
            'kendaraan.pemilik.jenisPemilik',
        ]);

        return response()->json([
            'message' => 'Kendaraan berhasil masuk parkir',
            'data' => [
                'id' => $transaksi->id,
                'nomor_plat' => $transaksi->kendaraan?->no_polisi,
                'jenis_kendaraan' => $transaksi->kendaraan?->jenisKendaraan?->nama_jenis_kendaraan,
                'jenis_pemilik' => $transaksi->kendaraan?->pemilik?->jenisPemilik?->nama_jenis_pemilik,
                'kode_tiket' => $transaksi->kode_tiket,
                'status_parkir' => $transaksi->status_parkir,
                'status_tiket' => $transaksi->status_tiket,
                'waktu_masuk' => $transaksi->waktu_masuk,
            ],
        ], 201);
    }

    public function keluar(Request $request, $id)
    {
        $validated = $request->validate([
            'tiket_hilang' => ['nullable', 'boolean'],
            'nominal_denda_tiket_hilang' => ['nullable', 'numeric', 'min:0'],
            'denda_manual' => ['nullable', 'array'],
            'denda_manual.*.alasan' => ['required_with:denda_manual', 'string', 'max:255'],
            'denda_manual.*.nominal' => ['required_with:denda_manual', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
            'kondisi_kendaraan' => ['nullable', 'in:baik,rusak,hilang'],
        ]);

        $transaksi = DB::transaction(function () use ($validated, $id) {
            $transaksi = TransaksiParkir::with([
                'kendaraan',
                'kendaraan.jenisKendaraan',
                'kendaraan.pemilik',
                'kendaraan.pemilik.jenisPemilik',
            ])->findOrFail($id);

            if ($transaksi->status_parkir !== 'parkir') {
                abort(response()->json([
                    'message' => 'Transaksi ini tidak dalam status parkir aktif',
                ], 422));
            }

            $tiketHilang = (bool) ($validated['tiket_hilang'] ?? false);
            $nominalDendaTiketHilang = $tiketHilang ? (float) ($validated['nominal_denda_tiket_hilang'] ?? 0) : 0;
            $dendaManualItems = $validated['denda_manual'] ?? [];

            $alasanDenda = [];
            $totalDendaManual = 0;

            if ($tiketHilang) {
                $alasanDenda[] = [
                    'jenis' => 'otomatis',
                    'alasan' => 'Tiket hilang',
                    'nominal' => $nominalDendaTiketHilang,
                ];
            }

            foreach ($dendaManualItems as $item) {
                $nominal = (float) $item['nominal'];
                $totalDendaManual += $nominal;

                $alasanDenda[] = [
                    'jenis' => 'manual',
                    'alasan' => $item['alasan'],
                    'nominal' => $nominal,
                ];
            }

            $statusTiket = $tiketHilang ? 'hilang' : 'nonaktif';

            $transaksi->update([
                'waktu_keluar' => now(),
                'status_parkir' => 'keluar',
                'status_tiket' => $statusTiket,
                'kondisi_kendaraan' => $validated['kondisi_kendaraan'] ?? $transaksi->kondisi_kendaraan,
                'alasan_denda' => count($alasanDenda) ? json_encode($alasanDenda, JSON_UNESCAPED_UNICODE) : null,
                'denda_manual' => $totalDendaManual,
                'keterangan' => $validated['keterangan'] ?? null,
                'diperbarui_oleh' => Auth::id(),
            ]);

            return $transaksi->fresh([
                'kendaraan',
                'kendaraan.jenisKendaraan',
                'kendaraan.pemilik',
                'kendaraan.pemilik.jenisPemilik',
            ]);
        });

        return response()->json([
            'message' => 'Kendaraan berhasil keluar parkir',
            'data' => [
                'id' => $transaksi->id,
                'nomor_plat' => $transaksi->kendaraan?->no_polisi,
                'jenis_kendaraan' => $transaksi->kendaraan?->jenisKendaraan?->nama_jenis_kendaraan,
                'jenis_pemilik' => $transaksi->kendaraan?->pemilik?->jenisPemilik?->nama_jenis_pemilik,
                'kode_tiket' => $transaksi->kode_tiket,
                'status_parkir' => $transaksi->status_parkir,
                'status_tiket' => $transaksi->status_tiket,
                'waktu_masuk' => $transaksi->waktu_masuk,
                'waktu_keluar' => $transaksi->waktu_keluar,
                'kondisi_kendaraan' => $transaksi->kondisi_kendaraan,
                'alasan_denda' => $this->decodeAlasanDenda($transaksi->alasan_denda),
                'denda_manual' => (float) $transaksi->denda_manual,
                'keterangan' => $transaksi->keterangan,
            ],
        ]);
    }

    private function generateKodeTiket(): string
    {
        do {
            $kode = 'TKT-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));
        } while (TransaksiParkir::where('kode_tiket', $kode)->exists());

        return $kode;
    }

    private function normalizeNoPolisi(string $noPolisi): string
    {
        return strtoupper(str_replace(' ', '', trim($noPolisi)));
    }

    private function decodeAlasanDenda(?string $alasanDenda): array
    {
        if (!$alasanDenda) {
            return [];
        }

        $decoded = json_decode($alasanDenda, true);

        return is_array($decoded) ? $decoded : [];
    }
}
