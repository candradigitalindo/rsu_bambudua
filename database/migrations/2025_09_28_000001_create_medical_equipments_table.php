<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medical_equipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('asset_tag')->nullable();
            $table->string('location')->nullable();
            $table->string('vendor')->nullable();
            $table->string('status')->default('available');
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->date('last_calibration_date')->nullable();
            $table->date('next_calibration_due')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['status']);
            $table->index(['location']);
            $table->index(['vendor']);
            $table->index(['next_calibration_due']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_equipments');
    }
};
