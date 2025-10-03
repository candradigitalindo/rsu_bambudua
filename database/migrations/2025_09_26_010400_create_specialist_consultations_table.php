<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('specialist_consultations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('encounter_id');
            $table->uuid('requested_by');
            $table->uuid('specialist_id');
            $table->uuid('assigned_doctor_id')->nullable();
            $table->text('reason');
            $table->string('status')->default('requested'); // requested, scheduled, completed, cancelled
            $table->timestamp('scheduled_at')->nullable();
            $table->text('result_notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['encounter_id']);
            $table->index(['specialist_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialist_consultations');
    }
};