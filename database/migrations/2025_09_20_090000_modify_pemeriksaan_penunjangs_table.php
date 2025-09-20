<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_penunjangs', function (Blueprint $table) {
            $table->dropColumn('dokumen_pemeriksaan');
            $table->longText('recomendation')->nullable()->after('hasil_pemeriksaan');
        });
    }
};
