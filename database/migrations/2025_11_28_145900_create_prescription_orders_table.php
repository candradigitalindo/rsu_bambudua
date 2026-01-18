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
        Schema::create('prescription_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('encounter_id')->constrained('encounters')->onDelete('cascade');
            $table->unsignedBigInteger('doctor_id');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->enum('pharmacy_status', ['Pending', 'Verified', 'Ready', 'Dispensed'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('encounter_id');
            $table->index('doctor_id');
            $table->index(['status', 'pharmacy_status']);

            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_orders');
    }
};
