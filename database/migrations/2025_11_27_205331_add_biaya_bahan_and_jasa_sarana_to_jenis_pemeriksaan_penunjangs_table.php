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
        Schema::table('jenis_pemeriksaan_penunjangs', function (Blueprint $table) {
            $table->decimal('biaya_bahan', 15, 2)->default(0)->after('fee_pelaksana');
            $table->decimal('jasa_sarana', 15, 2)->default(0)->after('biaya_bahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_pemeriksaan_penunjangs', function (Blueprint $table) {
            $table->dropColumn(['biaya_bahan', 'jasa_sarana']);
        });
    }
};
