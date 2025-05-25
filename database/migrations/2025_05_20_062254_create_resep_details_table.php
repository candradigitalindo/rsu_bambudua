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
        Schema::create('resep_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('resep_id')->constrained('reseps')->cascadeOnDelete();
            $table->string('nama_obat');
            $table->integer('qty')->default(1);
            $table->string('aturan_pakai')->nullable();
            // expired_at
            $table->date('expired_at')->nullable();
            $table->string('product_apotek_id')->nullable();
            $table->integer('harga')->default(0);
            $table->integer('total_harga')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_details');
    }
};
