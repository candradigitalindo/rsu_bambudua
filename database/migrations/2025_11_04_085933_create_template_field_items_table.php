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
        Schema::create('template_field_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('template_field_id')->constrained('template_fields')->onDelete('cascade');
            $table->string('item_name'); // e.g., 'pemeriksaan_nilai', 'satuan', 'nilai_normal'
            $table->string('item_label'); // e.g., 'Pemeriksaan', 'Satuan', 'Nilai Normal'
            $table->string('item_type')->default('text'); // 'text', 'number', 'textarea'
            $table->string('placeholder')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            // Index untuk performa
            $table->index(['template_field_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_field_items');
    }
};
