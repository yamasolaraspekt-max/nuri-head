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
        Schema::create('checklist_assembles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id')->nullable();
            $table->unsignedBigInteger('checklist_set_id')->nullable();
            $table->unsignedBigInteger('master_set_id');
            $table->string('assembled')->default('false');
            $table->date('assembled_date')->nullable();
            $table->unsignedBigInteger('assembled_by')->nullable();

            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade'); 
            $table->foreign('checklist_set_id')->references('id')->on('checklist_sets')->onDelete('cascade'); 
            $table->foreign('master_set_id')->references('id')->on('product_master_sets')->onDelete('cascade'); 
            $table->foreign('assembled_by')->references('id')->on('employees')->onDelete('cascade'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_assembles');
    }
};
