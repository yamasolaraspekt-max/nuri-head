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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('responsible_id')->nullable(); 
            $table->string('serial_no')->nullable();
            $table->string('article_no')->nullable();
            $table->string('ean')->nullable();
            $table->string('manual_no')->nullable();
            $table->string('location')->nullable();
            $table->string('shelf')->nullable();
            $table->string('row')->nullable();
            $table->integer('quantity')->nullable();
            $table->unsignedBigInteger('delete_by')->nullable();  
            $table->date('delete_date')->nullable();
            $table->unsignedBigInteger('edit_by')->nullable();  
            $table->date('edit_date')->nullable(); 

            $table->unsignedBigInteger('add_by')->nullable(); 

            $table->date('add_date')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('responsible_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('add_by')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('delete_by')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('edit_by')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
