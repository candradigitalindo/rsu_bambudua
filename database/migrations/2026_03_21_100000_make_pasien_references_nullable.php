<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make rekam_medis nullable on encounters
        Schema::table('encounters', function (Blueprint $table) {
            $table->string('rekam_medis')->nullable()->change();
        });

        // Make rekam_medis nullable on medical_record_files
        Schema::table('medical_record_files', function (Blueprint $table) {
            $table->string('rekam_medis')->nullable()->change();
        });

        // Make rekam_medis nullable on reminder_logs
        Schema::table('reminder_logs', function (Blueprint $table) {
            $table->string('rekam_medis')->nullable()->change();
        });

        // Change pasien_id FK from CASCADE to SET NULL on radiology_requests
        Schema::table('radiology_requests', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
            $table->uuid('pasien_id')->nullable()->change();
            $table->foreign('pasien_id')->references('id')->on('pasiens')->nullOnDelete();
        });

        // Change pasien_id FK from CASCADE to SET NULL on inpatient_admissions
        Schema::table('inpatient_admissions', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
            $table->uuid('pasien_id')->nullable()->change();
            $table->foreign('pasien_id')->references('id')->on('pasiens')->nullOnDelete();
        });

        // Change pasien_id FK from CASCADE to SET NULL on riwayat_penyakits
        Schema::table('riwayat_penyakits', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
            $table->uuid('pasien_id')->nullable()->change();
            $table->foreign('pasien_id')->references('id')->on('pasiens')->nullOnDelete();
        });

        // Change pasien_id FK from CASCADE to SET NULL on paket_pasiens
        Schema::table('paket_pasiens', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
            $table->uuid('pasien_id')->nullable()->change();
            $table->foreign('pasien_id')->references('id')->on('pasiens')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('radiology_requests', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
            $table->uuid('pasien_id')->nullable(false)->change();
            $table->foreign('pasien_id')->references('id')->on('pasiens')->cascadeOnDelete();
        });

        Schema::table('inpatient_admissions', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
            $table->uuid('pasien_id')->nullable(false)->change();
            $table->foreign('pasien_id')->references('id')->on('pasiens')->cascadeOnDelete();
        });

        Schema::table('riwayat_penyakits', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
            $table->uuid('pasien_id')->nullable(false)->change();
            $table->foreign('pasien_id')->references('id')->on('pasiens')->cascadeOnDelete();
        });

        Schema::table('paket_pasiens', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
            $table->uuid('pasien_id')->nullable(false)->change();
            $table->foreign('pasien_id')->references('id')->on('pasiens')->cascadeOnDelete();
        });
    }
};
