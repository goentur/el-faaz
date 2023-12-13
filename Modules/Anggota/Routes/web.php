<?php

use Illuminate\Support\Facades\Route;
use Modules\Anggota\app\Http\Controllers\AnggotaController;
use Modules\Anggota\app\Http\Controllers\AnggotaDetailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth', 'can:anggota')->group(function () {
    Route::middleware('role:developer')->prefix('anggota')->group(function () {
        Route::get('sampah', [AnggotaController::class, 'sampah'])->name('anggota.sampah');
        Route::post('memulihkan', [AnggotaController::class, 'memulihkan'])->name('anggota.memulihkan');
        Route::post('permanen', [AnggotaController::class, 'permanen'])->name('anggota.permanen');
    });
    Route::prefix('anggota/detail')->group(function () {
        Route::post('daftar-barang', [AnggotaDetailController::class, 'index'])->name('anggota.detail.daftar-barang');
        Route::get('cetak-tagihan/{id}', [AnggotaDetailController::class, 'cetakTagihan'])->name('anggota.detail.cetak-tagihan');
    });
    Route::resource('anggota', AnggotaController::class);
});
