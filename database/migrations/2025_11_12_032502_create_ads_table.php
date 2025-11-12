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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('year')->nullable();
            $table->bigInteger('brand_id')->unsigned()->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('model_id')->unsigned()->nullable();
            $table->foreign('model_id')->references('id')->on('models')->onDelete('cascade')->onUpdate('cascade');
            $table->string('color')->nullable();
            $table->string('kms_driven')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('transmission')->nullable();
            $table->string('mileage')->nullable();
            $table->string('engine_capacity')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('ownership_type')->nullable();
            $table->string('insurance_validity')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('negotiable')->nullable();
            $table->string('condition')->nullable();
            $table->string('location')->nullable();
            $table->string('contact_number');
            $table->string('email')->nullable();
            $table->text('tags')->nullable();
            $table->bigInteger('seller_id')->unsigned()->nullable();
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('published')->default(0);
            $table->boolean('featured')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
