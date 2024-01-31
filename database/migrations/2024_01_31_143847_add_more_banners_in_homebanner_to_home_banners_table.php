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
        Schema::table('home_banners', function (Blueprint $table) {
            $table->string('all_category_banner')->nullable();
            $table->string('all_brand_banner')->nullable();
            $table->string('all_store_banner')->nullable();
            $table->string('cart_banner')->nullable();
            $table->string('wishlist_banner')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_banners', function (Blueprint $table) {
            //
        });
    }
};
