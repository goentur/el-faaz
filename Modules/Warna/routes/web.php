<?php

use Illuminate\Support\Facades\Route;
use Modules\Warna\app\Http\Controllers\WarnaController;

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

Route::middleware('auth', 'can:warna')->group(function () {
    Route::middleware('role:developer')->group(function () {
        Route::get('warna/sampah', [WarnaController::class, 'sampah'])->name('warna.sampah');
        Route::post('warna/memulihkan', [WarnaController::class, 'memulihkan'])->name('warna.memulihkan');
        Route::post('warna/permanen', [WarnaController::class, 'permanen'])->name('warna.permanen');
    });
    Route::resource('warna', WarnaController::class);
});
