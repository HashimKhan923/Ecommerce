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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users');
            $table->string('name');
            $table->string('subject')->nullable();
            $table->string('preview_text')->nullable();
            $table->text('content')->nullable();
            $table->timestamp('send_time')->nullable();
            $table->enum('status', ['draft','scheduled','sending','sent'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
