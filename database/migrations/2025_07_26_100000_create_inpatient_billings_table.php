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
        Schema::create('inpatient_billings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inpatient_admission_id')->constrained('inpatient_admissions')->onDelete('cascade');
            $table->string('billing_type'); // e.g., 'Obat', 'Tindakan', 'Kamar'
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inpatient_billings');
    }
};
