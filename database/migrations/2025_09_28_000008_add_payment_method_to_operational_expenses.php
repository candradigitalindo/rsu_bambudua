<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('operational_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('operational_expenses', 'payment_method_code')) {
                $table->string('payment_method_code')->nullable()->after('cost_center_id');
            }
            if (!Schema::hasColumn('operational_expenses', 'payment_method_name')) {
                $table->string('payment_method_name')->nullable()->after('payment_method_code');
            }
            if (!Schema::hasColumn('operational_expenses', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method_name');
            }
            $table->index('payment_method_code');
        });
    }

    public function down(): void
    {
        Schema::table('operational_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('operational_expenses', 'payment_method_code')) {
                $table->dropIndex(['payment_method_code']);
                $table->dropColumn('payment_method_code');
            }
            if (Schema::hasColumn('operational_expenses', 'payment_method_name')) {
                $table->dropColumn('payment_method_name');
            }
            if (Schema::hasColumn('operational_expenses', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }
        });
    }
};
