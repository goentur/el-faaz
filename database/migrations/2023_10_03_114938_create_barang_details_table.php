<?php

use App\Models\Barang;
use App\Models\Satuan;
use App\Models\Warna;
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
        Schema::create('barang_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Barang::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Satuan::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Warna::class)->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->text('foto')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_details');
    }
};
