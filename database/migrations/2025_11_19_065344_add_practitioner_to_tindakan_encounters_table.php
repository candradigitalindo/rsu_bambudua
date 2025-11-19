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
        Schema::table('tindakan_encounters', function (Blueprint $table) {
            $table->string('id_petugas')->nullable()->after('total_harga');
            $table->string('petugas_name')->nullable()->after('total_harga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tindakan_encounters', function (Blueprint $table) {
            $table->dropColumn('id_petugas');
            $table->dropColumn('petugas_name');
        });
    }
};
