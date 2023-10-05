<?php

use Illuminate\Support\Facades\Route;
use Modules\SatuanBarang\Http\Controllers\SatuanBarangController;

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

Route::middleware('auth', 'can:satuan barang')->group(function () {
    Route::middleware('role:developer')->group(function () {
        Route::get('satuan-barang/sampah', [SatuanBarangController::class, 'sampah'])->name('satuan-barang.sampah');
        Route::post('satuan-barang/memulihkan', [SatuanBarangController::class, 'memulihkan'])->name('satuan-barang.memulihkan');
        Route::post('satuan-barang/permanen', [SatuanBarangController::class, 'permanen'])->name('satuan-barang.permanen');
    });
    Route::resource('satuan-barang', SatuanBarangController::class);
});
