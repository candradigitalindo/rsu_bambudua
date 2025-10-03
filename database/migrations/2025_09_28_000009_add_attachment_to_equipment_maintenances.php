<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('equipment_maintenances', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment_maintenances', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('operational_expense_id');
            }
            if (!Schema::hasColumn('equipment_maintenances', 'attachment_name')) {
                $table->string('attachment_name')->nullable()->after('attachment_path');
            }
            if (!Schema::hasColumn('equipment_maintenances', 'attachment_mime')) {
                $table->string('attachment_mime')->nullable()->after('attachment_name');
            }
            if (!Schema::hasColumn('equipment_maintenances', 'attachment_size')) {
                $table->integer('attachment_size')->nullable()->after('attachment_mime');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipment_maintenances', function (Blueprint $table) {
            if (Schema::hasColumn('equipment_maintenances', 'attachment_size')) {
                $table->dropColumn('attachment_size');
            }
            if (Schema::hasColumn('equipment_maintenances', 'attachment_mime')) {
                $table->dropColumn('attachment_mime');
            }
            if (Schema::hasColumn('equipment_maintenances', 'attachment_name')) {
                $table->dropColumn('attachment_name');
            }
            if (Schema::hasColumn('equipment_maintenances', 'attachment_path')) {
                $table->dropColumn('attachment_path');
            }
        });
    }
};
