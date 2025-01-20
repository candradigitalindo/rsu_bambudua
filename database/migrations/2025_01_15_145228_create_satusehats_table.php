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
        Schema::create('satusehats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('organization_name')->nullable();
            $table->string('organization_id')->nullable();
            $table->longText('client_id')->nullable();
            $table->longText('client_secret')->nullable();
            $table->string('developer_email')->nullable();
            $table->string('access_token')->nullable();
            $table->timestamp('expired_in')->nullable();
            $table->integer('status')->default(1)->comment('1=Sandbox, 2=Production');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satusehats');
    }
};
