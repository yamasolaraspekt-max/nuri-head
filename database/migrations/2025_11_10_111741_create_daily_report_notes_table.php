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
       Schema::create('daily_report_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');               // author
            $table->unsignedBigInteger('employee_id');               // author
            $table->unsignedBigInteger('daily_report_time_id')->nullable(); // row id
            $table->date('report_date');                             // date context
            $table->text('message');
            $table->timestamps();

            $table->foreign('report_id')->references('id')->on('daily_reports')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('daily_report_time_id')->references('id')->on('daily_report_times')->cascadeOnDelete();
            $table->index(['report_date','daily_report_time_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_notes');
    }
};
