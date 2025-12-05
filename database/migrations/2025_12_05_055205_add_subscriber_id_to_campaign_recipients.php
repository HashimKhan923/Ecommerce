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
        Schema::table('campaign_recipients', function (Blueprint $table) {

            // Drop old unique index
            $table->dropUnique('campaign_recipients_campaign_id_user_id_unique');

            // Make user_id nullable
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            // Add subscriber_id nullable
            $table->unsignedBigInteger('subscriber_id')->nullable()->after('user_id');
            $table->foreign('subscriber_id')
                ->references('id')->on('subscribers')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // New combined unique index
            $table->unique(['campaign_id', 'user_id', 'subscriber_id'], 'campaign_user_subscriber_unique');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_recipients', function (Blueprint $table) {
            //
        });
    }
};
