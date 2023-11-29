<?php

use Illuminate\Support\Facades\Route;
use Modules\Gudang\app\Http\Controllers\GudangController;

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

Route::middleware('auth', 'can:gudang')->group(function () {
    Route::middleware('role:developer')->group(function () {
        Route::get('gudang/sampah', [GudangController::class, 'sampah'])->name('gudang.sampah');
        Route::post('gudang/sampah/data', [GudangController::class, 'sampahDataGudang'])->name('gudang.sampah.data');
        Route::post('gudang/memulihkan', [GudangController::class, 'memulihkan'])->name('gudang.memulihkan');
        Route::post('gudang/permanen', [GudangController::class, 'permanen'])->name('gudang.permanen');
    });
    Route::post('gudang/data', [GudangController::class, 'dataGudang'])->name('gudang.data');
    Route::resource('gudang', GudangController::class);
});
