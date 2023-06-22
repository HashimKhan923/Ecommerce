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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->nullable();
            $table->integer('number_of_products')->nullable();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('seller_id')->unsigned()->nullable();
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('amount')->nullable();
            $table->enum('delivery_status', ['Pending','Confirmed','Picked Up','On The Way','Delivered'])->default('Pending');
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['Paid','Un Paid'])->default('Un Paid');
            $table->string('refund')->nullable();
            $table->text('information')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
