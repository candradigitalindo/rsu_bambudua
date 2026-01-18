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
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->decimal('fee_percentage', 5, 2)->default(0)->after('code')->comment('Fee dalam persen (%)');
            $table->decimal('fee_fixed', 15, 2)->default(0)->after('fee_percentage')->comment('Fee tetap dalam rupiah');
            $table->string('fee_type', 20)->default('percentage')->after('fee_fixed')->comment('Tipe fee: percentage, fixed, or both');
            $table->text('description')->nullable()->after('fee_type')->comment('Deskripsi metode pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn(['fee_percentage', 'fee_fixed', 'fee_type', 'description']);
        });
    }
};
