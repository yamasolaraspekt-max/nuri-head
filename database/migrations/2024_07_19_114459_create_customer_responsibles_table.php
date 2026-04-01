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
        Schema::create('customer_responsibles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('current_employee');
            $table->unsignedBigInteger('product_id');
            $table->string('status')->default('Published');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); 
            $table->foreign('employee_id')->references('id')->on('employees'); 
            $table->foreign('current_employee')->references('id')->on('employees'); 
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_responsibles');
    }
};
