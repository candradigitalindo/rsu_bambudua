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
        Schema::table('pasiens', function (Blueprint $table) {
            $table->longText('alamat')->nullable()->after('mr_lama');
            $table->string('province_code')->nullable()->after('mr_lama');
            $table->string('province')->nullable()->after('mr_lama');
            $table->string('city_code')->nullable()->after('mr_lama');
            $table->string('city')->nullable()->after('mr_lama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropColumn('alamat');
            $table->dropColumn('province_code');
            $table->dropColumn('city_code');
            $table->dropColumn('city');
        });
    }
};
