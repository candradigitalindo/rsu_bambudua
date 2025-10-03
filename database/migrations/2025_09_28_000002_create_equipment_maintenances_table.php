<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipment_maintenances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('equipment_id');
            $table->date('date');
            $table->string('type'); // preventive, corrective, calibration
            $table->string('performed_by');
            $table->text('notes')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('equipment_id')->references('id')->on('medical_equipments')->onDelete('cascade');
            $table->index(['equipment_id']);
            $table->index(['date']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenances');
    }
};
