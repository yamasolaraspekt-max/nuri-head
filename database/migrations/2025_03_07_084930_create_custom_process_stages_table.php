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
        Schema::create('custom_process_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_process_id');
            $table->unsignedBigInteger('employee_id')->nullabe();
            $table->unsignedBigInteger('product_id')->nullabe();
            $table->unsignedBigInteger('customer_id')->nullabe();
            $table->unsignedBigInteger('alternative_id')->nullabe();
            $table->string('service')->nullabe();
            $table->string('status');
            $table->timestamps();

            $table->foreign('custom_process_id')->references('id')->on('custom_processes')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_process_stages');
    }
};
