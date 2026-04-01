<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_set_tasks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('master_set_id');

            // reference to your setup tables
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->unsignedBigInteger('task_phase_id')->nullable();
            $table->unsignedBigInteger('phase_activity_id');

            // snapshot fields (so report stays consistent even if templates change)
            $table->string('stage_name')->nullable();
            $table->string('phase_name')->nullable();

            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->time('duration')->nullable();
            $table->string('duration_type')->nullable();
            $table->longText('notes')->nullable();
            $table->string('priority')->nullable();
            $table->decimal('percent', 10, 2)->nullable();

            // editable hours on MasterSet level (your “hour of it”)
            $table->decimal('hours', 10, 2)->default(0);

            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('master_set_id')->references('id')->on('master_sets')->onDelete('cascade');
            $table->foreign('stage_id')->references('id')->on('stages')->nullOnDelete();
            $table->foreign('task_phase_id')->references('id')->on('task_phases')->nullOnDelete();
            $table->foreign('phase_activity_id')->references('id')->on('phase_activities')->onDelete('cascade');

            // prevent duplicates in one set
            $table->unique(['master_set_id', 'phase_activity_id'], 'mst_unique_activity_per_set');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_set_tasks');
    }
};

