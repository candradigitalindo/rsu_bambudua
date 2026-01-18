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
        Schema::table('pasiens', function (Blueprint $table) {
            $table->boolean('is_kerabat_dokter')->default(false)->after('status')->comment('Flag pasien kerabat dokter');
            $table->boolean('is_kerabat_karyawan')->default(false)->after('is_kerabat_dokter')->comment('Flag pasien kerabat karyawan');
            $table->boolean('is_kerabat_owner')->default(false)->after('is_kerabat_karyawan')->comment('Flag pasien kerabat owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropColumn(['is_kerabat_dokter', 'is_kerabat_karyawan', 'is_kerabat_owner']);
        });
    }
};
