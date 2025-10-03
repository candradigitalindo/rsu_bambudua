<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('operational_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('operational_expenses', 'expense_category_id')) {
                $table->uuid('expense_category_id')->nullable()->after('category');
                $table->index('expense_category_id');
            }
            if (!Schema::hasColumn('operational_expenses', 'cost_center_id')) {
                $table->uuid('cost_center_id')->nullable()->after('cost_center');
                $table->index('cost_center_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('operational_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('operational_expenses', 'cost_center_id')) {
                $table->dropIndex(['cost_center_id']);
                $table->dropColumn('cost_center_id');
            }
            if (Schema::hasColumn('operational_expenses', 'expense_category_id')) {
                $table->dropIndex(['expense_category_id']);
                $table->dropColumn('expense_category_id');
            }
        });
    }
};
