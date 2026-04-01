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
        Schema::create('customer_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->unsigned();
            $table->unsignedBigInteger('product_id')->unsigned();
            $table->unsignedBigInteger('activity_id')->unsigned();
            $table->unsignedBigInteger('employee_id')->unsigned();  
            $table->longText('description');
            $table->string('status')->nullable();
            $table->date('dead_line')->nullable();
            $table->integer('user_id')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_activities');
    }
};
