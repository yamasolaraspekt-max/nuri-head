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
        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_no');
            $table->string('source')->nullable();
            $table->string('error_code')->nullable();
            $table->string('error_type')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('alternative_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('start_user')->nullable();
            $table->unsignedBigInteger('progress_user')->nullable();
            $table->unsignedBigInteger('end_user')->nullable();
            $table->unsignedBigInteger('edit_user')->nullable();
            $table->string('article_name')->nullable();
            $table->string('article_sn')->nullable();
            $table->date('installation_date')->nullable();
            $table->string('warranty_type')->nullable();
            $table->string('warranty_duration')->nullable();
            $table->integer('warranty_remaining')->nullable();
            $table->string('finance_to')->nullable();
            $table->date('date')->nullable();
            $table->date('progress_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('total_time')->nullable();
            $table->unsignedBigInteger('responsible')->nullable();
            $table->unsignedBigInteger('first_contact')->nullable();
            $table->longText('problem')->nullable();
            $table->longText('solution')->nullable();
            $table->longText('progress')->nullable();
            $table->integer('image_id')->nullable();
            $table->string('status')->nullable(); 
            $table->date('edit_date')->nullable();
            $table->string('priority')->nullable();
            $table->integer('repeated')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade'); 
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade'); 
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade'); 
            $table->foreign('responsible')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('first_contact')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('start_user')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('progress_user')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('end_user')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('edit_user')->references('id')->on('employees')->onDelete('cascade'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problems');
    }
};
