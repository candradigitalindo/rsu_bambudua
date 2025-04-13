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
        Schema::create('tindakan_bahan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tindakan_id')->constrained('tindakans')->onDelete('cascade');
            $table->foreignUuid('bahan_id')->constrained('bahans')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindakan_bahan');
    }
};
