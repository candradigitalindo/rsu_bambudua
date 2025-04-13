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
        Schema::create('historibahans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bahan_id')->constrained('bahans')->onDelete('cascade');
            $table->integer('quantity');
            $table->date('expired_at')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historibahans');
    }
};
