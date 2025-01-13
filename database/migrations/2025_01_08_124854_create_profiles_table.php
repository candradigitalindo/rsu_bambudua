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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('nik')->nullable();
            $table->timestamp('tgl_lahir')->nullable();
            $table->integer('gender')->nullable()->comment('1=Male, 2=Female');
            $table->string('email')->nullable();
            $table->string('no_hp')->nullable();
            $table->integer('status_menikah')->nullable()->comment('1=Belum Menikah, 2=Menikah');
            $table->string('gol_darah')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kode_provinsi')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_kota')->nullable();
            $table->string('kota')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
