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
        Schema::create('project_montage_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('list_name')->nullable();
             $table->integer('plan_montage')->nullable();
            $table->integer('supplier_section')->nullable();
            $table->integer('cran_section')->nullable();
            $table->integer('old_facility')->nullable();
            $table->integer('photo_section')->nullable();
            $table->integer('commission')->nullable();
            $table->string('status')->nullable();
            $table->string('default_stage')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_montage_checklists');
    }
};
