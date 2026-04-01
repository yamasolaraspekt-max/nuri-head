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
        Schema::create('project_montage_phase_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_montage_id');
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->unsignedBigInteger('activity_id')->nullable();
            
            $table->timestamps();

            $table->foreign('project_montage_id')->references('id')->on('project_montage_checklists')->onDelete('cascade');
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('phase_activities')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_montage_phase_lists');
    }
};
