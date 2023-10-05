<?php

use Illuminate\Support\Facades\Route;
use Modules\Anggota\Http\Controllers\AnggotaController;

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
    Route::middleware('role:developer')->group(function () {
        Route::get('anggota/sampah', [AnggotaController::class, 'sampah'])->name('anggota.sampah');
        Route::post('anggota/memulihkan', [AnggotaController::class, 'memulihkan'])->name('anggota.memulihkan');
        Route::post('anggota/permanen', [AnggotaController::class, 'permanen'])->name('anggota.permanen');
    });
    Route::resource('anggota', AnggotaController::class);
});
