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
        Schema::create('seller_policies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('shop_id')->unsigned()->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade')->onUpdate('cascade');
            $table->longtext('store_policy')->nullable();
            $table->longtext('return_policy')->nullable();
            $table->longtext('about')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_policies');
    }
};
