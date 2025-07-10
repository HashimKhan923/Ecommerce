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
        Schema::create('campaign_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->integer('total_sent')->default(0);
            $table->integer('total_opened')->default(0);
            $table->integer('total_clicked')->default(0);
            $table->integer('total_unsubscribed')->default(0);
            $table->float('open_rate')->default(0);
            $table->float('click_rate')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_summaries');
    }
};
