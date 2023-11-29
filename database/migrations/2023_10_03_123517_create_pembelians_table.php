<?php

use App\Models\Pemasok;
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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Pemasok::class)->constrained()->cascadeOnDelete();
            $table->bigInteger('tanggal');
            $table->bigInteger('total');
            $table->text('keterangan');
            $table->tinyInteger('status')->comment('1 = belum, 2 = sudah, 3 = masukan ke jurnal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
