<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('jenis_pemeriksaan_id')->constrained('jenis_pemeriksaan_penunjangs')->onDelete('cascade');
            $table->string('field_name'); // e.g., 'aorta_root_diam'
            $table->string('field_label'); // e.g., 'Aorta Root diam (mm)'
            $table->string('field_type'); // e.g., 'text', 'number', 'textarea'
            $table->string('placeholder')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_fields');
    }
};
