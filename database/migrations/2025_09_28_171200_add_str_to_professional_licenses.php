<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professional_licenses', function (Blueprint $table) {
            $table->string('str_number')->nullable()->after('sip_number');
            $table->date('str_expiry_date')->nullable()->after('sip_expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('professional_licenses', function (Blueprint $table) {
            $table->dropColumn(['str_number', 'str_expiry_date']);
        });
    }
};