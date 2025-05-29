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
            $table->integer('diskon_tindakan')->default(0)->after('tujuan_kunjungan');
            $table->integer('diskon_persen_tindakan')->default(0)->after('diskon_tindakan');
            $table->bigInteger('total_tindakan')->default(0)->after('diskon_persen_tindakan');
            $table->bigInteger('total_bayar_tindakan')->default(0)->after('total_tindakan');
            $table->integer('diskon_resep')->default(0)->after('total_bayar_tindakan');
            $table->integer('diskon_persen_resep')->default(0)->after('diskon_resep');
            $table->bigInteger('total_resep')->default(0)->after('diskon_persen_resep');
            $table->bigInteger('total_bayar_resep')->default(0)->after('total_resep');
            $table->longText('catatan')->nullable()->after('total_bayar_resep');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encounters', function (Blueprint $table) {
            $table->dropColumn('diskon_tindakan');
            $table->dropColumn('diskon_persen_tindakan');
            $table->dropColumn('total_tindakan');
            $table->dropColumn('total_bayar_tindakan');
            $table->dropColumn('diskon_resep');
            $table->dropColumn('diskon_persen_resep');
            $table->dropColumn('total_resep');
            $table->dropColumn('total_bayar_resep');
            $table->dropColumn('catatan');
        });
    }
};
