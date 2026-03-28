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
        Schema::table('paket_pemeriksaans', function (Blueprint $table) {
            $table->json('lab_ids')->nullable()->after('tindakan_ids');
            $table->json('radiologi_ids')->nullable()->after('lab_ids');
            $table->json('obat_ids')->nullable()->after('radiologi_ids');
        });
    }

    public function down(): void
    {
        Schema::table('paket_pemeriksaans', function (Blueprint $table) {
            $table->dropColumn(['lab_ids', 'radiologi_ids', 'obat_ids']);
        });
    }
};
