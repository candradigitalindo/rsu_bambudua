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
        Schema::table('resep_details', function (Blueprint $table) {
            $table->string('status')->default('Diajukan')->after('total_harga')->comment('Status item resep: Diajukan, Disiapkan, Diberikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resep_details', function (Blueprint $table) {
            if (Schema::hasColumn('resep_details', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
