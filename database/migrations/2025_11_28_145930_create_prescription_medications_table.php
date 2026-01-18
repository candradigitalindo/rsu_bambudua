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
        Schema::create('prescription_medications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('prescription_order_id');
            $table->string('medication_name');
            $table->string('dosage');
            $table->string('route'); // Oral, IV, IM, etc.
            $table->string('frequency'); // BID, TID, QID, etc.
            $table->json('scheduled_times')->nullable(); // ["08:00", "14:00", "20:00"]
            $table->text('instructions')->nullable();
            $table->integer('duration_days')->nullable();
            $table->timestamps();

            $table->index('prescription_order_id');
            $table->index('medication_name');

            // Foreign key constraint
            $table->foreign('prescription_order_id')->references('id')->on('prescription_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_medications');
    }
};
