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
        Schema::create('seller_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('amount')->nullable();
            $table->integer('product_upload_limit')->nullable();
            $table->text('logo')->nullable();
            $table->string('time_name')->nullable();
            $table->integer('time_number')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_packages');
    }
};
