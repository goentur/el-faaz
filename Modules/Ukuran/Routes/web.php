<?php

use Illuminate\Support\Facades\Route;
use Modules\Ukuran\Http\Controllers\UkuranController;

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

Route::middleware('auth', 'can:ukuran')->group(function () {
    Route::middleware('role:developer')->group(function () {
        Route::get('ukuran/sampah', [UkuranController::class, 'sampah'])->name('ukuran.sampah');
        Route::post('ukuran/memulihkan', [UkuranController::class, 'memulihkan'])->name('ukuran.memulihkan');
        Route::post('ukuran/permanen', [UkuranController::class, 'permanen'])->name('ukuran.permanen');
    });
    Route::resource('ukuran', UkuranController::class);
});
