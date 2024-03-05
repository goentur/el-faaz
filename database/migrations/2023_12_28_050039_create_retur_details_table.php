<?php

use App\Models\PemasokBarangDetail;
use App\Models\Retur;
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
        Schema::create('retur_details', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignIdFor(Retur::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('transaksi_detail_id')->comment("id bisa diambil dari transaksi penjualan detail ataupun pembelian detail");
            $table->foreignIdFor(PemasokBarangDetail::class)->constrained()->cascadeOnDelete();
            $table->bigInteger('tanggal');
            $table->smallInteger('kuantitas');
            $table->bigInteger('harga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_details');
    }
};
