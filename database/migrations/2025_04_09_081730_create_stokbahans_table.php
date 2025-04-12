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
        Schema::create('stokbahans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bahan_id')->constrained('bahans')->onDelete('cascade');
            $table->boolean('is_available')->default(true);
            $table->date('expired_at')->nullable();
            $table->timestamp('date_used')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stokbahans');
    }
};
