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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id'); // Define the foreign key column

            // Define the foreign key constraint
            $table->foreign('shop_id')
                ->references('id')
                ->on('shops')
                ->onDelete('cascade'); // Define the desired on delete behavior (e.g., cascade)

            // If you want to index the foreign key column for performance
            // $table->index('shop_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['shop_id']); // Drop the foreign key constraint
            $table->dropColumn('shop_id'); // Remove the foreign key column
        });
    }
};
