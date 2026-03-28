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
        $tables = ['tindakan_encounters', 'lab_requests', 'radiology_requests', 'resep_details'];

        foreach ($tables as $tableName) {
            if (!Schema::hasColumn($tableName, 'paket_pasien_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignUuid('paket_pasien_id')->nullable()
                        ->constrained('paket_pasiens')->nullOnDelete();
                });
            } else {
                // Column exists but may lack FK constraint — add it
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->foreign('paket_pasien_id')
                            ->references('id')->on('paket_pasiens')->nullOnDelete();
                    });
                } catch (\Exception $e) {
                    // FK already exists
                }
            }
        }
    }

    public function down(): void
    {
        foreach (['tindakan_encounters', 'lab_requests', 'radiology_requests', 'resep_details'] as $table) {
            Schema::table($table, function (Blueprint $tbl) use ($table) {
                $tbl->dropForeign([$table === 'resep_details' ? 'paket_pasien_id' : 'paket_pasien_id']);
                $tbl->dropColumn('paket_pasien_id');
            });
        }
    }
};
