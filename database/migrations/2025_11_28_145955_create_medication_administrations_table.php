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
            $table->foreignUuid('prescription_medication_id');
            $table->foreignUuid('admission_id')->constrained('inpatient_admissions')->onDelete('cascade');
            $table->unsignedBigInteger('nurse_id');
            $table->datetime('administered_at');
            $table->enum('status', [
                'Given',
                'Given Late',
                'Refused',
                'Held',
                'Not Available',
                'Patient NPO',
                'Patient Sleeping'
            ]);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['prescription_medication_id', 'administered_at'], 'idx_med_admin_med_time');
            $table->index('admission_id', 'idx_med_admin_admission');
            $table->index('nurse_id', 'idx_med_admin_nurse');

            $table->foreign('prescription_medication_id')->references('id')->on('prescription_medications')->onDelete('cascade');
            $table->foreign('nurse_id')->references('id')->on('users')->onDelete('cascade');
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
