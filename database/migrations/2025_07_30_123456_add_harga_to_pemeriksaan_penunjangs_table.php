<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan_penunjangs', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->default(0)->after('hasil_pemeriksaan');
            $table->integer('qty')->default(1)->after('harga');
            $table->decimal('total_harga', 15, 2)->default(0)->after('qty');
        });
    }

    public function down(): void
    {
        Schema::table('pemeriksaan_penunjangs', function (Blueprint $table) {
            $table->dropColumn(['harga', 'qty', 'total_harga']);
        });
    }
};
