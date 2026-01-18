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
        Schema::table('encounters', function (Blueprint $table) {
            // Simpan detail items yang dibayar untuk tindakan (tindakan, lab, radiologi)
            $table->json('paid_tindakan_items')->nullable()->after('payment_fee_tindakan');
            // Simpan detail items yang dibayar untuk resep
            $table->json('paid_resep_items')->nullable()->after('payment_fee_resep');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encounters', function (Blueprint $table) {
            $table->dropColumn(['paid_tindakan_items', 'paid_resep_items']);
        });
    }
};
