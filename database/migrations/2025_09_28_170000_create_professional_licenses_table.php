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
        Schema::create('professional_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // profesi: dokter, perawat, apoteker, asisten_apoteker, radiografer, analis_lab
            $table->string('profession');
            $table->string('sip_number')->nullable();
            $table->date('sip_expiry_date');
            // Untuk mencegah pengiriman berulang setiap hari
            $table->timestamp('six_month_reminder_sent_at')->nullable();
            $table->timestamps();

            $table->index(['sip_expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_licenses');
    }
};