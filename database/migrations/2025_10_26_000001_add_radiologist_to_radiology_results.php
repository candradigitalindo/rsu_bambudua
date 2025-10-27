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
        Schema::table('radiology_results', function (Blueprint $table) {
            $table->unsignedBigInteger('radiologist_id')->nullable()->after('radiology_request_id');
            $table->foreign('radiologist_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('radiology_results', function (Blueprint $table) {
            $table->dropForeign(['radiologist_id']);
            $table->dropColumn('radiologist_id');
        });
    }
};
