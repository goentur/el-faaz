<?php

use Illuminate\Support\Facades\Route;
use Modules\Angsuran\app\Http\Controllers\AngsuranHutangDagangController;
use Modules\Angsuran\app\Http\Controllers\AngsuranPiutangDagangController;

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

Route::middleware('auth', 'can:angsuran')->prefix('angsuran')->group(function () {
    Route::prefix('hutang-dagang')->group(function () {
        Route::get('', [AngsuranHutangDagangController::class, 'index'])->name('angsuran.hutang-dagang.index');
        Route::post('data', [AngsuranHutangDagangController::class, 'data'])->name('angsuran.hutang-dagang.data');
        Route::get('detail/{id}', [AngsuranHutangDagangController::class, 'detail'])->name('angsuran.hutang-dagang.detail');
        Route::post('detail/daftar-barang', [AngsuranHutangDagangController::class, 'daftarBarang'])->name('angsuran.hutang-dagang.daftar.barang');
        Route::post('detail/data-angsuran', [AngsuranHutangDagangController::class, 'detailDataAngsuran'])->name('angsuran.hutang-dagang.detail.data.angsuran');
        Route::post('detail/tambah-angsuran', [AngsuranHutangDagangController::class, 'tambahAngsuran'])->name('angsuran.hutang-dagang.detail.tambah-angsuran');
    });
    Route::prefix('piutang-dagang')->group(function () {
        Route::get('', [AngsuranPiutangDagangController::class, 'index'])->name('angsuran.piutang-dagang.index');
        Route::post('data', [AngsuranPiutangDagangController::class, 'data'])->name('angsuran.piutang-dagang.data');
        Route::get('detail/{id}', [AngsuranPiutangDagangController::class, 'detail'])->name('angsuran.piutang-dagang.detail');
        Route::post('detail/daftar-barang', [AngsuranPiutangDagangController::class, 'daftarBarang'])->name('angsuran.piutang-dagang.daftar.barang');
        Route::post('detail/data-angsuran', [AngsuranPiutangDagangController::class, 'detailDataAngsuran'])->name('angsuran.piutang-dagang.detail.data.angsuran');
        Route::post('detail/tambah-angsuran', [AngsuranPiutangDagangController::class, 'tambahAngsuran'])->name('angsuran.piutang-dagang.detail.tambah-angsuran');
    });
});
