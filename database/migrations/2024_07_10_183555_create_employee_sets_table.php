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
        Schema::create('employee_sets', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('master_set_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('position_id');  
            $table->integer('work_hour')->nullable();
             $table->double('buying_price', 10.2)->nullable();
            $table->double('sale_price', 10.2)->nullable();
            $table->double('total', 10.2)->nullable();
            
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('master_set_id')->references('id')->on('product_master_sets')->onDelete('cascade');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_sets');
    }
};
