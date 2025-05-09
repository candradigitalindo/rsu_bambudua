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
        Schema::create('tanda_vitals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // foreign key uuid ke encounter
            $table->foreignUuid('encounter_id')->references('id')->on('encounters')->onDelete('cascade');
            $table->string('nadi')->nullable();
            $table->string('pernapasan')->nullable();
            $table->string('sistolik')->nullable();
            $table->string('diastolik')->nullable();
            $table->string('suhu')->nullable();
            $table->string('berat_badan')->nullable();
            $table->string('tinggi_badan')->nullable();
            $table->string('kesadaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_vitals');
    }
};
