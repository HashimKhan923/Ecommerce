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
        Schema::create('seller_infromations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade'); 
            $table->string('social_security_number')->nullable();
            $table->string('business_ein_number')->nullable();
            $table->string('credit_card_number')->nullable();
            $table->string('paypal_address')->nullable();
            $table->string('document')->nullable();
            $table->string('social_security_card_front')->nullable();
            $table->string('social_security_card_back')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_infromations');
    }
};
