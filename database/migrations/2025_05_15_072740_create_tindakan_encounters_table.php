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
        Schema::create('tindakan_encounters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('encounter_id')->constrained('encounters')->onDelete('cascade');
            $table->string('tindakan_id');
            $table->string('tindakan_name');
            $table->string('tindakan_description')->nullable();
            $table->bigInteger('tindakan_harga')->default(0);
            $table->integer('qty')->default(1);
            $table->bigInteger('total_harga')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindakan_encounters');
    }
};
