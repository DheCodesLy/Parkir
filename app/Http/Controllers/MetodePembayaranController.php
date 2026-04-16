<?php

namespace App\Http\Controllers;

use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MetodePembayaranController extends Controller
{
   public function index(Request $request)
    {
        $query = MetodePembayaran::query()->orderBy('urutan')->orderBy('id');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('nama_metode', 'like', '%' . $keyword . '%')
                    ->orWhere('kode_metode', 'like', '%' . $keyword . '%')
                    ->orWhere('kategori', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->status_aktif);
        }

        $items = $query->paginate(10)->withQueryString();

        return view('metode_pembayaran.index', compact('items'));
    }

    public function create()
    {
        return view('metode_pembayaran.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_metode' => ['required', 'string', 'max:100', 'unique:metode_pembayarans,nama_metode'],
            'kode_metode' => ['required', 'string', 'max:50', 'unique:metode_pembayarans,kode_metode'],
            'kategori' => ['required', Rule::in(['tunai', 'digital', 'transfer', 'lainnya'])],
            'status_aktif' => ['nullable', 'boolean'],
            'urutan' => ['nullable', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($data) {
            $lastPosition = (int) MetodePembayaran::max('urutan');
            $position = isset($data['urutan']) ? (int) $data['urutan'] : $lastPosition + 1;
            $position = max(1, min($position, $lastPosition + 1));

            MetodePembayaran::where('urutan', '>=', $position)->increment('urutan');

            MetodePembayaran::create([
                'nama_metode' => $data['nama_metode'],
                'kode_metode' => $data['kode_metode'],
                'kategori' => $data['kategori'],
                'status_aktif' => $data['status_aktif'] ?? true,
                'urutan' => $position,
            ]);
        });

        return redirect()
            ->route('metode-pembayaran.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    public function show(MetodePembayaran $metodePembayaran)
    {
        return view('metode_pembayaran.show', compact('metodePembayaran'));
    }

    public function edit(MetodePembayaran $metodePembayaran)
    {
        return view('metode_pembayaran.edit', compact('metodePembayaran'));
    }

    public function update(Request $request, MetodePembayaran $metodePembayaran)
    {
        $data = $request->validate([
            'nama_metode' => ['required', 'string', 'max:100', Rule::unique('metode_pembayarans', 'nama_metode')->ignore($metodePembayaran->id)],
            'kode_metode' => ['required', 'string', 'max:50', Rule::unique('metode_pembayarans', 'kode_metode')->ignore($metodePembayaran->id)],
            'kategori' => ['required', Rule::in(['tunai', 'digital', 'transfer', 'lainnya'])],
            'status_aktif' => ['nullable', 'boolean'],
            'urutan' => ['nullable', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($data, $metodePembayaran) {
            $oldPosition = (int) $metodePembayaran->urutan;
            $maxPosition = (int) MetodePembayaran::where('id', '!=', $metodePembayaran->id)->max('urutan');
            $newPosition = isset($data['urutan']) ? (int) $data['urutan'] : $oldPosition;
            $newPosition = max(1, min($newPosition, max(1, $maxPosition + 1)));

            if ($newPosition < $oldPosition) {
                MetodePembayaran::where('id', '!=', $metodePembayaran->id)
                    ->whereBetween('urutan', [$newPosition, $oldPosition - 1])
                    ->increment('urutan');
            }

            if ($newPosition > $oldPosition) {
                MetodePembayaran::where('id', '!=', $metodePembayaran->id)
                    ->whereBetween('urutan', [$oldPosition + 1, $newPosition])
                    ->decrement('urutan');
            }

            $metodePembayaran->update([
                'nama_metode' => $data['nama_metode'],
                'kode_metode' => $data['kode_metode'],
                'kategori' => $data['kategori'],
                'status_aktif' => $data['status_aktif'] ?? false,
                'urutan' => $newPosition,
            ]);
        });

        return redirect()
            ->route('metode-pembayaran.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui');
    }

    public function destroy(MetodePembayaran $metodePembayaran)
    {
        DB::transaction(function () use ($metodePembayaran) {
            $deletedPosition = (int) $metodePembayaran->urutan;

            $metodePembayaran->delete();

            MetodePembayaran::where('urutan', '>', $deletedPosition)->decrement('urutan');
        });

        return redirect()
            ->route('metode-pembayaran.index')
            ->with('success', 'Metode pembayaran berhasil dihapus');
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct', 'exists:metode_pembayarans,id'],
        ]);

        DB::transaction(function () use ($data) {
            $requestedIds = collect($data['ids'])->map(fn ($id) => (int) $id)->values();
            $existingIds = MetodePembayaran::query()->orderBy('urutan')->orderBy('id')->pluck('id');
            $finalIds = $requestedIds->merge($existingIds->diff($requestedIds))->values();

            foreach ($finalIds as $index => $id) {
                MetodePembayaran::whereKey($id)->update([
                    'urutan' => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('metode-pembayaran.index')
            ->with('success', 'Urutan metode pembayaran berhasil diperbarui');
    }

    public function toggleStatus(MetodePembayaran $metodePembayaran)
    {
        $metodePembayaran->update([
            'status_aktif' => !$metodePembayaran->status_aktif,
        ]);

        return redirect()
            ->route('metode-pembayaran.index')
            ->with('success', 'Status metode pembayaran berhasil diperbarui');
    }
}
