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
        Schema::create('product_apoteks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('satuan');
            $table->bigInteger('harga')->default(0);
            $table->integer('type')->default(0)->comment('0 = Obat Resep, 1 = Non Resep, 2 = Umum');
            $table->integer('status')->default(1)->comment('0 = Tidak Aktif, 1 = Aktif');
            // stok
            $table->integer('stok')->default(0)->comment('Stok barang');
            // apakah mempunyai expired
            $table->integer('expired')->default(0)->comment('0 = Tidak ada expired, 1 = Ada expired');
            // jumlah Warning stok
            $table->integer('warning_stok')->default(0)->comment('Jumlah stok yang memberikan peringatan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_apoteks');
    }
};
