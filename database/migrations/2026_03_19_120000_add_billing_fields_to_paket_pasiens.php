<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_pasiens', function (Blueprint $table) {
            $table->boolean('status_bayar')->default(0)->after('harga_bayar');
            $table->string('metode_pembayaran')->nullable()->after('status_bayar');
            $table->bigInteger('payment_fee')->default(0)->after('metode_pembayaran');
            $table->bigInteger('grand_total')->default(0)->after('payment_fee');
            $table->timestamp('paid_at')->nullable()->after('grand_total');
        });

        // Update existing records: set status_bayar = 1 for already active pakets
        \Illuminate\Support\Facades\DB::table('paket_pasiens')
            ->whereIn('status', ['aktif', 'selesai'])
            ->update(['status_bayar' => 1]);
    }

    public function down(): void
    {
        Schema::table('paket_pasiens', function (Blueprint $table) {
            $table->dropColumn(['status_bayar', 'metode_pembayaran', 'payment_fee', 'grand_total', 'paid_at']);
        });
    }
};
