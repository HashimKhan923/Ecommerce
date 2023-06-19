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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('added_by')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade'); 
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->string('year')->nullable();
            $table->string('model')->nullable();
            $table->bigInteger('brand_id')->unsigned()->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->text('photos')->nullable();
            $table->text('thumbnail_img')->nullable();
            $table->text('tags')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price')->nullable();
            $table->string('colors')->nullable();
            $table->string('sizes')->nullable();
            $table->integer('published')->default(0);
            $table->integer('approved')->default(0);
            $table->integer('featured')->default(0);
            $table->integer('seller_featured')->default(0);
            $table->integer('min_qty')->nullable();
            $table->integer('num_of_sale')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_img')->nullable();
            $table->string('slug')->nullable();
            $table->integer('rating')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
