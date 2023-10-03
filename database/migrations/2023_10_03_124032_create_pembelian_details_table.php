<?php

use App\Models\Barang;
use App\Models\Pembelian;
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
        Schema::create('pembelian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pembelian::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Barang::class)->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('pembelian_details');
    }
};
