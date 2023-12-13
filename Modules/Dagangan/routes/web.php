<?php

use Illuminate\Support\Facades\Route;
use Modules\Dagangan\app\Http\Controllers\DaganganController;

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

Route::middleware('auth', 'can:dagangan')->group(function () {
    Route::middleware('role:developer')->group(function () {
        Route::get('dagangan/sampah', [DaganganController::class, 'sampah'])->name('dagangan.sampah');
        Route::post('dagangan/memulihkan', [DaganganController::class, 'memulihkan'])->name('dagangan.memulihkan');
        Route::post('dagangan/permanen', [DaganganController::class, 'permanen'])->name('dagangan.permanen');
    });
    Route::resource('dagangan', DaganganController::class);
});

Route::middleware('auth')->group(function () {
    Route::post('dagangan/data', [DaganganController::class, 'data'])->name('dagangan.data');
});
