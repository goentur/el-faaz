<?php

use App\Models\Pemasok;
use App\Models\Satuan;
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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pemasok::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Satuan::class)->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->smallInteger('stok');
            $table->bigInteger('hpp');
            $table->bigInteger('harga_jual');
            $table->bigInteger('harga_anggota');
            $table->string('foto')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
