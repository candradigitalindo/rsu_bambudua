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
        Schema::create('nursing_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('admission_id'); // inpatient_admissions uses UUID
            $table->unsignedBigInteger('nurse_id'); // users table uses auto-increment ID
            $table->text('note');
            $table->enum('note_type', ['general', 'observation', 'medication', 'procedure'])->default('general');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            // Foreign keys with proper types
            $table->foreign('admission_id')->references('id')->on('inpatient_admissions')->onDelete('cascade');
            $table->foreign('nurse_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['admission_id', 'recorded_at']);
            $table->index(['nurse_id', 'recorded_at']);
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nursing_notes');
    }
};
