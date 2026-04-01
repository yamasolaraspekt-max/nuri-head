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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('emp_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('request_to');
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->integer('year')->nullable();
            $table->string('leave_type')->nullable();
            $table->string('reason')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('old_start')->nullable();
            $table->date('old_end')->nullable();
            $table->integer('remaining_day')->default(0);
            $table->integer('leave_day')->default(0);
            $table->integer('duration')->default(0); 
            $table->string('paid')->nullable();
            $table->string('approved')->nullable();
            $table->string('status')->nullable();
            $table->string('request_back')->nullable();
            $table->string('request_answer')->nullable();
            $table->longText('description')->nullable();
            $table->json('notes')->nullable(); 
            $table->timestamps(); 
            $table->foreign('emp_id')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('created_by')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('request_to')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('changed_by')->references('id')->on('employees')->onDelete('cascade'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
