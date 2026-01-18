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
            // Fee untuk tindakan
            $table->decimal('payment_fee_tindakan', 15, 2)->default(0)->after('metode_pembayaran_tindakan');

            // Fee untuk resep
            $table->decimal('payment_fee_resep', 15, 2)->default(0)->after('metode_pembayaran_resep');

            // Grand total untuk tindakan (total_bayar_tindakan + payment_fee_tindakan)
            $table->decimal('grand_total_tindakan', 15, 2)->default(0)->after('payment_fee_tindakan');

            // Grand total untuk resep (total_bayar_resep + payment_fee_resep)
            $table->decimal('grand_total_resep', 15, 2)->default(0)->after('payment_fee_resep');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encounters', function (Blueprint $table) {
            $table->dropColumn([
                'payment_fee_tindakan',
                'payment_fee_resep',
                'grand_total_tindakan',
                'grand_total_resep'
            ]);
        });
    }
};
