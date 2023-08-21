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
            $table->bigInteger('deal_id')->unsigned()->nullable();
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade')->onUpdate('cascade');
            $table->string('weight')->nullable();
            $table->decimal('unit')->nullable();
            $table->string('sku')->nullable();
            $table->bigInteger('brand_id')->unsigned()->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('model_id')->unsigned()->nullable();
            $table->foreign('model_id')->references('id')->on('models')->onDelete('cascade')->onUpdate('cascade');
            $table->text('photos')->nullable();
            $table->text('thumbnail_img')->nullable();
            $table->text('tags')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price')->nullable();
            $table->bigInteger('varient_id')->unsigned()->nullable();
            $table->foreign('varient_id')->references('id')->on('varients')->onDelete('cascade')->onUpdate('cascade');
            $table->string('sizes')->nullable();
            $table->string('pdf')->nullable();
            $table->boolean('published')->default(0);
            $table->boolean('approved')->default(0);
            $table->boolean('featured')->default(0);
            $table->boolean('todays_deal')->default(0);
            $table->boolean('cash_on_delivery')->default(0);
            $table->boolean('seller_featured')->default(0);
            $table->integer('min_qty')->nullable();
            $table->integer('num_of_sale')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_img')->nullable();
            $table->string('slug')->nullable();
            $table->integer('rating')->nullable();
            $table->boolean('refundable')->default(1);
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
