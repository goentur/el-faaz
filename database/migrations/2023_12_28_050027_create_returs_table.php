<?php

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
        Schema::create('returs', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('transaksi_id')->comment("id bisa diambil dari transaksi penjualan ataupun pembelian");
            $table->bigInteger('tanggal');
            $table->bigInteger('total');
            $table->tinyInteger('jenis')->comment('1 = pembelian, 2 = penjualan');
            $table->tinyInteger('status')->comment('1 = belum, 3 = masuk ke jurnal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returs');
    }
};
