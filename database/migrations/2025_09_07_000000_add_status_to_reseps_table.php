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
        Schema::table('reseps', function (Blueprint $table) {
            $table->string('status')->default('Diajukan')->after('catatan')->comment('Status resep: Diajukan, Disiapkan, Selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
