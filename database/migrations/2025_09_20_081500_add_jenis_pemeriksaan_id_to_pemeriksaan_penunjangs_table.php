<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_penunjangs', function (Blueprint $table) {
            $table->foreignUuid('jenis_pemeriksaan_id')->nullable()->after('encounter_id')->constrained('jenis_pemeriksaan_penunjangs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_penunjangs', function (Blueprint $table) {
            $table->dropForeign(['jenis_pemeriksaan_id']);
            $table->dropColumn('jenis_pemeriksaan_id');
        });
    }
};
