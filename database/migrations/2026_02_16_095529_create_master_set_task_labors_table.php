<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_set_task_labors', function (Blueprint $table) {
            $table->id();

            // Link to the specific task line item
            $table->unsignedBigInteger('master_set_task_id');
            
            // The qualification needed (e.g., Geselle, Meister)
            $table->unsignedBigInteger('qualification_id');

            // Hours specific to this task
            $table->decimal('hours', 10, 2)->default(0);
            

            $table->timestamps();

            $table->foreign('master_set_task_id')
                ->references('id')->on('master_set_tasks')
                ->onDelete('cascade');

            $table->foreign('qualification_id')
                ->references('id')->on('position_qualifications')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_set_task_labors');
    }
};