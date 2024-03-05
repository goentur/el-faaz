<?php

use Illuminate\Support\Facades\Route;
use Modules\Akun\Http\Controllers\AkunController;

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

Route::middleware('auth')->group(function () {
    Route::middleware('can:akun')->group(function () {
        Route::middleware('role:developer')->group(function () {
            Route::get('akun/sampah', [AkunController::class, 'sampah'])->name('akun.sampah');
            Route::post('akun/memulihkan', [AkunController::class, 'memulihkan'])->name('akun.memulihkan');
            Route::post('akun/permanen', [AkunController::class, 'permanen'])->name('akun.permanen');
        });
        Route::resource('akun', AkunController::class);
    });
    Route::post('akun/data', [AkunController::class, 'data'])->name('akun.data');
});
