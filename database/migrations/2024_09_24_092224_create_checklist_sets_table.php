g<?php

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
        Schema::create('checklist_sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedBigInteger('master_set_id');
            $table->unsignedBigInteger('designation')->nullable();
            $table->string('ordered')->nullable();
            $table->date('order_date')->nullable();
            $table->unsignedBigInteger('order_by')->nullable();
            $table->string('commisioned')->nullable();
            $table->date('commisioned_date')->nullable();
            $table->unsignedBigInteger('commisioned_by')->nullable(); 
            $table->string('checked')->nullable();
            $table->date('checked_date')->nullable();
            $table->unsignedBigInteger('checked_by')->nullable(); 
            $table->integer('order')->nullable();
            $table->string('status')->nullable();

            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade'); 
            $table->foreign('master_set_id')->references('id')->on('product_master_sets')->onDelete('cascade'); 
            $table->foreign('order_by')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('commisioned_by')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('checked_by')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('designation')->references('id')->on('products');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_sets');
    }
};
