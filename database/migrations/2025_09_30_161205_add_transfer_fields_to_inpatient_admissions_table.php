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
        Schema::table('inpatient_admissions', function (Blueprint $table) {
            $table->datetime('transfer_date')->nullable()->after('discharge_date');
            $table->string('transfer_from')->nullable()->after('transfer_date');
            $table->string('transfer_to')->nullable()->after('transfer_from');
            $table->text('transfer_notes')->nullable()->after('transfer_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inpatient_admissions', function (Blueprint $table) {
            $table->dropColumn(['transfer_date', 'transfer_from', 'transfer_to', 'transfer_notes']);
        });
    }
};
