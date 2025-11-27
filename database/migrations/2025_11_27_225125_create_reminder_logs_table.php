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
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('rekam_medis');
            $table->string('encounter_id')->nullable();
            $table->string('reminder_type'); // 'obat' atau 'checkup'
            $table->date('reminder_date'); // Tanggal reminder
            $table->boolean('wa_clicked')->default(false); // Apakah WA sudah diklik
            $table->timestamp('clicked_at')->nullable(); // Kapan diklik
            $table->unsignedBigInteger('clicked_by')->nullable(); // User yang klik
            $table->timestamps();

            $table->foreign('clicked_by')->references('id')->on('users')->onDelete('set null');

            // Index untuk query cepat
            $table->index(['rekam_medis', 'reminder_date', 'reminder_type']);
            $table->index('wa_clicked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
