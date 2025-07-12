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
        Schema::create('inpatient_admissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('encounter_id')->constrained('encounters')->onDelete('cascade');
            $table->foreignUuid('pasien_id')->constrained('pasiens')->onDelete('cascade');
            $table->foreignId('dokter_id')->nullable()->constrained('users')->nullOnDelete('cascade');
            $table->string('nama_dokter')->nullable();
            $table->foreignUuid('ruangan_id')->nullable()->constrained('ruangans')->nullOnDelete('cascade');
            $table->string('bed_number');
            $table->text('admission_reason')->nullable();
            $table->dateTime('admission_date');
            $table->enum('status', ['active', 'discharged'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inpatient_admissions');
    }
};
