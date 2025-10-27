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
        Schema::table('radiology_requests', function (Blueprint $table) {
            // Ubah status default langsung ke processing (tidak perlu requested)
            $table->string('status')->default('processing')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('radiology_requests', function (Blueprint $table) {
            $table->string('status')->default('requested')->change();
        });
    }
};
