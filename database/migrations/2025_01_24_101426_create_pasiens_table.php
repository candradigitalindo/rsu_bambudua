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
        Schema::create('pasiens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('rekam_medis');
            $table->string('name');
            $table->integer('is_identitas')->default(1)->comment('1=Ya, 2=Tidak');
            $table->string('jenis_identitas')->nullable();
            $table->string('no_identitas')->nullable();
            $table->date('tgl_lahir');
            $table->string('golongan_darah')->nullable();
            $table->integer('jenis_kelamin')->nullable()->comment('1=Male,2=Female');
            $table->string('email')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('status_menikah')->nullable();
            $table->string('etnis')->nullable();
            $table->string('agama')->nullable();
            $table->integer('kewarganegaraan')->default(1)->comment('1=WNI, 2=WNA');
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('mr_lama')->nullable();
            $table->string('satusehat_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};
