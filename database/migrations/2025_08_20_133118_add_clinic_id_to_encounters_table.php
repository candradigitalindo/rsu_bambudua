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
        Schema::table('encounters', function (Blueprint $table) {
            $table->foreignUuid('clinic_id')->nullable()->after('type')->constrained('clinics')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->after('clinic_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encounters', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn('clinic_id');
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
