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
        Schema::table('reagent_transactions', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->after('reagent_id');
            $table->date('expiry_date')->nullable()->after('qty');
            // Modify the 'type' column to include 'adjustment'
            $table->enum('type', ['in', 'out', 'adjustment'])->change();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reagent_transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn(['user_id', 'expiry_date']);
            $table->enum('type', ['in', 'out'])->change();
        });
    }
};
