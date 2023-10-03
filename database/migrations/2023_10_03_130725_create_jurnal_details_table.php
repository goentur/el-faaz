<?php

use App\Models\Akun;
use App\Models\Jurnal;
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
        Schema::create('jurnal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Akun::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Jurnal::class)->constrained()->cascadeOnDelete();
            $table->bigInteger('debet');
            $table->bigInteger('kredit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_details');
    }
};
