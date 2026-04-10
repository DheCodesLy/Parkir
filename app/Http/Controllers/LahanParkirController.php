<?php

namespace App\Http\Controllers;

use App\Models\JenisKendaraan;
use App\Models\JenisPemilik;
use App\Models\KapasitasParkir;
use App\Models\LahanParkir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LahanParkirController extends Controller
{
    public function index()
    {
        $lahanParkirs = LahanParkir::with([
            'kapasitasParkir.jenisPemilik',
            'kapasitasParkir.jenisKendaraan',
        ])->latest()->get()->map(function ($lahan) {
            return [
                'id' => $lahan->id,
                'nama_lahan' => $lahan->nama_lahan,
                'kapasitas' => $lahan->kapasitas,
                'sisa_slot' => $lahan->sisa_slot,
                'status_aktif' => $lahan->status_aktif,
                'jenis_pemilik' => $lahan->kapasitasParkir->pluck('jenisPemilik.nama')->filter()->unique()->values(),
                'jenis_kendaraan' => $lahan->kapasitasParkir->pluck('jenisKendaraan.nama')->filter()->unique()->values(),
                'pengguna_parkir' => $lahan->kapasitasParkir->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'jenis_pemilik_id' => $item->jenis_pemilik_id,
                        'jenis_pemilik_nama' => $item->jenisPemilik?->nama,
                        'jenis_kendaraan_id' => $item->jenis_kendaraan_id,
                        'jenis_kendaraan_nama' => $item->jenisKendaraan?->nama,
                        'status_aktif' => $item->status_aktif,
                    ];
                })->values(),
            ];
        });

        $jenisPemiliks = JenisPemilik::where('status_aktif', true)->get(['id', 'nama_jenis_pemilik']);
        $jenisKendaraans = JenisKendaraan::where('status_aktif', true)->get(['id', 'nama_jenis_kendaraan']);

        return view('LahanParkir.index', compact('lahanParkirs', 'jenisPemiliks', 'jenisKendaraans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lahan' => ['required', 'string', 'max:255', 'unique:lahan_parkirs,nama_lahan'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'pengguna_parkir' => ['required', 'array', 'min:1'],
            'pengguna_parkir.*.jenis_pemilik_id' => ['required', 'exists:jenis_pemiliks,id'],
            'pengguna_parkir.*.jenis_kendaraan_id' => ['required', 'exists:jenis_kendaraans,id'],
        ], [
            'nama_lahan.required' => 'Nama lahan wajib diisi.',
            'nama_lahan.unique' => 'Nama lahan sudah digunakan.',
            'kapasitas.required' => 'Kapasitas wajib diisi.',
            'kapasitas.integer' => 'Kapasitas harus berupa angka.',
            'kapasitas.min' => 'Kapasitas minimal 1.',
            'pengguna_parkir.required' => 'Pengguna parkir wajib diisi minimal 1.',
            'pengguna_parkir.array' => 'Format pengguna parkir tidak valid.',
            'pengguna_parkir.min' => 'Pengguna parkir wajib diisi minimal 1.',
            'pengguna_parkir.*.jenis_pemilik_id.required' => 'Jenis pemilik wajib dipilih.',
            'pengguna_parkir.*.jenis_pemilik_id.exists' => 'Jenis pemilik tidak valid.',
            'pengguna_parkir.*.jenis_kendaraan_id.required' => 'Jenis kendaraan wajib dipilih.',
            'pengguna_parkir.*.jenis_kendaraan_id.exists' => 'Jenis kendaraan tidak valid.',
        ]);

        DB::transaction(function () use ($validated) {
            $lahanParkir = LahanParkir::create([
                'nama_lahan' => $validated['nama_lahan'],
                'kapasitas' => $validated['kapasitas'],
                'sisa_slot' => $validated['kapasitas'],
                'status_aktif' => true,
            ]);

            foreach ($validated['pengguna_parkir'] as $pengguna) {
                KapasitasParkir::create([
                    'lahan_parkir_id' => $lahanParkir->id,
                    'jenis_pemilik_id' => $pengguna['jenis_pemilik_id'],
                    'jenis_kendaraan_id' => $pengguna['jenis_kendaraan_id'],
                    'status_aktif' => true,
                ]);
            }
        });

        return redirect()->route('LahanParkir.index')->with('success', 'Lahan parkir berhasil ditambahkan.');
    }

    public function create()
    {
        $jenisPemiliks = JenisPemilik::where('status_aktif', true)->get(['id', 'nama']);
        $jenisKendaraans = JenisKendaraan::where('status_aktif', true)->get(['id', 'nama']);

        return view('LahanParkir.create', compact('jenisPemiliks', 'jenisKendaraans'));
    }

    public function edit($id)
    {
        $lahanParkir = LahanParkir::with([
            'kapasitasParkir.jenisPemilik',
            'kapasitasParkir.jenisKendaraan',
        ])->findOrFail($id);

        $jenisPemiliks = JenisPemilik::where('status_aktif', true)->get(['id', 'nama']);
        $jenisKendaraans = JenisKendaraan::where('status_aktif', true)->get(['id', 'nama']);

        $formData = [
            'id' => $lahanParkir->id,
            'nama_lahan' => $lahanParkir->nama_lahan,
            'kapasitas' => $lahanParkir->kapasitas,
            'sisa_slot' => $lahanParkir->sisa_slot,
            'status_aktif' => $lahanParkir->status_aktif,
            'pengguna_parkir' => $lahanParkir->kapasitasParkir->map(function ($item) {
                return [
                    'id' => $item->id,
                    'jenis_pemilik_id' => $item->jenis_pemilik_id,
                    'jenis_kendaraan_id' => $item->jenis_kendaraan_id,
                ];
            })->values()->toArray(),
        ];

        return view('LahanParkir.edit', compact('lahanParkir', 'formData', 'jenisPemiliks', 'jenisKendaraans'));
    }

    public function update(Request $request, $id)
    {
        $lahanParkir = LahanParkir::with('kapasitasParkir')->findOrFail($id);

        $validated = $request->validate([
            'nama_lahan' => ['required','string','max:255',
                Rule::unique('lahan_parkirs', 'nama_lahan')->ignore($lahanParkir->id),
            ],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'pengguna_parkir' => ['required', 'array', 'min:1'],
            'pengguna_parkir.*.id' => ['nullable', 'integer', 'exists:kapasitas_parkirs,id'],
            'pengguna_parkir.*.jenis_pemilik_id' => ['required', 'exists:jenis_pemiliks,id'],
            'pengguna_parkir.*.jenis_kendaraan_id' => ['required', 'exists:jenis_kendaraans,id'],
        ], [
            'nama_lahan.required' => 'Nama lahan wajib diisi.',
            'nama_lahan.unique' => 'Nama lahan sudah digunakan.',
            'kapasitas.required' => 'Kapasitas wajib diisi.',
            'kapasitas.integer' => 'Kapasitas harus berupa angka.',
            'kapasitas.min' => 'Kapasitas minimal 1.',
            'pengguna_parkir.required' => 'Pengguna parkir wajib diisi minimal 1.',
            'pengguna_parkir.array' => 'Format pengguna parkir tidak valid.',
            'pengguna_parkir.min' => 'Pengguna parkir wajib diisi minimal 1.',
            'pengguna_parkir.*.id.integer' => 'ID pengguna parkir tidak valid.',
            'pengguna_parkir.*.id.exists' => 'Data pengguna parkir tidak ditemukan.',
            'pengguna_parkir.*.jenis_pemilik_id.required' => 'Jenis pemilik wajib dipilih.',
            'pengguna_parkir.*.jenis_pemilik_id.exists' => 'Jenis pemilik tidak valid.',
            'pengguna_parkir.*.jenis_kendaraan_id.required' => 'Jenis kendaraan wajib dipilih.',
            'pengguna_parkir.*.jenis_kendaraan_id.exists' => 'Jenis kendaraan tidak valid.',
        ]);

        DB::transaction(function () use ($validated, $lahanParkir) {
            $kapasitasLama = $lahanParkir->kapasitas;
            $kapasitasBaru = (int) $validated['kapasitas'];
            $selisihKapasitas = $kapasitasBaru - $kapasitasLama;

            $dataUpdateLahan = [
                'nama_lahan' => $validated['nama_lahan'],
                'kapasitas' => $kapasitasBaru,
            ];

            if ($selisihKapasitas > 0) {
                $dataUpdateLahan['sisa_slot'] = $lahanParkir->sisa_slot + $selisihKapasitas;
            }

            if ($selisihKapasitas < 0) {
                $sisaSlotBaru = $lahanParkir->sisa_slot + $selisihKapasitas;
                $dataUpdateLahan['sisa_slot'] = max(0, $sisaSlotBaru);
            }

            $lahanParkir->update($dataUpdateLahan);

            $existingIds = $lahanParkir->kapasitasParkir->pluck('id')->toArray();

            $incomingIds = collect($validated['pengguna_parkir'])->pluck('id')->filter()->map(fn ($item) => (int) $item)->toArray();

            $deletedIds = array_diff($existingIds, $incomingIds);

            if (!empty($deletedIds)) {
                KapasitasParkir::whereIn('id', $deletedIds)->where('lahan_parkir_id', $lahanParkir->id)->delete();
            }

            foreach ($validated['pengguna_parkir'] as $pengguna) {
                if (!empty($pengguna['id'])) {
                    KapasitasParkir::where('id', $pengguna['id'])->where('lahan_parkir_id', $lahanParkir->id)->update([
                        'jenis_pemilik_id' => $pengguna['jenis_pemilik_id'],
                        'jenis_kendaraan_id' => $pengguna['jenis_kendaraan_id'],
                    ]);
                } else {
                    KapasitasParkir::create([
                        'lahan_parkir_id' => $lahanParkir->id,
                        'jenis_pemilik_id' => $pengguna['jenis_pemilik_id'],
                        'jenis_kendaraan_id' => $pengguna['jenis_kendaraan_id'],
                        'status_aktif' => true,
                    ]);
                }
            }
        });

        return redirect()->route('LahanParkir.index')->with('success', 'Lahan parkir berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $lahanParkir = LahanParkir::findOrFail($id);

        DB::transaction(function () use ($lahanParkir) {
            KapasitasParkir::where('lahan_parkir_id', $lahanParkir->id)->delete();
            $lahanParkir->delete();
        });

        return redirect()->route('LahanParkir.index')->with('success', 'Lahan parkir berhasil dihapus.');
    }

    public function checkNamaLahan(Request $request)
    {
        $request->validate([
            'nama_lahan' => ['required', 'string', 'max:255'],
        ]);

        $existing = LahanParkir::whereRaw('LOWER(nama_lahan) = ?', [strtolower($request->nama_lahan)])->first(['id', 'nama_lahan']);

        return response()->json([
            'exists' => (bool) $existing,
            'data' => $existing,
        ]);
    }

    public function show($id)
    {
        $lahanParkir = LahanParkir::with([
            'kapasitasParkir.jenisPemilik',
            'kapasitasParkir.jenisKendaraan',
        ])->findOrFail($id);

        return view('LahanParkir.show', compact('lahanParkir'));
    }
}
