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
        Schema::create('inpatient_visits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inpatient_admission_id')->constrained('inpatient_admissions')->onDelete('cascade');
            $table->foreignId('dokter_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('perawat_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('tanggal_visit');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inpatient_visits');
    }
};
