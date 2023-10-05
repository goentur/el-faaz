<?php

use Illuminate\Support\Facades\Route;
use Modules\Pemasok\Http\Controllers\PemasokController;

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

Route::middleware('auth', 'can:pemasok')->group(function () {
    Route::middleware('role:developer')->group(function () {
        Route::get('pemasok/sampah', [PemasokController::class, 'sampah'])->name('pemasok.sampah');
        Route::post('pemasok/memulihkan', [PemasokController::class, 'memulihkan'])->name('pemasok.memulihkan');
        Route::post('pemasok/permanen', [PemasokController::class, 'permanen'])->name('pemasok.permanen');
    });
    Route::resource('pemasok', PemasokController::class);
});
