<?php

use Illuminate\Support\Facades\Route;
use Modules\Barang\Http\Controllers\BarangController;

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

Route::middleware('auth', 'can:barang')->group(function () {
    Route::middleware('role:developer')->group(function () {
        Route::get('barang/sampah', [BarangController::class, 'sampah'])->name('barang.sampah');
        Route::post('barang/memulihkan', [BarangController::class, 'memulihkan'])->name('barang.memulihkan');
        Route::post('barang/permanen', [BarangController::class, 'permanen'])->name('barang.permanen');
    });
    Route::resource('barang', BarangController::class);
});
