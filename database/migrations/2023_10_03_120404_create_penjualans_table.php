<?php

use App\Models\MetodePembayaran;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(MetodePembayaran::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('anggota_id')->nullable();
            $table->string('nama_pembeli');
            $table->bigInteger('tanggal');
            $table->bigInteger('bayar');
            $table->bigInteger('total');
            $table->bigInteger('ongkir')->nullable();
            $table->tinyInteger('jenis')->comment('1 = penjualan anggota, 2 = penjualan non anggota, 3 = penjualan marketplace');
            $table->tinyInteger('status')->comment('1 = belum, 2 = sudah, 3 = PO tapi lunas, 4 = jurnal lunas, 5 = jurnal belum lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
