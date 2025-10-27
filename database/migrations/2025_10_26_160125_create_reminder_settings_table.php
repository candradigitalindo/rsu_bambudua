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
        Schema::create('reminder_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Nama reminder (e.g., "Reminder Beli Obat", "Reminder Check Up")
            $table->enum('type', ['obat', 'checkup']); // Tipe reminder
            $table->integer('days_before')->default(2); // Berapa hari sebelum reminder dikirim
            $table->text('message_template')->nullable(); // Template pesan reminder
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_settings');
    }
};
