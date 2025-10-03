<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiology_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->foreignUuid('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->foreignUuid('jenis_pemeriksaan_id')->constrained('jenis_pemeriksaan_penunjangs')->restrictOnDelete();
            $table->unsignedBigInteger('dokter_id'); // users.id
            $table->foreign('dokter_id')->references('id')->on('users')->restrictOnDelete();
            $table->string('status')->default('requested'); // requested|processing|completed|canceled
            $table->decimal('price', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_requests');
    }
};
