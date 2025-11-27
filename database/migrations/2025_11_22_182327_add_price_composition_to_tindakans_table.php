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
        Schema::table('tindakans', function (Blueprint $table) {
            $table->bigInteger('honor_dokter')->default(0)->after('harga');
            $table->bigInteger('bonus_perawat')->default(0)->after('honor_dokter');
            $table->bigInteger('biaya_bahan')->default(0)->after('bonus_perawat');
            $table->bigInteger('jasa_sarana')->default(0)->after('biaya_bahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tindakans', function (Blueprint $table) {
            $table->dropColumn(['honor_dokter',
            'bonus_perawat',
            'biaya_bahan', 'jasa_sarana']);
        });
    }
};
