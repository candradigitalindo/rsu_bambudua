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
        Schema::create('patient_companions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained('inpatient_admissions')->onDelete('cascade');
            $table->string('name');
            $table->string('nik')->nullable();
            $table->string('phone')->nullable();
            $table->string('relation')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_companions');
    }
};
