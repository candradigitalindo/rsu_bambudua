<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lab_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('encounter_id');
            $table->uuid('requested_by');
            $table->string('status')->default('requested');
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->integer('total_charge')->default(0);
            $table->boolean('charged')->default(false);
            $table->timestamps();

            $table->index('encounter_id');
            $table->index('requested_by');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_requests');
    }
};