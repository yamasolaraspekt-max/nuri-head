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
        Schema::create('appointment_attachments', function (Blueprint $table) {
          $table->id(); 
            $table->unsignedBigInteger('appointment_id');   
            $table->string('image_name');
            $table->string('image');
            $table->string('file_type');
            $table->softDeletes();
            $table->timestamps();
 
            $table->foreign('appointment_id')->references('id')->on('main_appointments')->onDelete('cascade');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_attachments');
    }
};
