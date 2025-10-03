<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiology_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('radiology_request_id')->constrained('radiology_requests')->cascadeOnDelete();
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end')->nullable();
            $table->string('modality')->nullable(); // X-ray, USG, CT, MRI, etc
            $table->string('room')->nullable();
            $table->unsignedBigInteger('radiographer_id')->nullable(); // users.id
            $table->text('preparation')->nullable();
            $table->string('priority')->default('routine'); // routine|urgent|stat
            $table->string('status')->default('scheduled'); // scheduled|in_progress|finished|canceled|no_show
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('radiographer_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_schedules');
    }
};
