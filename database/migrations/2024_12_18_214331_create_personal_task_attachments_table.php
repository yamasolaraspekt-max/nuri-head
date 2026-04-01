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
        Schema::create('personal_task_attachments', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('task_id');   
            $table->unsignedBigInteger('customer_id')->nullable();   
            $table->string('image_name');
            $table->string('image');
            $table->string('file_type');
            $table->softDeletes();
            $table->timestamps();
 
            $table->foreign('task_id')->references('id')->on('personal_tasks')->onDelete('cascade');  
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');  

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_task_attachments');
    }
};
