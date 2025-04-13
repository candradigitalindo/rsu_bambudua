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
        Schema::table('historibahans', function (Blueprint $table) {
            $table->enum('status', ['masuk', 'keluar'])->default('masuk')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historibahans', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
