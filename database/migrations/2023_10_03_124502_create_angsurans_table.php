<?php

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
        Schema::create('angsurans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaksi_id')->comment("id bisa diambil dari transaksi penjualan ataupun pembelian");
            $table->tinyInteger('jenis')->comment('1 = pembelian, 2 = penjualan');
            $table->tinyInteger('status')->comment('1 = belum, 2 = selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angsurans');
    }
};
