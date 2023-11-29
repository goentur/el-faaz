<?php

use Illuminate\Support\Facades\Route;
use Modules\Pembelian\app\Http\Controllers\PembelianController;

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

Route::middleware('auth', 'can:pembelian')->prefix('pembelian')->group(function () {
    Route::get('', [PembelianController::class, 'index'])->name('pembelian.index');
    Route::post('data-barang', [PembelianController::class, 'dataBarang'])->name('pembelian.dataBarang');
    Route::post('selesai', [PembelianController::class, 'selesai'])->name('pembelian.selesai');
});
