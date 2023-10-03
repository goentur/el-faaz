<?php

use App\Models\Anggota;
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
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('anggota_id')->nullable();
            $table->string('nama_pembeli');
            $table->bigInteger('tanggal');
            $table->bigInteger('total');
            $table->tinyInteger('jenis')->comment('1 = penjualan anggota, 2 = penjualan non anggota, 3 = penjualan marketplace');
            $table->tinyInteger('status')->comment('1 = belum, 2 = sudah, 3 = masukan ke jurnal');
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
