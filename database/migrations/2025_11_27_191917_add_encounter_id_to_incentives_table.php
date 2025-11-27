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
        Schema::table('incentives', function (Blueprint $table) {
            $table->uuid('encounter_id')->nullable()->after('user_id');
            $table->foreign('encounter_id')->references('id')->on('encounters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incentives', function (Blueprint $table) {
            $table->dropForeign(['encounter_id']);
            $table->dropColumn('encounter_id');
        });
    }
};
