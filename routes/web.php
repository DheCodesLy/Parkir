<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    JenisKendaraanController,
    JenisPemilikController,
    LahanParkirController,
    MetodePembayaranController,
    ParkirController,
    ProfileController,
    RoleController,
    TarifParkirController,
    UserController
};

// --- PUBLIC ROUTES ---
Route::redirect('/', '/login');

// --- AUTHENTICATED ROUTES ---
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // 2. TRANSAKSI PARKIR (ParkirPro Core)
    // PERHATIKAN: Route statis (form-masuk, form-keluar) harus di atas route parameter {id}
    Route::controller(ParkirController::class)->prefix('transaksi-parkirs')->name('transaksi-parkirs.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/form-masuk', 'formMasuk')->name('masuk');
        Route::get('/form-keluar', 'formKeluar')->name('form-keluar'); // Route ini sekarang aman dari 404
        Route::get('/parkir/autocomplete-plat', [ParkirController::class, 'autocompletePlat'])->name('autocomplete.plat');
        Route::get('/{id}', 'show')->name('show');

        Route::post('/masuk', 'masuk')->name('akses-masuk');
        Route::post('/pilih-lahan', 'pilihLahan')->name('pilih-lahan');
        Route::post('/{id}/keluar', 'keluar')->name('keluar');
    });

    // 3. MASTER DATA: LAHAN PARKIR
    Route::get('/LahanParkir/check-nama', [LahanParkirController::class, 'checkNamaLahan'])->name('LahanParkir.check-nama');
    Route::resource('LahanParkir', LahanParkirController::class);

    // 4. MASTER DATA: TARIF PARKIR
    Route::controller(TarifParkirController::class)->prefix('tarif-parkir')->name('tarif-parkirs.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/berlaku', 'berlaku')->name('berlaku');
        Route::get('/kendaraan-by-lahan', 'kendaraanByLahan')->name('kendaraan-by-lahan');
        Route::get('/{tarifParkir}', 'show')->name('show');

        Route::post('/', 'store')->name('store');
        Route::patch('/{tarifParkir}/nonaktifkan', 'nonaktifkan')->name('nonaktifkan');
    });

    // 5. MASTER DATA: METODE PEMBAYARAN
    Route::controller(MetodePembayaranController::class)->prefix('metode-pembayaran')->name('metode-pembayaran.')->group(function () {
        Route::put('/reorder', 'reorder')->name('reorder');
        Route::patch('/{metodePembayaran}/toggle-status', 'toggleStatus')->name('toggle-status');
    });
    Route::resource('metode-pembayaran', MetodePembayaranController::class)->except(['reorder', 'toggleStatus']);

    // 6. MANAJEMEN USER & ROLE
    Route::resource('users', UserController::class);
    Route::resource('role', RoleController::class);
    Route::resource('jenis-kendaraan', JenisKendaraanController::class);
    Route::resource('jenis-pemilik', JenisPemilikController::class);

    // 7. PROFILE SETTINGS
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

});

require __DIR__.'/auth.php';
