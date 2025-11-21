<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiology_supplies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('unit')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('warning_stock')->default(0);
            $table->timestamps();
        });

        Schema::create('radiology_supply_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('supply_id')->constrained('radiology_supplies')->onDelete('cascade');
            $table->string('batch_number');
            $table->integer('quantity');
            $table->integer('remaining_quantity');
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('radiology_supply_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('supply_id')->constrained('radiology_supplies')->onDelete('cascade');
            $table->foreignUuid('batch_id')->nullable()->constrained('radiology_supply_batches')->onDelete('set null');
            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_supply_transactions');
        Schema::dropIfExists('radiology_supply_batches');
        Schema::dropIfExists('radiology_supplies');
    }
};
