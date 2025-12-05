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
        Schema::table('campaign_recipients', function (Blueprint $table) {
            $table->bigInteger('subscriber_id')->unsigned();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_recipients', function (Blueprint $table) {
            //
        });
    }
};
