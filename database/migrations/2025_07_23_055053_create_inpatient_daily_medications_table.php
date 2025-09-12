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
        Schema::create('inpatient_daily_medications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inpatient_admission_id')->constrained('inpatient_admissions')->onDelete('cascade');
            $table->string('medication_code')->nullable();
            $table->string('medication_name');
            $table->integer('harga')->default(0);
            $table->integer('jumlah')->default(1);
            $table->integer('total')->default(0);
            $table->string('dosage_instructions');
            $table->string('satuan')->nullable();
            $table->string('route')->nullable();
            $table->string('frequency')->nullable();
            $table->date('expiration_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['Diajukan', 'Diberikan', 'Batal', 'Disiapkan'])->default('Diajukan');
            $table->foreignId('authorized_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('authorized_name')->nullable();
            $table->foreignId('administered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('administered_name')->nullable();
            $table->timestamp('administered_at')->nullable();
            $table->enum('is_billing', ['Ya', 'Tidak'])->default('Tidak');
            $table->date('medicine_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inpatient_daily_medications');
    }
};
