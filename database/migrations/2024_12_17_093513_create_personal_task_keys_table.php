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
        Schema::create('personal_task_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personal_task_id')->nullable();
            $table->integer('same_id')->nullable();
            $table->string('task')->nullable(); 
            $table->decimal('duration',10,2)->nullable(); 
            $table->string('link')->nullable(); 
            $table->text('key_description')->nullable(); 
            $table->string('status')->nullable();
            $table->integer('is_completed')->nullable();
            $table->unsignedBigInteger('done_by')->nullable();
            $table->date('done_date')->nullable();
            $table->string('done_status')->nullable();
            $table->integer('work_progress')->nullable();
            $table->time('submit_time')->nullable();
            $table->decimal('total_time', 10,2)->nullable();
            $table->longText('reason')->nullable();
            $table->json('employee_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('personal_task_id')->references('id')->on('personal_tasks')->onDelete('cascade');  
            $table->foreign('done_by')->references('id')->on('employees')->onDelete('cascade');  

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_task_keys');
    }
};
