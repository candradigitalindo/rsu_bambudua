<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiology_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('radiology_request_id')->constrained('radiology_requests')->cascadeOnDelete();
            $table->longText('findings');
            $table->longText('impression');
            $table->json('payload')->nullable();
            $table->json('files')->nullable();
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_results');
    }
};
