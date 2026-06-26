<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('general_task_steps')) {
            Schema::create('general_task_steps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('general_task_id')->constrained('general_tasks')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(1);
                $table->unsignedInteger('soll_minutes')->default(0);
                $table->unsignedInteger('ist_minutes')->default(0);
                $table->boolean('is_done')->default(false);
                $table->foreignId('checked_by')->nullable()->constrained('employees')->nullOnDelete();
                $table->timestamp('checked_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('employees')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['general_task_id', 'sort_order']);
                $table->index(['is_done', 'checked_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('general_task_steps');
    }
};
