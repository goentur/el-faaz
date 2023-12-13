<?php

use Illuminate\Support\Facades\Route;
use Modules\Riwayat\app\Http\Controllers\RiwayatPembelianController;
use Modules\Riwayat\app\Http\Controllers\RiwayatPenjualanController;

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

Route::middleware('auth', 'can:riwayat')->prefix('riwayat')->group(function () {
    Route::prefix('pembelian')->group(function () {
        Route::get('', [RiwayatPembelianController::class, 'index'])->name('riwayat.pembelian.index');
        Route::post('data', [RiwayatPembelianController::class, 'data'])->name('riwayat.pembelian.data');
        Route::get('detail/{id}', [RiwayatPembelianController::class, 'detail'])->name('riwayat.pembelian.detail');
        Route::post('detail/daftar-barang', [RiwayatPembelianController::class, 'daftarBarang'])->name('riwayat.pembelian.daftar.barang');
        Route::post('detail/data-angsuran', [RiwayatPembelianController::class, 'detailDataAngsuran'])->name('riwayat.pembelian.detail.data.angsuran');
    });
    Route::prefix('penjualan')->group(function () {
        Route::get('', [RiwayatPenjualanController::class, 'index'])->name('riwayat.penjualan.index');
        Route::post('data', [RiwayatPenjualanController::class, 'data'])->name('riwayat.penjualan.data');
        Route::get('detail/{id}', [RiwayatPenjualanController::class, 'detail'])->name('riwayat.penjualan.detail');
        Route::post('detail/daftar-barang', [RiwayatPenjualanController::class, 'daftarBarang'])->name('riwayat.penjualan.daftar.barang');
        Route::post('detail/data-angsuran', [RiwayatPenjualanController::class, 'detailDataAngsuran'])->name('riwayat.penjualan.detail.data.angsuran');
    });
});
