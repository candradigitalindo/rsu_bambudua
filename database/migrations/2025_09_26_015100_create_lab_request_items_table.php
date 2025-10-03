<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lab_request_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lab_request_id');
            $table->uuid('test_id')->nullable();
            $table->string('test_name');
            $table->integer('price')->default(0);
            $table->string('result_value')->nullable();
            $table->string('result_unit')->nullable();
            $table->string('result_reference')->nullable();
            $table->text('result_notes')->nullable();
            $table->timestamps();

            $table->index('lab_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_request_items');
    }
};