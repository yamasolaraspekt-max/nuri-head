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
        Schema::create('product_master_sets', function (Blueprint $table) {
            $table->id();
            $table->string('setname');
            $table->unsignedBigInteger('article_group');
            $table->unsignedBigInteger('sub_article');   
            $table->unsignedBigInteger('phase_id');   
            $table->double('price', 10.1)->nullable();
            $table->double('employee_price', 10.1)->nullable();
            $table->double('material_price', 10.1)->nullable();
            $table->integer('employee_percent')->nullable();
            $table->integer('material_percent')->nullable();
            $table->double('asset_price', 10.1)->nullable();
            $table->integer('asset_percent')->nullable();
            $table->string('status')->nullable();
            $table->integer('product_parent_id')->nullable();
            $table->string('product_parent_name')->nullable(); 
            $table->timestamps();

            $table->foreign('article_group')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('sub_article')->references('id')->on('sub_article_groups')->onDelete('cascade');
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_master_sets');
    }
};
