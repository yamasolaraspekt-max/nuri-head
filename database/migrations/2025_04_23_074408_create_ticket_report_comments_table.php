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
       Schema::create('ticket_report_comments', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('ticket_report_id');
            $table->unsignedBigInteger('liked_by');
            $table->unsignedBigInteger('comment_by');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('product_id');

            // Comment body
            $table->longText('comment')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('ticket_id')->references('id')->on('problems')->onDelete('cascade');
            $table->foreign('ticket_report_id')->references('id')->on('ticket_reports')->onDelete('cascade');
            $table->foreign('liked_by')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('comment_by')->references('id')->on('employees')->onDelete('cascade');
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
        Schema::dropIfExists('ticket_report_comments');
    }
};
