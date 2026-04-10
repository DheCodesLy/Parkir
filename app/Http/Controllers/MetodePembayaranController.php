<?php

namespace App\Http\Controllers;

use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MetodePembayaranController extends Controller
{
    public function index()
    {
        $metodePembayarans = MetodePembayaran::latest()->get();
        return view('MetodePembayaran.index', compact('metodePembayarans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_metode' => ['required', 'string', 'max:100', 'unique:metode_pembayarans,nama_metode'],
            'kategori' => ['required', Rule::in(['tunai', 'digital', 'transfer', 'lainnya'])],
            'status_aktif' => ['required', 'boolean'],
            'urutan' => ['required', 'integer', 'min:0'],
        ]);

        $validated['nama_metode'] = Str::title(trim($validated['nama_metode']));
        $validated['kode_metode'] = $this->generateKodeMetode($validated['nama_metode']);

        MetodePembayaran::create($validated);

        return redirect()
            ->route('metode-pembayaran.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, MetodePembayaran $metodePembayaran)
    {
        $validated = $request->validate([
            'nama_metode' => [
                'required',
                'string',
                'max:100',
                Rule::unique('metode_pembayarans', 'nama_metode')->ignore($metodePembayaran->id),
            ],
            'kategori' => ['required', Rule::in(['tunai', 'digital', 'transfer', 'lainnya'])],
            'status_aktif' => ['required', 'boolean'],
            'urutan' => ['required', 'integer', 'min:0'],
        ]);

        $validated['nama_metode'] = Str::title(trim($validated['nama_metode']));
        $validated['kode_metode'] = $this->generateKodeMetode($validated['nama_metode'], $metodePembayaran->id);

        $metodePembayaran->update($validated);

        return redirect()
            ->route('metode-pembayaran.index')
            ->with('success', 'Data berhasil diupdate.');
    }

    public function destroy(Request $request, MetodePembayaran $metodePembayaran)
    {
        $metodePembayaran->delete();

        return redirect()
            ->route('metode-pembayaran.index')
            ->with('success', 'Data berhasil dihapus.');
    }

    private function generateKodeMetode(string $namaMetode, ?int $ignoreId = null): string
    {
        $baseKode = Str::slug(trim($namaMetode), '-');
        $kode = $baseKode;
        $counter = 1;

        while (
            MetodePembayaran::withTrashed()
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->where('kode_metode', $kode)
                ->exists()
        ) {
            $kode = $baseKode . '-' . $counter;
            $counter++;
        }

        return $kode;
    }
}
