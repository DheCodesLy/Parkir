<?php

namespace App\Http\Controllers;

use App\Models\JenisKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JenisKendaraanController extends Controller
{
    public function index()
    {
        $jenisKendaraans = JenisKendaraan::latest()->get();
        return view('JenisKendaraan.index', compact('jenisKendaraans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis_kendaraan' => ['required', 'string', 'max:255', 'unique:jenis_kendaraans,nama_jenis_kendaraan'],
            'deskripsi' => ['nullable', 'string'],
            'status_aktif' => ['required', 'boolean'],
        ]);

        $validated['nama_jenis_kendaraan'] = Str::title(trim($validated['nama_jenis_kendaraan']));
        $validated['kode_jenis_kendaraan'] = Str::slug($validated['nama_jenis_kendaraan'], '-');

        $jenisKendaraan = JenisKendaraan::create($validated);

        return redirect()
            ->route('jenis-kendaraan.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, JenisKendaraan $jenisKendaraan)
    {
        $validated = $request->validate([
            'nama_jenis_kendaraan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jenis_kendaraans', 'nama_jenis_kendaraan')->ignore($jenisKendaraan->id),
            ],
            'deskripsi' => ['nullable', 'string'],
            'status_aktif' => ['required', 'boolean'],
        ]);

        $validated['nama_jenis_kendaraan'] = Str::title(trim($validated['nama_jenis_kendaraan']));
        $validated['kode_jenis_kendaraan'] = Str::slug($validated['nama_jenis_kendaraan'], '-');

        $jenisKendaraan->update($validated);

        return redirect()
            ->route('jenis-kendaraan.index')
            ->with('success', 'Data berhasil diupdate.');
    }

    public function destroy(Request $request, JenisKendaraan $jenisKendaraan)
    {
        $deletedData = $jenisKendaraan->toArray();
        $jenisKendaraan->delete();

        return redirect()
            ->route('jenis-kendaraan.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
