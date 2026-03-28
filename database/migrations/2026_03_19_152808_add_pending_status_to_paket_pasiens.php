<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE paket_pasiens MODIFY COLUMN status ENUM('pending','aktif','selesai','expired','batal') NOT NULL DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE paket_pasiens MODIFY COLUMN status ENUM('aktif','selesai','expired','batal') NOT NULL DEFAULT 'aktif'");
    }
};
