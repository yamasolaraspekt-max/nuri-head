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
        Schema::create('task_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('alternative')->nullable();
            $table->unsignedBigInteger('phase_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('activities_id');
            $table->unsignedBigInteger('sub_task_id')->nullable();
            $table->string('document_name')->nullable();
            $table->string('document')->nullable(); // Address of Docuemnt PDF...
            $table->string('document_sum')->nullable();
            $table->string('document_note')->nullable();
            $table->string('document_status')->nullable(); 
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');
            $table->foreign('activities_id')->references('id')->on('phase_activities')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade'); 
            $table->foreign('sub_task_id')->references('id')->on('task_sub_tasks')->onDelete('cascade'); 
            $table->foreign('alternative')->references('id')->on('lead_alternative_adds')->onDelete('cascade'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_documents');
    }
};
