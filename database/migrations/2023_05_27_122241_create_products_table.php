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
            $table->bigInteger('brand_id')->unsigned()->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->text('photos')->nullable();
            $table->text('thumbnail_img')->nullable();
            $table->text('tags')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price')->nullable();
            $table->integer('variant_product')->default(0);
            $table->text('colors')->nullable();
            $table->text('variations')->nullable();
            $table->integer('todays_deal')->default(0);
            $table->integer('published')->default(0);
            $table->integer('approved')->default(0);
            $table->integer('stock')->default(0);
            $table->integer('featured')->default(0);
            $table->integer('seller_featured')->default(0);
            $table->integer('unit')->nullable();
            $table->decimal('weight')->nullable();
            $table->integer('min_qty')->nullable();
            $table->integer('low_stock_quantity')->nullable();
            $table->decimal('discount')->nullable();
            $table->string('discount_type')->nullable();
            $table->string('discount_start_date')->nullable();
            $table->string('discount_end_date')->nullable();
            $table->decimal('tax')->nullable();
            $table->string('tax_type')->nullable();
            $table->string('shipping_type')->nullable();
            $table->decimal('shipping_cost')->nullable();
            $table->integer('is_quantity_multiplied')->default(0);
            $table->integer('est_shipping_days')->nullable();
            $table->integer('num_of_sale')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_img')->nullable();
            $table->text('pdf')->nullable();
            $table->string('slug')->nullable();
            $table->integer('refundable')->default(0);
            $table->decimal('rating')->nullable();
            $table->text('barcode')->nullable();
            $table->integer('digital')->default(0);
            $table->string('file_name')->nullable();
            $table->text('file_path')->nullable();
            $table->text('external_link')->nullable();
            $table->text('external_link_btn')->nullable();
            $table->integer('wholesale_product')->default(0);
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
