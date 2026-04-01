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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('ticket_no');
            $table->string('title');
            $table->longText('description');
            $table->string('image_path')->nullable();
            $table->string('status')->nullable();
            $table->string('response')->nullable();
            $table->integer('main_feed')->nullable(); 
            $table->date('fixed_date')->nullable(); 
            $table->date('progress_date')->nullable(); 
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
