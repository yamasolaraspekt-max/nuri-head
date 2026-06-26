<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('general_task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('general_tasks')->cascadeOnDelete();
            $table->foreignId('depends_on_task_id')->constrained('general_tasks')->cascadeOnDelete();
            $table->string('type')->default('finish_to_start');
            $table->integer('lag_days')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();

            $table->unique(['task_id', 'depends_on_task_id'], 'general_task_dep_unique');
            $table->index('depends_on_task_id', 'general_task_dep_parent_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_task_dependencies');
    }
};
