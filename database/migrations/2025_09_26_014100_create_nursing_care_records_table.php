<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nursing_care_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('encounter_id');
            $table->uuid('nurse_id')->nullable();
            $table->string('shift', 10)->nullable(); // Pagi/Siang/Malam
            $table->smallInteger('systolic')->nullable();
            $table->smallInteger('diastolic')->nullable();
            $table->smallInteger('heart_rate')->nullable();
            $table->smallInteger('resp_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->smallInteger('spo2')->nullable();
            $table->tinyInteger('pain_scale')->nullable(); // 0-10
            $table->text('nursing_diagnosis')->nullable();
            $table->text('interventions')->nullable();
            $table->text('evaluation_notes')->nullable();
            $table->timestamps();

            $table->index('encounter_id');
            $table->index('nurse_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nursing_care_records');
    }
};