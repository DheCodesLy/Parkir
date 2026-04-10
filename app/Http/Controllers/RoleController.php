<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::latest()->get();
        return view('Role.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_role' => ['required', 'string', 'max:255', 'unique:roles,nama_role'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $validated['nama_role'] = Str::title(trim($validated['nama_role']));
        $validated['kode_role'] = Str::slug($validated['nama_role'], '-');

        Role::create($validated);

        return redirect()->route('role.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'nama_role' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'nama_role')->ignore($role->id),
            ],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $validated['nama_role'] = Str::title(trim($validated['nama_role']));
        $validated['kode_role'] = Str::slug($validated['nama_role'], '-');

        $role->update($validated);

        return redirect()->route('role.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy(Request $request, Role $role)
    {
        $role->delete();

        return redirect()->route('role.index')->with('success', 'Data berhasil dihapus.');
    }
}
