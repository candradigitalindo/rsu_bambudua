<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('equipment_maintenances', function (Blueprint $table) {
            $table->uuid('operational_expense_id')->nullable()->after('cost');
            $table->index('operational_expense_id');
        });
    }

    public function down(): void
    {
        Schema::table('equipment_maintenances', function (Blueprint $table) {
            $table->dropIndex(['operational_expense_id']);
            $table->dropColumn('operational_expense_id');
        });
    }
};
