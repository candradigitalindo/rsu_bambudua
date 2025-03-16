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
        Schema::create('encounters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_encounter');
            $table->string('rekam_medis');
            $table->string('name_pasien');
            $table->string('pasien_satusehat_id')->nullable();
            $table->integer('status')->default(1)->comment('1=Progress,2=Finish');
            $table->integer('type')->comment('1=Rawat Jalan, 2=Rawat Inap, 3=IGD');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
