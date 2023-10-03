<?php

use App\Models\Barang;
use App\Models\Penjualan;
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
        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Penjualan::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Barang::class)->constrained()->cascadeOnDelete();
            $table->smallInteger('kuantitas');
            $table->bigInteger('harga');
            $table->tinyInteger('status')->comment('1 = belum tersedia, 2 = sudah tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
    }
};