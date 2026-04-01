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
       Schema::create('customer_report_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');         // Link to customer_reports
            $table->unsignedBigInteger('user_id');           // Authenticated user
            $table->unsignedBigInteger('parent_id')->nullable(); // Replying to another comment
            $table->text('comment');
            $table->timestamps();

            $table->foreign('report_id')->references('id')->on('customer_reports')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('customer_report_comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_report_comments');
    }
};
