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
        Schema::create('job_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->integer('total_jobs')->default(0);
            $table->integer('pending_jobs')->default(0);
            $table->integer('failed_jobs')->default(0);
            $table->text('failed_job_ids')->nullable();
            $table->text('options')->nullable();
            $table->timestamps();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('finished_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_batches');
    }
};
