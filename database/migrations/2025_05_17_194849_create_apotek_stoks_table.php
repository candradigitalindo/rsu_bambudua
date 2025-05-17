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
        Schema::create('apotek_stoks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_apotek_id')->constrained('product_apoteks')->onDelete('cascade');
            // expired_at
            $table->date('expired_at')->nullable()->comment('Tanggal kadaluarsa');
            // status
            $table->integer('status')->default(1)->comment('0 = Available, 1 = Not Available');
            // status expired
            $table->integer('status_expired')->default(0)->comment('0 = Non Expired, 1 = Expired');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apotek_stoks');
    }
};
