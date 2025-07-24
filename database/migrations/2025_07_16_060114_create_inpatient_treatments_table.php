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
        Schema::create('inpatient_treatments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained('inpatient_admissions')->onDelete('cascade');
            $table->string('request_type')->default('treatment');
            $table->foreignUuid('tindakan_id')->nullable()->constrained('tindakans')->onDelete('set null');
            $table->string('tindakan_name');
            $table->integer('harga')->default(0);
            $table->integer('total')->default(0);
            $table->timestamp('treatment_date')->nullable();
            $table->integer('quantity')->default(1);
            $table->text('result')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('document')->nullable();
            $table->enum('is_billing', ['Ya', 'Tidak'])->default('Tidak');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inpatient_treatments');
    }
};
