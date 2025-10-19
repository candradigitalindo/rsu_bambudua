<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reagent_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reagent_id');
            $table->enum('type', ['in', 'out']); // Initial types
            $table->integer('qty');
            $table->uuid('lab_request_item_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('reagent_id');
            $table->index('lab_request_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reagent_transactions');
    }
};
