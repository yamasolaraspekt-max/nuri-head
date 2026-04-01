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
       Schema::create('stamp_article_lists', function (Blueprint $table) {
        $table->id();  

        $table->unsignedBigInteger('employee_id');
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('color')->nullable();
        $table->string('icon')->nullable();
        $table->boolean('is_shared')->default(false);
        $table->text('description')->nullable(); 
        $table->timestamps();

         $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
    });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stamp_article_lists');
    }
};
