<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('discounts', 'diskon_tindakan_nominal')) {
                $table->integer('diskon_tindakan_nominal')->default(0);
            }
            if (!Schema::hasColumn('discounts', 'diskon_resep_nominal')) {
                $table->integer('diskon_resep_nominal')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            if (Schema::hasColumn('discounts', 'diskon_tindakan_nominal')) {
                $table->dropColumn('diskon_tindakan_nominal');
            }
            if (Schema::hasColumn('discounts', 'diskon_resep_nominal')) {
                $table->dropColumn('diskon_resep_nominal');
            }
        });
    }
};
