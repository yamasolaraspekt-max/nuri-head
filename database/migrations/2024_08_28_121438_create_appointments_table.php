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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->string('postcode')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('priority')->nullable();
            $table->string('color')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable(); 
            $table->integer('report_date_type')->nullable(); 
            $table->date('report_date')->nullable(); 
            $table->date('feedback_date')->nullable(); 
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->date('update_start_date')->nullable();
            $table->date('update_end_date')->nullable();
            $table->date('update_start_time')->nullable();
            $table->date('update_end_time')->nullable();
            $table->integer('total_hour')->nullable();  
            $table->integer('updated_by')->nullable();
            $table->longText('update_reason')->nullable(); 
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->longText('delete_reason')->nullable();
            $table->integer('postpond_request')->nullable(); 
            $table->longText('postpond_reason')->nullable(); 
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('phase_activities')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
