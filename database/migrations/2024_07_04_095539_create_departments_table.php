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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('department_name');
            $table->unsignedBigInteger('parent_id')->nullable(); 
            $table->unsignedBigInteger('branch_id')->nullable(); 
            $table->unsignedBigInteger('department_head')->nullable(); 
            $table->unsignedBigInteger('head_representative')->nullable(); 
            $table->integer('order')->nullable(); 
            $table->longText('description')->default(0);
            $table->string('status')->default('Published');
            $table->timestamps();
             $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('department_head')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('head_representative')->references('id')->on('employees')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
