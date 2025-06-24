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
        Schema::create('campaign_segments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('campaign_id')->unsigned()->nullable();
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('segment_id')->unsigned()->nullable();
            $table->foreign('segment_id')->references('id')->on('segments')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_segments');
    }
};
