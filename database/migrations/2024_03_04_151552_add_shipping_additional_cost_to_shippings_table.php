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
        Schema::table('shippings', function (Blueprint $table) {
            $table->decimal('shipping_additional_cost')->nullable()->after('is_qty_multiply');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shippings', function (Blueprint $table) {
            //
        });
    }
};
