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
        Schema::create('customer_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('report_by');
            $table->string('stage')->nullable();
            $table->longText('report')->nullable();
            $table->json('report_details')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('report_by')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade'); 
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade'); 
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('set null'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_reports');
    }
};
