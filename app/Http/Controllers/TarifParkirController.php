<?php

namespace App\Http\Controllers;

use App\Models\JenisKendaraan;
use App\Models\JenisPemilik;
use App\Models\LahanParkir;
use App\Models\TarifParkir;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarifParkirController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            // Sesuaikan nama tabel 'lahan_parkirs' (sesuai konvensi model LahanParkir)
            'lahan_id' => ['nullable', 'integer', 'exists:lahan_parkirs,id'],
            'jenis_kendaraan_id' => ['nullable', 'integer', 'exists:jenis_kendaraans,id'],
            'jenis_pemilik_id' => ['nullable', 'integer', 'exists:jenis_pemiliks,id'],
            'status_aktif' => ['nullable', 'in:0,1'],
            'berlaku_pada' => ['nullable', 'date'],
            'scope' => ['nullable', 'in:semua,default,spesifik'],
        ]);

        $berlakuPada = $request->filled('berlaku_pada')
            ? Carbon::parse($request->berlaku_pada)->startOfSecond()
            : null;

        $tarifParkirs = TarifParkir::with(['LahanParkir', 'JenisKendaraan', 'JenisPemilik'])
            ->when($request->filled('lahan_id'), function ($query) use ($request) {
                $query->where('lahan_id', $request->integer('lahan_id'));
            })
            ->when($request->filled('jenis_kendaraan_id'), function ($query) use ($request) {
                $query->where('jenis_kendaraan_id', $request->integer('jenis_kendaraan_id'));
            })
            ->when($request->filled('jenis_pemilik_id'), function ($query) use ($request) {
                $query->where('jenis_pemilik_id', $request->integer('jenis_pemilik_id'));
            })
            ->when($request->filled('status_aktif'), function ($query) use ($request) {
                $query->where('status_aktif', (bool) $request->status_aktif);
            })
            ->when($request->input('scope') === 'default', function ($query) {
                $query->whereNull('lahan_id');
            })
            ->when($request->input('scope') === 'spesifik', function ($query) {
                $query->whereNotNull('lahan_id');
            })
            ->when($berlakuPada, function ($query) use ($berlakuPada) {
                $query->where('masa_berlaku', '<=', $berlakuPada)
                    ->where(function ($subQuery) use ($berlakuPada) {
                        $subQuery->whereNull('selesai_berlaku')
                            ->orWhere('selesai_berlaku', '>', $berlakuPada);
                    });
            })
            ->orderByDesc('masa_berlaku')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $lahans = LahanParkir::where('status_aktif', true)
            ->orderBy('nama_lahan')
            ->get(['id', 'nama_lahan']);

        $jenisPemiliks = JenisPemilik::where('status_aktif', true)
            ->orderBy('nama_jenis_pemilik')
            ->get(['id', 'nama_jenis_pemilik']);

        $jenisKendaraans = collect();

        if (old('lahan_id')) {
            $jenisKendaraans = $this->getJenisKendaraanByLahan((int) old('lahan_id'));
        } else {
            $jenisKendaraans = JenisKendaraan::where('status_aktif', true)
                ->orderBy('nama_jenis_kendaraan')
                ->get(['id', 'nama_jenis_kendaraan']);
        }

        $tarifBerlaku = session()->has('tarif_berlaku_result')
            ? session('tarif_berlaku_result')
            : null;

        return view('tarif-parkirs.index', compact(
            'tarifParkirs',
            'lahans',
            'jenisKendaraans',
            'jenisPemiliks',
            'tarifBerlaku'
        ));
    }

        public function store(Request $request)
    {
        $validated = $request->validate([
            'lahan_id' => ['nullable', 'integer', 'exists:lahan_parkirs,id'],
            'jenis_kendaraan_id' => ['required', 'integer', 'exists:jenis_kendaraans,id'],
            'jenis_pemilik_id' => ['required', 'integer', 'exists:jenis_pemiliks,id'],
            'biaya_masuk' => ['nullable', 'numeric', 'min:0'],
            'biaya_per_jam' => ['required', 'numeric', 'min:0'],
            'biaya_maksimal' => ['nullable', 'numeric', 'min:0'],
            'gratis_menit' => ['nullable', 'integer', 'min:0'],
            'masa_berlaku' => ['required', 'date'],
            'selesai_berlaku' => ['nullable', 'date', 'after:masa_berlaku'],
            'confirm_replace' => ['nullable', 'in:0,1'],
        ]);

        $lahanId = $validated['lahan_id'] ?? null;
        $masaBerlaku = Carbon::parse($validated['masa_berlaku'])->startOfSecond();
        $selesaiBerlaku = !empty($validated['selesai_berlaku']) ? Carbon::parse($validated['selesai_berlaku'])->startOfSecond() : null;

        // Cek apakah ada tarif aktif yang tumpang tindih
        $conflicts = $this->buildConflictQuery(
            $lahanId,
            $validated['jenis_kendaraan_id'],
            $validated['jenis_pemilik_id'],
            $masaBerlaku,
            $selesaiBerlaku
        )->get();

        // Jika ada konflik dan belum ada konfirmasi dari user
        if ($conflicts->isNotEmpty() && !$request->boolean('confirm_replace')) {
            return redirect()->back()
                ->withInput()
                ->with('needs_confirmation', true)
                ->with('conflict_count', $conflicts->count());
        }

        return DB::transaction(function () use ($validated, $lahanId, $masaBerlaku, $selesaiBerlaku, $conflicts) {
            // Jika dikonfirmasi, nonaktifkan semua data lama yang konflik
            if ($conflicts->isNotEmpty()) {
                foreach ($conflicts as $conflict) {
                    $conflict->update([
                        'status_aktif' => false,
                        'selesai_berlaku' => $masaBerlaku // Berakhir tepat saat yang baru mulai
                    ]);
                }
            }

            // Buat tarif baru
            TarifParkir::create([
                'lahan_id' => $lahanId,
                'jenis_kendaraan_id' => $validated['jenis_kendaraan_id'],
                'jenis_pemilik_id' => $validated['jenis_pemilik_id'],
                'biaya_masuk' => $validated['biaya_masuk'] ?? 0,
                'biaya_per_jam' => $validated['biaya_per_jam'],
                'biaya_maksimal' => $validated['biaya_maksimal'] ?? null,
                'gratis_menit' => $validated['gratis_menit'] ?? 0,
                'status_aktif' => true,
                'masa_berlaku' => $masaBerlaku,
                'selesai_berlaku' => $selesaiBerlaku,
            ]);

            return redirect()->route('tarif-parkirs.index')
                ->with('success', 'Tarif parkir baru berhasil diaktifkan dan tarif lama telah dinonaktifkan.');
        });
    }

    public function show(TarifParkir $tarifParkir)
    {
        // Sesuaikan nama relasi dengan Model
        $tarifParkir->load(['lahan', 'jenisKendaraan', 'jenisPemilik']);

        return redirect()
            ->route('tarif-parkirs.index')
            ->with('show_tarif', $tarifParkir);
    }

    public function nonaktifkan(TarifParkir $tarifParkir)
    {
        // Jika sudah nonaktif, langsung kembalikan
        if (!$tarifParkir->status_aktif) {
            return redirect()->route('tarif-parkirs.index')
                ->with('warning', 'Tarif parkir sudah nonaktif.');
        }

        $waktuSekarang = now()->startOfSecond();

        DB::transaction(function () use ($tarifParkir, $waktuSekarang) {
            $tarifParkir->update([
                'status_aktif' => false,
                'selesai_berlaku' => $waktuSekarang // Otomatis terset ke waktu sekarang
            ]);
        });

        return redirect()->route('tarif-parkirs.index')
            ->with('success', 'Tarif parkir berhasil dinonaktifkan.');
    }

    public function berlaku(Request $request)
    {
        $validated = $request->validate([
            'lahan_id' => ['nullable', 'integer', 'exists:lahan_parkirs,id'],
            'jenis_kendaraan_id' => ['required', 'integer', 'exists:jenis_kendaraans,id'],
            'jenis_pemilik_id' => ['required', 'integer', 'exists:jenis_pemiliks,id'],
            'waktu' => ['nullable', 'date'],
        ]);

        $waktu = !empty($validated['waktu'])
            ? Carbon::parse($validated['waktu'])->startOfSecond()
            : now()->startOfSecond();

        // UBAH: LahanParkir menjadi lahan agar sinkron
        $tarif = TarifParkir::with(['lahan', 'jenisKendaraan', 'jenisPemilik'])
            ->where('status_aktif', true)
            ->where('jenis_kendaraan_id', $validated['jenis_kendaraan_id'])
            ->where('jenis_pemilik_id', $validated['jenis_pemilik_id'])
            ->where('masa_berlaku', '<=', $waktu)
            ->where(function ($query) use ($waktu) {
                $query->whereNull('selesai_berlaku')
                    ->orWhere('selesai_berlaku', '>', $waktu);
            })
            ->when(
                array_key_exists('lahan_id', $validated) && !is_null($validated['lahan_id']),
                function ($query) use ($validated) {
                    $query->where(function ($subQuery) use ($validated) {
                        $subQuery->where('lahan_id', $validated['lahan_id'])
                            ->orWhereNull('lahan_id');
                    });
                },
                function ($query) {
                    $query->whereNull('lahan_id');
                }
            )
            ->orderByRaw('case when lahan_id is null then 1 else 0 end')
            ->orderByDesc('masa_berlaku')
            ->first();

        if (!$tarif) {
            return redirect()
                ->route('tarif-parkirs.index')
                ->with('warning', 'Tarif parkir tidak ditemukan untuk parameter tersebut.')
                ->with('open_berlaku_modal', true);
        }

        return redirect()
            ->route('tarif-parkirs.index')
            ->with('success', 'Tarif parkir berlaku berhasil ditemukan.')
            ->with('open_berlaku_modal', true)
            ->with('tarif_berlaku_result', $tarif);
    }

    public function kendaraanByLahan(Request $request)
    {
        $request->validate([
            'lahan_id' => ['nullable', 'integer', 'exists:lahan_parkirs,id'],
        ]);

        $lahanId = $request->input('lahan_id');

        $kendaraans = $lahanId
            ? $this->getJenisKendaraanByLahan((int) $lahanId)
            : JenisKendaraan::where('status_aktif', true)
                ->orderBy('nama_jenis_kendaraan')
                ->get(['id', 'nama_jenis_kendaraan']);

        return response()->json([
            'success' => true,
            'data' => $kendaraans->values(),
            'auto_selected_id' => $kendaraans->count() === 1 ? $kendaraans->first()->id : null,
        ]);
    }

    private function getJenisKendaraanByLahan(int $lahanId)
    {
        return JenisKendaraan::query()
            ->select('jenis_kendaraans.id', 'jenis_kendaraans.nama_jenis_kendaraan')
            ->join('tarif_parkirs', 'tarif_parkirs.jenis_kendaraan_id', '=', 'jenis_kendaraans.id')
            ->where('jenis_kendaraans.status_aktif', true)
            ->where('tarif_parkirs.lahan_id', $lahanId)
            ->distinct()
            ->orderBy('jenis_kendaraans.nama_jenis_kendaraan')
            ->get();
    }

    private function buildConflictQuery($lahanId, $jenisKendaraanId, $jenisPemilikId, $masaBerlaku, $selesaiBerlaku)
    {
        return TarifParkir::query()
            ->where('status_aktif', true)
            ->where('jenis_kendaraan_id', $jenisKendaraanId)
            ->where('jenis_pemilik_id', $jenisPemilikId)
            ->where(function ($query) use ($lahanId) {
                if (is_null($lahanId)) {
                    $query->whereNull('lahan_id');
                } else {
                    $query->where('lahan_id', $lahanId);
                }
            })
            ->where(function ($query) use ($masaBerlaku, $selesaiBerlaku) {
                // Logika tumpang tindih waktu
                $query->where(function ($q) use ($masaBerlaku) {
                    $q->whereNull('selesai_berlaku')
                    ->orWhere('selesai_berlaku', '>', $masaBerlaku);
                });

                if ($selesaiBerlaku) {
                    $query->where('masa_berlaku', '<', $selesaiBerlaku);
                }
            });
    }
}
