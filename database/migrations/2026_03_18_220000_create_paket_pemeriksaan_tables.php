<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master paket pemeriksaan
        Schema::create('paket_pemeriksaans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('tindakan_ids')->nullable(); // array of tindakan UUIDs included
            $table->integer('jumlah_sesi')->default(1);
            $table->bigInteger('harga')->default(0);
            $table->boolean('is_gratis')->default(false);
            $table->integer('masa_berlaku_hari')->default(30); // expiry in days
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Paket assigned to patient
        Schema::create('paket_pasiens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paket_pemeriksaan_id')->constrained('paket_pemeriksaans')->cascadeOnDelete();
            $table->foreignUuid('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->integer('total_sesi');
            $table->integer('sesi_terpakai')->default(0);
            $table->bigInteger('harga_bayar')->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_expired');
            $table->enum('status', ['aktif', 'selesai', 'expired', 'batal'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });

        // Usage log per session
        Schema::create('paket_pasien_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('paket_pasien_id')->constrained('paket_pasiens')->cascadeOnDelete();
            $table->foreignUuid('encounter_id')->nullable()->constrained('encounters')->nullOnDelete();
            $table->integer('sesi_ke'); // which session number
            $table->unsignedBigInteger('used_by')->nullable();
            $table->foreign('used_by')->references('id')->on('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_pasien_usages');
        Schema::dropIfExists('paket_pasiens');
        Schema::dropIfExists('paket_pemeriksaans');
    }
};
