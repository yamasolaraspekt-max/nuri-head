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
       Schema::create('stamp_article_list_items', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PRIMARY KEY

            $table->unsignedBigInteger('stamp_article_id');
            $table->unsignedBigInteger('stamp_article_list_id');
            $table->unsignedBigInteger('employee_id'); 
            $table->string('note')->nullable(); 
            $table->timestamps(); 

         $table->foreign('stamp_article_id')->references('id')->on('products')->onDelete('cascade');
         $table->foreign('stamp_article_list_id')->references('id')->on('stamp_article_lists')->onDelete('cascade');
         $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
             
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stamp_article_list_items');
    }
};
