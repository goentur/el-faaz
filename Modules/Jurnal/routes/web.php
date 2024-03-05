<?php

use Illuminate\Support\Facades\Route;
use Modules\Jurnal\App\Http\Controllers\Pembelian\LunasController as PembelianLunasController;
use Modules\Jurnal\App\Http\Controllers\Penjualan\LunasController as PenjualanLunasController;

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

Route::middleware('auth', 'can:jurnal')->prefix('jurnal')->group(function () {
    Route::prefix('penjualan')->group(function () {
        Route::prefix('lunas')->group(function () {
            Route::get('', [PenjualanLunasController::class, 'index'])->name('jurnal.penjualan.lunas.index');
            Route::post('data', [PenjualanLunasController::class, 'data'])->name('jurnal.penjualan.lunas.data');
            Route::post('simpan', [PenjualanLunasController::class, 'simpan'])->name('jurnal.penjualan.lunas.simpan');
        });
    });
    Route::prefix('pembelian')->group(function () {
        Route::prefix('lunas')->group(function () {
            Route::get('', [PembelianLunasController::class, 'index'])->name('jurnal.pembelian.lunas.index');
            Route::post('data', [PembelianLunasController::class, 'data'])->name('jurnal.pembelian.lunas.data');
            Route::post('simpan', [PembelianLunasController::class, 'simpan'])->name('jurnal.pembelian.lunas.simpan');
        });
    });
});
