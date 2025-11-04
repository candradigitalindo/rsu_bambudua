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
        Schema::table('template_field_items', function (Blueprint $table) {
            $table->string('examination_name')->nullable()->after('item_type'); // Nama pemeriksaan yang ditentukan admin
            $table->string('unit')->nullable()->after('examination_name'); // Satuan (mm, %, mg/dL, dll)
            $table->string('normal_range')->nullable()->after('unit'); // Range normal yang ditentukan admin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_field_items', function (Blueprint $table) {
            $table->dropColumn(['examination_name', 'unit', 'normal_range']);
        });
    }
};
