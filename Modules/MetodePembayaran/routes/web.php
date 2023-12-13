<?php

use Illuminate\Support\Facades\Route;
use Modules\MetodePembayaran\app\Http\Controllers\MetodePembayaranController;

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

Route::middleware('auth', 'can:metode pembayaran')->group(function () {
    Route::middleware('role:developer')->group(function () {
        Route::get('metode-pembayaran/sampah', [MetodePembayaranController::class, 'sampah'])->name('metode-pembayaran.sampah');
        Route::post('metode-pembayaran/memulihkan', [MetodePembayaranController::class, 'memulihkan'])->name('metode-pembayaran.memulihkan');
        Route::post('metode-pembayaran/permanen', [MetodePembayaranController::class, 'permanen'])->name('metode-pembayaran.permanen');
    });
    Route::resource('metode-pembayaran', MetodePembayaranController::class);
});
