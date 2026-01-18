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
        Schema::table('prescription_orders', function (Blueprint $table) {
            $table->text('pharmacy_notes')->nullable()->after('notes');
            $table->timestamp('pharmacy_processed_at')->nullable()->after('pharmacy_notes');
            $table->unsignedBigInteger('pharmacy_processed_by')->nullable()->after('pharmacy_processed_at');

            $table->foreign('pharmacy_processed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_orders', function (Blueprint $table) {
            $table->dropForeign(['pharmacy_processed_by']);
            $table->dropColumn(['pharmacy_notes', 'pharmacy_processed_at', 'pharmacy_processed_by']);
        });
    }
};
