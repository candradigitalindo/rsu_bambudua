<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lab_request_items', function (Blueprint $table) {
            $table->json('result_payload')->nullable()->after('result_notes');
            $table->index('test_id');
        });
    }

    public function down(): void
    {
        Schema::table('lab_request_items', function (Blueprint $table) {
            $table->dropIndex(['test_id']);
            $table->dropColumn('result_payload');
        });
    }
};
