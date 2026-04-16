<?php

namespace App\Http\Controllers;

use App\Models\JenisKendaraan;
use App\Models\JenisPemilik;
use App\Models\Kendaraan;
use App\Models\PemilikKendaraan;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with([
            'UserRole.Role',
            'PemilikKendaraan.Kendaraan.JenisKendaraan',
        ])->latest()->paginate(10);

        $roles = Role::orderBy('nama_role')->get();

        $jenisKendaraans = JenisKendaraan::where('status_aktif', true)
            ->orderBy('nama_jenis_kendaraan')
            ->get();

        $modalType = null;
        $modalData = null;

        if (old('form_type') === 'create') {
            $modalType = 'create';
        }

        if (old('form_type') === 'edit' && old('edit_user_id')) {
            $selectedUser = $this->findUserForModal((int) old('edit_user_id'));

            if ($selectedUser) {
                $modalType = 'edit';
                $modalData = $this->buildUserPayload($selectedUser);
            }
        }

        if ($modalType === null) {
            $requestedModal = $request->query('modal');

            if (in_array($requestedModal, ['create', 'show', 'edit'], true)) {
                $modalType = $requestedModal;

                if (in_array($requestedModal, ['show', 'edit'], true) && $request->filled('selected_user')) {
                    $selectedUser = $this->findUserForModal((int) $request->query('selected_user'));

                    if ($selectedUser) {
                        $modalData = $this->buildUserPayload($selectedUser);
                    } else {
                        $modalType = null;
                    }
                }
            }
        }

        return view('users.index', compact(
            'users',
            'roles',
            'jenisKendaraans',
            'modalType',
            'modalData'
        ));
    }

    public function create()
    {
        return redirect()->route('users.index', [
            'modal' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'         => $validated['name'],
                'email'        => $validated['email'],
                'password'     => Hash::make($validated['password']),
                'alamat'       => $validated['alamat'] ?? null,
                'status_aktif' => true,
            ]);

            $this->syncSingleRole($user->id, (int) $validated['role_id']);

            if ($validated['tambah_kendaraan']) {
                $this->handleVehicle($user->id, $validated);
            }
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        return redirect()->route('users.index', [
            'modal' => 'show',
            'selected_user' => $user->id,
        ]);
    }

    public function edit(User $user)
    {
        return redirect()->route('users.index', [
            'modal' => 'edit',
            'selected_user' => $user->id,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $this->validateRequest($request, $user->id);

        DB::transaction(function () use ($validated, $user) {
            $payload = [
                'name'         => $validated['name'],
                'email'        => $validated['email'],
                'alamat'       => $validated['alamat'] ?? null,
                'status_aktif' => true,
            ];

            if (!empty($validated['password'])) {
                $payload['password'] = Hash::make($validated['password']);
            }

            $user->update($payload);

            $this->syncSingleRole($user->id, (int) $validated['role_id']);

            if ($validated['tambah_kendaraan']) {
                $this->handleVehicle($user->id, $validated);
            } else {
                $this->deactivateVehicleByUser($user->id);
            }
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            $this->deactivateVehicleByUser($user->id);

            UserRole::where('user_id', $user->id)->delete();

            $user->update([
                'status_aktif' => false,
            ]);
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dinonaktifkan.');
    }

    protected function validateRequest(Request $request, ?int $userId = null): array
    {
        $request->merge([
            'tambah_kendaraan' => $request->boolean('tambah_kendaraan'),
            'no_polisi' => $request->filled('no_polisi')
                ? strtoupper(trim((string) $request->input('no_polisi')))
                : null,
        ]);

        $emailUniqueRule = Rule::unique('users', 'email');
        if ($userId !== null) {
            $emailUniqueRule->ignore($userId);
        }

        return $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', $emailUniqueRule],
            'password' => $userId === null
                ? ['required', 'string', 'min:8']
                : ['nullable', 'string', 'min:8'],
            'alamat'   => ['nullable', 'string'],
            'role_id' => ['required', 'exists:roles,id'],
            'tambah_kendaraan' => ['required', 'boolean'],
            'no_polisi' => [
                Rule::requiredIf(fn () => $request->boolean('tambah_kendaraan')),
                'nullable',
                'string',
                'max:20',
            ],
            'jenis_kendaraan_id' => [
                Rule::requiredIf(fn () => $request->boolean('tambah_kendaraan')),
                'nullable',
                'exists:jenis_kendaraans,id',
            ],
            'merk'    => ['nullable', 'string', 'max:100'],
            'warna'   => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string'],
        ], [
            'role_id.required'            => 'Role wajib dipilih.',
            'role_id.exists'              => 'Role tidak ditemukan.',
            'password.required'           => 'Password wajib diisi.',
            'password.min'                => 'Password minimal 8 karakter.',
            'no_polisi.required'          => 'No polisi wajib diisi saat tambah kendaraan dipilih.',
            'jenis_kendaraan_id.required' => 'Jenis kendaraan wajib dipilih saat tambah kendaraan dipilih.',
            'jenis_kendaraan_id.exists'   => 'Jenis kendaraan tidak valid.',
        ]);
    }

    protected function syncSingleRole(int $userId, int $roleId): void
    {
        UserRole::where('user_id', $userId)->delete();

        UserRole::create([
            'user_id' => $userId,
            'role_id' => $roleId,
        ]);
    }

    protected function handleVehicle(int $userId, array $validated): void
    {
        $pemilik = PemilikKendaraan::updateOrCreate(
            ['user_id' => $userId],
            [
                'jenis_pemilik_id' => $this->resolveDefaultJenisPemilikId(),
                'status_aktif'     => true,
            ]
        );

        Kendaraan::updateOrCreate(
            ['pemilik_id' => $pemilik->id],
            [
                'no_polisi'          => $validated['no_polisi'],
                'jenis_kendaraan_id' => $validated['jenis_kendaraan_id'],
                'merk'               => $validated['merk'] ?? null,
                'warna'              => $validated['warna'] ?? null,
                'catatan'            => $validated['catatan'] ?? null,
                'status_aktif'       => true,
            ]
        );
    }

    protected function deactivateVehicleByUser(int $userId): void
    {
        $pemilik = PemilikKendaraan::where('user_id', $userId)->first();

        if (!$pemilik) {
            return;
        }

        Kendaraan::where('pemilik_id', $pemilik->id)->update([
            'status_aktif' => false,
        ]);

        $pemilik->update([
            'status_aktif' => false,
        ]);
    }

    protected function resolveDefaultJenisPemilikId(): int
    {
        $table = (new JenisPemilik())->getTable();

        $query = JenisPemilik::query();

        if (Schema::hasColumn($table, 'kode_jenis_pemilik')) {
            $query->where('kode_jenis_pemilik', 'PRIBADI');
        }

        if (Schema::hasColumn($table, 'status_aktif')) {
            $query->where('status_aktif', true);
        }

        $jenisPemilik = $query->first();

        if (!$jenisPemilik) {
            $jenisPemilik = JenisPemilik::query()->first();
        }

        if (!$jenisPemilik) {
            throw ValidationException::withMessages([
                'jenis_pemilik_id' => 'Master jenis pemilik belum tersedia. Tambahkan dulu data jenis pemilik.',
            ]);
        }

        return $jenisPemilik->id;
    }

    protected function findUserForModal(int $userId): ?User
    {
        return User::with([
            'UserRole.Role',
            'PemilikKendaraan.Kendaraan.JenisKendaraan',
        ])->find($userId);
    }

    protected function buildUserPayload(User $user): array
    {
        $selectedRole = $user->UserRole->first();
        $pemilik = $user->PemilikKendaraan;
        $kendaraan = optional($pemilik)->Kendaraan;

        return [
            'user' => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'alamat'       => $user->alamat,
                'status_aktif' => (bool) $user->status_aktif,
            ],
            'selected_role_id' => optional($selectedRole)->role_id,
            'selected_role_nama' => optional(optional($selectedRole)->Role)->nama_role,
            'has_vehicle' => (bool) ($kendaraan && $kendaraan->status_aktif),
            'pemilik' => $pemilik ? [
                'id'               => $pemilik->id,
                'user_id'          => $pemilik->user_id,
                'jenis_pemilik_id' => $pemilik->jenis_pemilik_id,
                'status_aktif'     => (bool) $pemilik->status_aktif,
            ] : null,
            'kendaraan' => $kendaraan ? [
                'id'                    => $kendaraan->id,
                'pemilik_id'            => $kendaraan->pemilik_id,
                'no_polisi'             => $kendaraan->no_polisi,
                'jenis_kendaraan_id'    => $kendaraan->jenis_kendaraan_id,
                'jenis_kendaraan_nama'  => optional($kendaraan->JenisKendaraan)->nama_jenis_kendaraan,
                'merk'                  => $kendaraan->merk,
                'warna'                 => $kendaraan->warna,
                'catatan'               => $kendaraan->catatan,
                'status_aktif'          => (bool) $kendaraan->status_aktif,
            ] : null,
        ];
    }
}
