<?php

namespace App\Http\Controllers;

use App\Models\JenisPemilik;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JenisPemilikController extends Controller
{
    public function index()
    {
        $jenisPemiliks = JenisPemilik::latest()->get();
        return view('JenisPemilik.index', compact('jenisPemiliks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis_pemilik' => ['required', 'string', 'max:255', 'unique:jenis_pemiliks,nama_jenis_pemilik'],
            'deskripsi' => ['nullable', 'string'],
            'status_aktif' => ['required', 'boolean'],
        ]);

        $validated['nama_jenis_pemilik'] = Str::title(trim($validated['nama_jenis_pemilik']));
        $validated['kode_jenis_pemilik'] = Str::slug($validated['nama_jenis_pemilik'], '-');

        JenisPemilik::create($validated);

        return redirect()->route('jenis-pemilik.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, JenisPemilik $jenisPemilik)
    {
        $validated = $request->validate([
            'nama_jenis_pemilik' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jenis_pemiliks', 'nama_jenis_pemilik')->ignore($jenisPemilik->id),
            ],
            'deskripsi' => ['nullable', 'string'],
            'status_aktif' => ['required', 'boolean'],
        ]);

        $validated['nama_jenis_pemilik'] = Str::title(trim($validated['nama_jenis_pemilik']));
        $validated['kode_jenis_pemilik'] = Str::slug($validated['nama_jenis_pemilik'], '-');

        $jenisPemilik->update($validated);

        return redirect()->route('jenis-pemilik.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy(Request $request, JenisPemilik $jenisPemilik)
    {
        $jenisPemilik->delete();

        return redirect()->route('jenis-pemilik.index')->with('success', 'Data berhasil dihapus.');
    }
}
