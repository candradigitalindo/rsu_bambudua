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
        Schema::create('histori_apoteks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_apotek_id')->constrained('product_apoteks')->onDelete('cascade');
            $table->integer('jumlah')->default(0)->comment('Jumlah barang');
            $table->integer('type')->default(0)->comment('0 = Masuk, 1 = Keluar');
            // Expired-at
            $table->date('expired_at')->nullable()->comment('Tanggal kadaluarsa');
            // Keterangan
            $table->string('keterangan')->nullable()->comment('Keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histori_apoteks');
    }
};
