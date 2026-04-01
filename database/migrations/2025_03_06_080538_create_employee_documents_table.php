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
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('type'); 
            $table->string('image_name')->nullable();
            $table->string('image'); 
            $table->string('file_type')->nullable();  
            $table->string('status')->nullable();  
            $table->timestamps(); 

            $table->softDeletes(); 
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('created_by')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('updated_by')->references('id')->on('employees')->onDelete('cascade'); 
   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
