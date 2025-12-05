<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('segments', function (Blueprint $table) {
            $table->string('segment_type')->default('user')->after('seller_id'); 
            // values: 'user' or 'subscriber'
        });
    }

    public function down()
    {
        Schema::table('segments', function (Blueprint $table) {
            $table->dropColumn('segment_type');
        });
    }

};
