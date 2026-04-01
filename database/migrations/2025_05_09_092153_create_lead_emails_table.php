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
       Schema::create('lead_emails', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique();
            $table->string('from');
            $table->string('domain')->nullable(); // just add here
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->boolean('is_read')->default(false); // no 'after'
            $table->timestamp('date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_emails');
    }
};
