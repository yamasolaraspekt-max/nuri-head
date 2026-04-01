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
        Schema::create('personal_tasks', function (Blueprint $table) {
            $table->id(); 
            $table->string('task_id')->nullable(); 
            $table->unsignedBigInteger('customer_id')->nullable(); 
            $table->unsignedBigInteger('alternative_id')->nullable(); 
            $table->unsignedBigInteger('product_id')->nullable(); 
            $table->string('is_customer')->default('false');
            $table->string('public')->nullable();
            $table->string('task_title')->nullable();
            $table->longText('description')->nullable(); 
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->string('task_status')->nullable();
            $table->string('priority')->nullable(); 
            $table->string('color')->nullable(); 
            $table->string('progress')->nullable(); 
            $table->string('repeat')->nullable();
            $table->date('reminder_date')->nullable();
            $table->time('reminder_time')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->time('due_time')->nullable();
            $table->decimal('total_day', 10,2)->nullable();
            $table->decimal('total_time', 10,2)->nullable();
            $table->boolean('is_notified')->default(false)->nullable();
            $table->string('type')->default('personal_task');
            $table->json('controller_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('assigned_by')->references('id')->on('employees')->onDelete('cascade'); 
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
        Schema::dropIfExists('personal_tasks');
    }
};
