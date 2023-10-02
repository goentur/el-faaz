<?php

use Illuminate\Support\Facades\Route;
use Modules\PeranPengguna\Http\Controllers\PeranPenggunaController;

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

Route::middleware('auth', 'can:peran pengguna')->group(function () {
    Route::resource('peran-pengguna', PeranPenggunaController::class);
});
