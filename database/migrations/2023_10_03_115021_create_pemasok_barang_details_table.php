<?php

use App\Models\BarangDetail;
use App\Models\Pemasok;
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
        Schema::create('pemasok_barang_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pemasok::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(BarangDetail::class)->constrained()->cascadeOnDelete();
            $table->smallInteger('stok');
            $table->bigInteger('harga_beli');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasok_barang_details');
    }
};
