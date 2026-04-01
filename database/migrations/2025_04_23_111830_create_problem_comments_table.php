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
       Schema::create('problem_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('problems')->onDelete('cascade');
            $table->unsignedBigInteger('ticket_task_id')->nullable();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('problem_comments')->onDelete('cascade');
            $table->longText('comment')->nullable();
            $table->unsignedInteger('likes')->default(0);
            $table->timestamps();

            $table->foreign('ticket_task_id')->references('id')->on('ticket_tasks')->onDelete('cascade');

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problem_comments');
    }
};
