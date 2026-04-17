<?php

namespace App\Http\Controllers;

use App\Models\JenisKendaraan;
use App\Models\JenisPemilik;
use App\Models\KapasitasParkir;
use App\Models\Kendaraan;
use App\Models\LahanParkir;
use App\Models\PemilikKendaraan;
use App\Models\TransaksiParkir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

        // Fitur Pencarian (Kebal spasi dan strip)
        if ($request->filled('search')) {
            $search = strtoupper(preg_replace('/[^A-Z0-9]/', '', $request->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw("UPPER(REPLACE(kode_tiket, '-', '')) LIKE ?", ["%{$search}%"])
                    ->orWhereHas('kendaraan', function ($kQuery) use ($search) {
                        $kQuery->whereRaw("UPPER(REPLACE(REPLACE(no_polisi, ' ', ''), '-', '')) LIKE ?", ["%{$search}%"]);
                    });
            });
        }

        // Ambil data dengan Pagination dan rapikan format datanya
        $transaksis = $query
            ->latest('id')
            ->paginate($request->get('per_page', 10))
            ->through(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'nomor_plat' => $item->kendaraan?->no_polisi,
                    'jenis_kendaraan' => $item->kendaraan?->jenisKendaraan?->nama_jenis_kendaraan,
                    'jenis_pemilik' => $item->kendaraan?->pemilik?->jenisPemilik?->nama_jenis_pemilik,
                    'kode_tiket' => $item->kode_tiket,
                    'status_parkir' => $item->status_parkir,
                    'status_tiket' => $item->status_tiket,
                ];
            });

        return view('parkir.index', compact('transaksis'));
    }

    public function show($id)
    {
        $transaksi = TransaksiParkir::with([
            'kendaraan',
            'kendaraan.jenisKendaraan',
            'kendaraan.pemilik',
            'kendaraan.pemilik.User:id,name',
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
                'nama_pemilik' => $transaksi->kendaraan?->pemilik?->User?->name,
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

    public function formMasuk(Request $request)
    {
        $daftarLahan = LahanParkir::query()
            ->where('status_aktif', true)
            ->orderBy('nama_lahan')
            ->get(['id', 'nama_lahan', 'kapasitas', 'sisa_slot']);

        $lahanTerpilihId = (int) $request->session()->get('parkir_masuk.lahan_parkir_id');

        $jenisKendaraan = collect();
        $selectedJenisKendaraanId = old('jenis_kendaraan_id');

        if ($lahanTerpilihId) {
            $jenisKendaraan = $this->getJenisKendaraanLahan($lahanTerpilihId);

            if ($jenisKendaraan->count() === 1 && !$selectedJenisKendaraanId) {
                $selectedJenisKendaraanId = $jenisKendaraan->first()->id;
            }
        }

        return view('parkir.parkir-masuk', [
            'daftarLahan' => $daftarLahan,
            'lahanTerpilihId' => $lahanTerpilihId,
            'jenisKendaraan' => $jenisKendaraan,
            'selectedJenisKendaraanId' => $selectedJenisKendaraanId,
        ]);
    }

    public function pilihLahan(Request $request)
    {
        $validated = $request->validate([
            'lahan_parkir_id' => [
                'required',
                'integer',
                Rule::exists('lahan_parkirs', 'id')->where(
                    fn ($query) => $query->where('status_aktif', true)
                ),
            ],
        ]);

        $lahan = LahanParkir::query()
            ->select('id', 'nama_lahan', 'kapasitas', 'sisa_slot')
            ->findOrFail($validated['lahan_parkir_id']);

        $jenisKendaraan = $this->getJenisKendaraanLahan($lahan->id)->values();

        if ($jenisKendaraan->isEmpty()) {
            throw ValidationException::withMessages([
                'lahan_parkir_id' => 'Lahan ini belum punya jenis kendaraan aktif.',
            ]);
        }

        $request->session()->put('parkir_masuk.lahan_parkir_id', $lahan->id);
        $request->session()->put('parkir_masuk.nama_lahan', $lahan->nama_lahan);

        return response()->json([
            'success' => true,
            'data' => [
                'lahan' => $lahan,
                'jenis_kendaraans' => $jenisKendaraan,
                'auto_selected' => $jenisKendaraan->count() === 1,
                'selected_id' => $jenisKendaraan->count() === 1
                    ? $jenisKendaraan->first()->id
                    : null,
            ],
        ]);
    }

    public function masuk(Request $request)
    {
        $validated = $request->validate([
            'lahan_parkir_id' => [
                'required',
                'integer',
                Rule::exists('lahan_parkirs', 'id')->where(
                    fn ($query) => $query->where('status_aktif', true)
                ),
            ],
            'no_polisi' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/',
            ],
            'jenis_kendaraan_id' => ['nullable', 'integer', 'exists:jenis_kendaraans,id'],
        ], [
            'jenis_kendaraan_id.exists' => 'Jenis kendaraan tidak valid.',
            'no_polisi.regex' => 'Format plat harus seperti AB 5678 RT.',
        ]);

        try {
            $hasil = DB::transaction(function () use ($validated, $request) {
                // Gunakan fungsi normalisasi yang menjaga format parkir standar
                $noPolisi = $this->normalizeNoPolisi($validated['no_polisi']);
                $waktuMasuk = now();

                $lahan = LahanParkir::query()
                    ->lockForUpdate()
                    ->findOrFail($validated['lahan_parkir_id']);

                if (!$lahan->status_aktif) {
                    throw ValidationException::withMessages([
                        'lahan_parkir_id' => 'Lahan parkir tidak aktif.',
                    ]);
                }

                if ((int) $lahan->sisa_slot < 1) {
                    throw ValidationException::withMessages([
                        'lahan_parkir_id' => 'Lahan parkir sudah penuh.',
                    ]);
                }

                $jenisKendaraanTersedia = KapasitasParkir::query()
                    ->where('lahan_parkir_id', $lahan->id)
                    ->where('status_aktif', true)
                    ->distinct()
                    ->pluck('jenis_kendaraan_id')
                    ->map(fn ($id) => (int) $id)
                    ->values();

                if ($jenisKendaraanTersedia->isEmpty()) {
                    throw ValidationException::withMessages([
                        'lahan_parkir_id' => 'Lahan ini belum memiliki jenis kendaraan aktif.',
                    ]);
                }

                $jenisKendaraanId = isset($validated['jenis_kendaraan_id'])
                    ? (int) $validated['jenis_kendaraan_id']
                    : null;

                if (!$jenisKendaraanId && $jenisKendaraanTersedia->count() === 1) {
                    $jenisKendaraanId = (int) $jenisKendaraanTersedia->first();
                }

                if (!$jenisKendaraanId || !$jenisKendaraanTersedia->contains($jenisKendaraanId)) {
                    throw ValidationException::withMessages([
                        'jenis_kendaraan_id' => 'Jenis kendaraan tidak tersedia pada lahan yang dipilih.',
                    ]);
                }

                // Pencarian kendaraan yang kebal terhadap spasi/format aneh
                $cleanNoPolisi = preg_replace('/[^A-Z0-9]/', '', $noPolisi);

                $kendaraan = Kendaraan::query()
                    ->with('pemilik')
                    ->lockForUpdate()
                    ->whereRaw("UPPER(REPLACE(REPLACE(no_polisi, ' ', ''), '-', '')) = ?", [$cleanNoPolisi])
                    ->first();

                if ($kendaraan && (int) $kendaraan->jenis_kendaraan_id !== $jenisKendaraanId) {
                    throw ValidationException::withMessages([
                        'jenis_kendaraan_id' => 'Jenis kendaraan tidak sesuai dengan data kendaraan terdaftar.',
                    ]);
                }

                if (!$kendaraan) {
                    $jenisPemilikTamu = JenisPemilik::query()
                        ->where(function ($query) {
                            $query->where('kode_jenis_pemilik', 'tamu')
                                ->orWhereRaw('LOWER(nama_jenis_pemilik) = ?', ['tamu']);
                        })
                        ->first();

                    if (!$jenisPemilikTamu) {
                        throw ValidationException::withMessages([
                            'no_polisi' => 'Jenis pemilik tamu tidak ditemukan.',
                        ]);
                    }

                    $pemilik = PemilikKendaraan::create([
                        'user_id' => null,
                        'jenis_pemilik_id' => $jenisPemilikTamu->id,
                        'status_aktif' => 1,
                    ]);

                    $kendaraan = Kendaraan::create([
                        'pemilik_id' => $pemilik->id,
                        'no_polisi' => $noPolisi,
                        'jenis_kendaraan_id' => $jenisKendaraanId,
                        'merk' => null,
                        'warna' => null,
                        'catatan' => null,
                        'status_aktif' => 1,
                    ]);

                    $jenisPemilikId = $jenisPemilikTamu->id;
                } else {
                    $jenisPemilikId = $kendaraan->pemilik?->jenis_pemilik_id;
                }

                if (!$jenisPemilikId) {
                    throw ValidationException::withMessages([
                        'no_polisi' => 'Jenis pemilik kendaraan tidak ditemukan.',
                    ]);
                }

                $kapasitasValid = KapasitasParkir::query()
                    ->where('lahan_parkir_id', $lahan->id)
                    ->where('jenis_pemilik_id', $jenisPemilikId)
                    ->where('jenis_kendaraan_id', $jenisKendaraanId)
                    ->where('status_aktif', true)
                    ->exists();

                if (!$kapasitasValid) {
                    throw ValidationException::withMessages([
                        'jenis_kendaraan_id' => 'Kombinasi lahan, pemilik, dan kendaraan tidak diizinkan.',
                    ]);
                }

                $transaksiAktif = TransaksiParkir::query()
                    ->where('kendaraan_id', $kendaraan->id)
                    ->where('status_parkir', 'parkir')
                    ->whereIn('status_tiket', ['aktif', 'invalid'])
                    ->lockForUpdate()
                    ->exists();

                if ($transaksiAktif) {
                    throw ValidationException::withMessages([
                        'no_polisi' => 'Kendaraan ini masih memiliki transaksi parkir aktif.',
                    ]);
                }

                $transaksi = TransaksiParkir::create([
                    'kendaraan_id' => $kendaraan->id,
                    'lahan_parkir_id' => $lahan->id,
                    'kode_tiket' => $this->generateKodeTiket(),
                    'waktu_masuk' => $waktuMasuk,
                    'dibuat_oleh' => Auth::id(),
                ]);

                $lahan->decrement('sisa_slot');

                $request->session()->put('parkir_masuk.lahan_parkir_id', $lahan->id);
                $request->session()->put('parkir_masuk.nama_lahan', $lahan->nama_lahan);

                $namaJenisKendaraan = JenisKendaraan::query()
                    ->whereKey($jenisKendaraanId)
                    ->value('nama_jenis_kendaraan');

                return [
                    'transaksi' => $transaksi,
                    'ticket_data' => [
                        'kode_tiket' => $transaksi->kode_tiket,
                        'waktu_masuk' => $waktuMasuk->format('d M Y H:i:s'),
                        'no_polisi' => $kendaraan->no_polisi,
                        'jenis_kendaraan' => $namaJenisKendaraan,
                        'nama_lahan' => $lahan->nama_lahan,
                    ],
                ];
            });

            return redirect()
                ->route('transaksi-parkirs.masuk')
                ->with('success', 'Kendaraan berhasil masuk parkir.')
                ->with('ticket_data', $hasil['ticket_data']);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memproses kendaraan masuk.');
        }
    }

    private function getJenisKendaraanLahan(int $lahanParkirId)
    {
        return JenisKendaraan::query()
            ->select('jenis_kendaraans.id', 'jenis_kendaraans.nama_jenis_kendaraan')
            ->join('kapasitas_parkirs', 'kapasitas_parkirs.jenis_kendaraan_id', '=', 'jenis_kendaraans.id')
            ->where('kapasitas_parkirs.lahan_parkir_id', $lahanParkirId)
            ->where('kapasitas_parkirs.status_aktif', true)
            ->distinct()
            ->orderBy('jenis_kendaraans.nama_jenis_kendaraan')
            ->get()
            ->map(fn ($item) => (object) [
                'id' => $item->id,
                'nama' => $item->nama_jenis_kendaraan,
            ]);
    }

    private function generateKodeTiket(): string
    {
        do {
            $kode = 'TKT-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));
        } while (TransaksiParkir::query()->where('kode_tiket', $kode)->exists());

        return $kode;
    }

    private function normalizeNoPolisi(string $noPolisi): string
    {
        // Ubah jadi huruf besar dan pastikan spasi rapi (tidak ada spasi ganda)
        return strtoupper(preg_replace('/\s+/', ' ', trim($noPolisi)));
    }

    public function formKeluar()
    {
        return view('parkir.parkir-keluar');
    }

    public function cariDataKeluar($keyword)
    {
        // Ekstrak hanya huruf & angka murni (mengabaikan spasi dan strip)
        $normalizedKeyword = strtoupper(preg_replace('/[^A-Z0-9]/', '', $keyword));

        $transaksi = TransaksiParkir::with([
            'kendaraan.jenisKendaraan',
            'kendaraan.pemilik.jenisPemilik',
            'kendaraan.pemilik.User:id,name'
        ])
        // Gunakan whereNull dan orWhere untuk menjaga kehandalan jika sistem parkirnya sedikit anomali
        ->where(function($q) {
            $q->whereNull('waktu_keluar')
              ->orWhere('status_parkir', 'parkir');
        })
        ->where(function ($query) use ($keyword, $normalizedKeyword) {
            // Cocokkan tiket atau plat nomor dari string mentah
            $query->where('kode_tiket', $keyword)
                  ->orWhereRaw("UPPER(REPLACE(kode_tiket, '-', '')) = ?", [$normalizedKeyword])
                  // Cocokkan ke plat nomor dengan menghapus spasi dari sisi SQL
                  ->orWhereHas('kendaraan', function ($kQuery) use ($normalizedKeyword) {
                      $kQuery->whereRaw("UPPER(REPLACE(REPLACE(no_polisi, ' ', ''), '-', '')) = ?", [$normalizedKeyword]);
                  });
        })
        ->latest('id')
        ->first();

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan. Pastikan tiket/plat benar dan kendaraan belum diproses keluar.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaksi->id,
                'kode_tiket' => $transaksi->kode_tiket,
                'nomor_plat' => $transaksi->kendaraan?->no_polisi,
                'jenis_kendaraan' => $transaksi->kendaraan?->jenisKendaraan?->nama_jenis_kendaraan,
                'nama_pemilik' => $transaksi->kendaraan?->pemilik?->User?->name ?? 'Umum/Tamu',
                'waktu_masuk' => $transaksi->waktu_masuk,
            ]
        ]);
    }

    public function autocompletePlat(Request $request)
    {
        $term = $request->get('term');

        if (!$term) {
            return response()->json([]);
        }

        // Normalisasi term pencarian (bersihkan spasi)
        $normalizedTerm = strtoupper(preg_replace('/[^A-Z0-9]/', '', $term));

        $transaksiAktif = TransaksiParkir::with('kendaraan')
            ->where(function($q) {
                $q->whereNull('waktu_keluar')
                  ->orWhere('status_parkir', 'parkir');
            })
            ->where(function($query) use ($term, $normalizedTerm) {
                $query->where('kode_tiket', 'LIKE', '%' . $term . '%')
                      ->orWhereRaw("UPPER(REPLACE(kode_tiket, '-', '')) LIKE ?", ['%' . $normalizedTerm . '%'])
                      ->orWhereHas('kendaraan', function ($kQuery) use ($term, $normalizedTerm) {
                          $kQuery->where('no_polisi', 'LIKE', '%' . $term . '%')
                                 ->orWhereRaw("UPPER(REPLACE(REPLACE(no_polisi, ' ', ''), '-', '')) LIKE ?", ['%' . $normalizedTerm . '%']);
                      });
            })
            ->take(10)
            ->get();

        $results = [];
        foreach ($transaksiAktif as $transaksi) {
            if ($transaksi->kendaraan) {
                $results[] = [
                    'label' => $transaksi->kendaraan->no_polisi . ' (' . $transaksi->kode_tiket . ')',
                    'value' => $transaksi->kendaraan->no_polisi
                ];
            }
        }

        return response()->json($results);
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

    private function decodeAlasanDenda(?string $alasanDenda): array
    {
        if (!$alasanDenda) {
            return [];
        }

        $decoded = json_decode($alasanDenda, true);

        return is_array($decoded) ? $decoded : [];
    }
}
