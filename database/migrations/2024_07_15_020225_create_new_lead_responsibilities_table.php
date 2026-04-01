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
        Schema::create('new_lead_responsibilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('new_lead_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('current_employee');
            $table->unsignedBigInteger('product_id'); 
            $table->unsignedBigInteger('alternative_id'); 
            $table->string('status')->default('Published');
            $table->longText('reason')->nullable();
            $table->foreign('new_lead_id')->references('id')->on('new_leads')->onDelete('cascade'); 
            $table->foreign('employee_id')->references('id')->on('employees'); 
            $table->foreign('current_employee')->references('id')->on('employees'); 
            $table->foreign('product_id')->references('id')->on('article_groups'); 
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_lead_responsibilities');
    }
};
