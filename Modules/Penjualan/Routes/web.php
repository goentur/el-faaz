<?php

use Illuminate\Support\Facades\Route;
use Modules\Penjualan\Http\Controllers\PenjualanController;

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

Route::middleware('auth', 'can:penjualan')->prefix('penjualan')->group(function () {
    Route::get('', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::post('data-barang', [PenjualanController::class, 'dataBarang'])->name('penjualan.dataBarang');
    Route::post('cek-stok-barang-tersedia', [PenjualanController::class, 'cekStokBarangTersedia'])->name('penjualan.cekStokBarangTersedia');
    Route::post('data-anggota', [PenjualanController::class, 'dataAnggota'])->name('penjualan.dataAnggota');
    Route::post('selesai', [PenjualanController::class, 'selesai'])->name('penjualan.selesai');
});
Route::middleware('auth')->prefix('penjualan')->group(function () {
    Route::post('cetak-nota', [PenjualanController::class, 'cetakNota'])->name('penjualan.cetak-nota');
});
