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
        Schema::create('ticket_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('employee_id'); 
            $table->unsignedBigInteger('customer_id'); 
            $table->unsignedBigInteger('alternative_id'); 
            $table->unsignedBigInteger('product_id'); 
            $table->string('title')->nullable();
            $table->longText('report')->nullable();
            $table->string('language')->nullable(); 
            $table->date('report_date')->nullable();
            $table->unsignedInteger('likes')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ticket_id')->references('id')->on('problems')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_reports');
    }
};
