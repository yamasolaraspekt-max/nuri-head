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
        Schema::create('personal_task_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('type')->nullable();       // z.B. status_changed, key_done, comment_added
            $table->string('title')->nullable();      // kurze Überschrift
            $table->text('description')->nullable();  // Details
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('personal_tasks')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_task_histories');
    }
};
