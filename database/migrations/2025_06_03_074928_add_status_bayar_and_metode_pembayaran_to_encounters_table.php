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
            $table->tinyInteger('status_bayar_resep')->default(0)->after('status'); // 0 = Belum Lunas, 1 = Lunas
            $table->string('metode_pembayaran_resep')->nullable()->after('status_bayar_resep');
            $table->tinyInteger('status_bayar_tindakan')->default(0)->after('status_bayar_resep'); // 0 = Belum Bayar, 1 = Sudah Bayar
            $table->string('metode_pembayaran_tindakan')->nullable()->after('status_bayar_tindakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encounters', function (Blueprint $table) {
            $table->dropColumn(['status_bayar_resep', 'metode_pembayaran_resep', 'status_bayar_tindakan', 'metode_pembayaran_tindakan']);
        });
    }
};
