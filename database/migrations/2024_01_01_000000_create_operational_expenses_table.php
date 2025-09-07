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
        Schema::create('operational_expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->timestamps();
        });
    }
};
