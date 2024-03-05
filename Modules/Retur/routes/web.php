<?php

use Illuminate\Support\Facades\Route;
use Modules\Retur\app\Http\Controllers\ReturPenjualanController;

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

Route::middleware('auth', 'can:retur')->prefix('retur')->group(function () {
    Route::prefix('penjualan')->group(function () {
        Route::get('', [ReturPenjualanController::class, 'index'])->name('retur.penjualan.index');
        Route::post('data', [ReturPenjualanController::class, 'data'])->name('retur.penjualan.data');
        Route::get('detail/{id}', [ReturPenjualanController::class, 'detail'])->name('retur.penjualan.detail');
        Route::post('detail/daftar-barang', [ReturPenjualanController::class, 'daftarBarang'])->name('retur.penjualan.daftar.barang');
        Route::post('detail/daftar-barang-retur', [ReturPenjualanController::class, 'daftarBarangRetur'])->name('retur.penjualan.daftar.barang.retur');
        Route::post('detail/detail-barang', [ReturPenjualanController::class, 'detailBarang'])->name('retur.penjualan.detail.barang');
        Route::post('detail/simpan-retur', [ReturPenjualanController::class, 'simpanRetur'])->name('retur.penjualan.simpan.retur');
        Route::post('detail/hapus-retur', [ReturPenjualanController::class, 'hapusRetur'])->name('retur.penjualan.hapus.retur');
    });
});
