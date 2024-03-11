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
        Schema::table('coupons', function (Blueprint $table) {
            $table->text('customer_id')->nullable();
            $table->text('category_id')->nullable();
            $table->text('brand_id')->nullable();
            $table->text('model_id')->nullable();
            $table->decimal('minimum_purchase_amount')->nullable();
            $table->integer('minimum_quantity_items')->nullable();
            $table->boolean('is_amount_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            //
        });
    }
};
