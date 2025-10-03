<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('activity_logs', 'subject')) {
                    $table->string('subject')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('activity_logs', 'module')) {
                    $table->string('module')->nullable()->after('subject');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (Schema::hasColumn('activity_logs', 'subject')) {
                    $table->dropColumn('subject');
                }
                if (Schema::hasColumn('activity_logs', 'module')) {
                    $table->dropColumn('module');
                }
            });
        }
    }
};
