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
        Schema::create('medication_administrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('prescription_medication_id');
            $table->foreign('prescription_medication_id')->references('id')->on('prescription_medications')->onDelete('cascade');
            $table->uuid('administered_by');
            $table->foreign('administered_by')->references('id')->on('users')->onDelete('restrict');
            $table->dateTime('administered_at');
            $table->string('dose_given')->nullable(); // Dosis yang diberikan
            $table->text('notes')->nullable(); // Catatan pemberian
            $table->text('patient_response')->nullable(); // Respon pasien
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_administrations');
    }
};
