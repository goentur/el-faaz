<?php

use App\Models\Barang;
use App\Models\Ukuran;
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
        Schema::create('barang_ukurans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Barang::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Ukuran::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_ukurans');
    }
};
