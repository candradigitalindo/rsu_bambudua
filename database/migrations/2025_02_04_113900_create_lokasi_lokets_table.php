<?php

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
        Schema::create('lokasi_lokets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('lokasi_loket');
            $table->string('prefix_antrian');
            $table->timestamps();
        });
        Schema::dropIfExists('lokets');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasi_lokets');
    }
};
