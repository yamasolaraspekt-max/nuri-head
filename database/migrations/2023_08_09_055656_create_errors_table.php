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
        Schema::create('errors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('article_name')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('error_code')->nullable();
            $table->string('problem_types')->nullable();
            $table->date('latest_update')->nullable();
            $table->longText('reason')->nullable();
            $table->longText('solution')->nullable();
            $table->string('file')->nullable();
            $table->string('links')->nullable();
            $table->string('status')->nullable(); 
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('errors');
    }
};
