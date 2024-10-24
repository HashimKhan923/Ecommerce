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
        Schema::create('email_batches', function (Blueprint $table) {
            $table->id();
            $table->integer('batch_id')->nullable();
            $table->integer('total_emails')->default(0);        
            $table->integer('successful_emails')->default(0);   
            $table->integer('failed_emails')->default(0);       
            $table->integer('spam_emails')->default(0); 
            $table->integer('from_id')->nullable();
            $table->integer('to_id')->nullable();    
            $table->string('status')->default('sending');
            $table->timestamp('start_at')->nullable();   
            $table->timestamp('completed_at')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_batches');
    }
};
