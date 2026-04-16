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
            'lahan_id' => ['nullable', 'integer', 'exists:lahans,id'],
            'jenis_kendaraan_id' => ['nullable', 'integer', 'exists:jenis_kendaraans,id'],
            'jenis_pemilik_id' => ['nullable', 'integer', 'exists:jenis_pemiliks,id'],
            'status_aktif' => ['nullable', 'in:0,1'],
            'berlaku_pada' => ['nullable', 'date'],
            'scope' => ['nullable', 'in:semua,default,spesifik'],
        ]);

        $berlakuPada = $request->filled('berlaku_pada')
            ? Carbon::parse($request->berlaku_pada)->startOfSecond()
            : null;

        $tarifParkirs = TarifParkir::with(['lahan', 'jenisKendaraan', 'jenisPemilik'])
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

        $validated['lahan_id'] = $validated['lahan_id'] ?? null;
        $validated['biaya_masuk'] = $validated['biaya_masuk'] ?? 0;
        $validated['gratis_menit'] = $validated['gratis_menit'] ?? 0;

        $masaBerlaku = Carbon::parse($validated['masa_berlaku'])->startOfSecond();
        $selesaiBerlaku = !empty($validated['selesai_berlaku'])
            ? Carbon::parse($validated['selesai_berlaku'])->startOfSecond()
            : null;

        return DB::transaction(function () use ($validated, $masaBerlaku, $selesaiBerlaku) {
            $conflicts = $this->buildConflictQuery(
                $validated['lahan_id'],
                $validated['jenis_kendaraan_id'],
                $validated['jenis_pemilik_id'],
                $masaBerlaku,
                $selesaiBerlaku
            )->lockForUpdate()->get();

            if ($conflicts->isNotEmpty() && empty($validated['confirm_replace'])) {
                return redirect()
                    ->route('tarif-parkirs.index')
                    ->withInput()
                    ->with('warning', 'Sudah ada tarif aktif atau tarif terjadwal untuk kombinasi yang sama. Simpan ulang dengan konfirmasi penggantian.')
                    ->with('needs_confirmation', true);
            }

            foreach ($conflicts as $conflict) {
                $conflictStart = Carbon::parse($conflict->masa_berlaku)->startOfSecond();

                if ($conflictStart->lt($masaBerlaku)) {
                    $conflict->update([
                        'selesai_berlaku' => $masaBerlaku,
                    ]);
                } else {
                    $conflict->update([
                        'status_aktif' => false,
                    ]);
                }
            }

            TarifParkir::create([
                'lahan_id' => $validated['lahan_id'],
                'jenis_kendaraan_id' => $validated['jenis_kendaraan_id'],
                'jenis_pemilik_id' => $validated['jenis_pemilik_id'],
                'biaya_masuk' => $validated['biaya_masuk'],
                'biaya_per_jam' => $validated['biaya_per_jam'],
                'biaya_maksimal' => $validated['biaya_maksimal'] ?? null,
                'gratis_menit' => $validated['gratis_menit'],
                'status_aktif' => true,
                'masa_berlaku' => $masaBerlaku,
                'selesai_berlaku' => $selesaiBerlaku,
            ]);

            return redirect()
                ->route('tarif-parkirs.index')
                ->with('success', 'Tarif parkir berhasil disimpan.');
        });
    }

    public function show(TarifParkir $tarifParkir)
    {
        $tarifParkir->load(['lahan', 'jenisKendaraan', 'jenisPemilik']);

        return redirect()
            ->route('tarif-parkirs.index')
            ->with('show_tarif', $tarifParkir);
    }

    public function nonaktifkan(Request $request, TarifParkir $tarifParkir)
    {
        $validated = $request->validate([
            'waktu_nonaktif' => ['nullable', 'date'],
        ]);

        if (!$tarifParkir->status_aktif) {
            return redirect()
                ->route('tarif-parkirs.index')
                ->with('warning', 'Tarif parkir sudah nonaktif.');
        }

        $waktuNonaktif = !empty($validated['waktu_nonaktif'])
            ? Carbon::parse($validated['waktu_nonaktif'])->startOfSecond()
            : now()->startOfSecond();

        DB::transaction(function () use ($tarifParkir, $waktuNonaktif) {
            $updateData = [
                'status_aktif' => false,
            ];

            if (
                Carbon::parse($tarifParkir->masa_berlaku)->startOfSecond()->lte($waktuNonaktif) &&
                (
                    is_null($tarifParkir->selesai_berlaku) ||
                    Carbon::parse($tarifParkir->selesai_berlaku)->startOfSecond()->gt($waktuNonaktif)
                )
            ) {
                $updateData['selesai_berlaku'] = $waktuNonaktif;
            }

            $tarifParkir->update($updateData);
        });

        return redirect()
            ->route('tarif-parkirs.index')
            ->with('success', 'Tarif parkir berhasil dinonaktifkan.');
    }

    public function berlaku(Request $request)
    {
        $validated = $request->validate([
            'lahan_id' => ['nullable', 'integer', 'exists:lahans,id'],
            'jenis_kendaraan_id' => ['required', 'integer', 'exists:jenis_kendaraans,id'],
            'jenis_pemilik_id' => ['required', 'integer', 'exists:jenis_pemiliks,id'],
            'waktu' => ['nullable', 'date'],
        ]);

        $waktu = !empty($validated['waktu'])
            ? Carbon::parse($validated['waktu'])->startOfSecond()
            : now()->startOfSecond();

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
            'lahan_id' => ['nullable', 'integer', 'exists:lahans,id'],
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

    private function buildConflictQuery(
        ?int $lahanId,
        int $jenisKendaraanId,
        int $jenisPemilikId,
        Carbon $masaBerlaku,
        ?Carbon $selesaiBerlaku
    ) {
        return TarifParkir::query()
            ->where('jenis_kendaraan_id', $jenisKendaraanId)
            ->where('jenis_pemilik_id', $jenisPemilikId)
            ->where('status_aktif', true)
            ->when(
                is_null($lahanId),
                function ($query) {
                    $query->whereNull('lahan_id');
                },
                function ($query) use ($lahanId) {
                    $query->where('lahan_id', $lahanId);
                }
            )
            ->where(function ($query) use ($masaBerlaku) {
                $query->whereNull('selesai_berlaku')
                    ->orWhere('selesai_berlaku', '>', $masaBerlaku);
            })
            ->when($selesaiBerlaku, function ($query) use ($selesaiBerlaku) {
                $query->where('masa_berlaku', '<', $selesaiBerlaku);
            })
            ->orderBy('masa_berlaku');
    }
}
