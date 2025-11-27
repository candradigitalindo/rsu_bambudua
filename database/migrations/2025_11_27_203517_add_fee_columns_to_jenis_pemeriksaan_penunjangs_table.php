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
            $table->decimal('fee_dokter_penunjang', 15, 2)->default(0)->after('harga')->comment('Fee untuk dokter yang merequest pemeriksaan penunjang');
            $table->decimal('fee_perawat_penunjang', 15, 2)->default(0)->after('fee_dokter_penunjang')->comment('Fee untuk perawat yang membantu pemeriksaan penunjang');
            $table->decimal('fee_pelaksana', 15, 2)->default(0)->after('fee_perawat_penunjang')->comment('Fee untuk petugas lab/radiologi yang melaksanakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_pemeriksaan_penunjangs', function (Blueprint $table) {
            $table->dropColumn(['fee_dokter_penunjang', 'fee_perawat_penunjang', 'fee_pelaksana']);
        });
    }
};
