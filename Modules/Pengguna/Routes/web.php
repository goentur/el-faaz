<?php

use Illuminate\Support\Facades\Route;
use Modules\Pengguna\Http\Controllers\PenggunaController;

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

Route::middleware('auth', 'can:pengguna')->group(function () {
    Route::middleware('role:developer')->group(function () {
        Route::get('pengguna/sampah', [PenggunaController::class, 'sampah'])->name('pengguna.sampah');
        Route::post('pengguna/memulihkan', [PenggunaController::class, 'memulihkan'])->name('pengguna.memulihkan');
        Route::post('pengguna/permanen', [PenggunaController::class, 'permanen'])->name('pengguna.permanen');
    });
    Route::resource('pengguna', PenggunaController::class);
});
