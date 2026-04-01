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
        Schema::create('wp_fusion_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->dateTime('time')->nullable(); // FIXED: from time() to dateTime()
            $table->string('source_url')->nullable(); 
            $table->integer('post_id')->nullable(); 
            $table->integer('user_id')->nullable(); 
            $table->text('user_agent')->nullable(); // FIXED: from integer() to text()
            $table->string('ip')->nullable(); 
            $table->boolean('is_read')->nullable(); // Optional: boolean if it's true/false
            $table->date('privacy_scrub_date')->nullable(); 
            $table->string('on_privacy_scrub')->nullable(); 
            $table->longText('data')->nullable(); 
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wp_fusion_form_submissions');
    }
};
