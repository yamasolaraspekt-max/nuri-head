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
       Schema::create('project_timeline_done_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('timeline_id');
            $table->unsignedBigInteger('done_by')->nullable();
            $table->date('done_date')->nullable();
            $table->integer('timeline_range')->nullable(); // matches with done_range values
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('timeline_id')->references('id')->on('project_timelines')->onDelete('cascade');
            $table->foreign('done_by')->references('id')->on('employees')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_timeline_done_dates');
    }
};
