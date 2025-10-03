<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('operational_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('operational_expenses', 'category')) {
                $table->string('category')->nullable()->after('description');
            }
            if (!Schema::hasColumn('operational_expenses', 'cost_center')) {
                $table->string('cost_center')->nullable()->after('category');
            }
            $table->index('category');
            $table->index('cost_center');
        });
    }

    public function down(): void
    {
        Schema::table('operational_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('operational_expenses', 'cost_center')) {
                $table->dropIndex(['cost_center']);
                $table->dropColumn('cost_center');
            }
            if (Schema::hasColumn('operational_expenses', 'category')) {
                $table->dropIndex(['category']);
                $table->dropColumn('category');
            }
        });
    }
};
