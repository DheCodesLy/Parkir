<?php

use App\Http\Controllers\JenisKendaraanController;
use App\Http\Controllers\JenisPemilikController;
use App\Http\Controllers\LahanParkirController;
use App\Http\Controllers\MetodePembayaranController;
use App\Http\Controllers\ParkirController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::resource('jenis-kendaraan', JenisKendaraanController::class);
Route::resource('jenis-pemilik', JenisPemilikController::class);
Route::resource('role', RoleController::class);
Route::resource('metode-pembayaran', MetodePembayaranController::class);

Route::get('/transaksi-parkirs', [ParkirController::class, 'index'])->name('transaksi-parkirs.index');
Route::get('/transaksi-parkirs/{id}', [ParkirController::class, 'show'])->name('transaksi-parkirs.show');
Route::post('/transaksi-parkirs/masuk', [ParkirController::class, 'masuk'])->name('transaksi-parkirs.masuk');
Route::post('/transaksi-parkirs/{id}/keluar', [ParkirController::class, 'keluar'])->name('transaksi-parkirs.keluar');
Route::get('/LahanParkir/check-nama', [LahanParkirController::class, 'checkNamaLahan'])->name('LahanParkir.check-nama');
Route::resource('LahanParkir', LahanParkirController::class);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
