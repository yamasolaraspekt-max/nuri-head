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
       Schema::create('project_task_comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('phase_id')->constrained('task_phases')->onDelete('cascade');
            $table->foreignId('activity_id')->constrained('phase_activities')->onDelete('cascade');

            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade'); // changed from comment_by to employee_id

            $table->longText('comment');
            $table->enum('status', ['Published', 'Draft', 'Hidden'])->default('Published');
            $table->foreignId('parent_id')->nullable()->constrained('project_task_comments')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'phase_id', 'activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task_comments');
    }
};
