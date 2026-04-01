<?php

 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_set_checklists', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('master_set_id');
            $table->unsignedBigInteger('maintenance_checklist_id');

            // WHEN this checklist is needed in the project flow
            // examples: start, middle, end, review, acceptance, qa
            $table->string('trigger')->default('start');

            $table->boolean('is_required')->default(true);

            // sort in UI
            $table->unsignedInteger('sort_order')->default(0);

            // optional snapshots (helpful if title changes later)
            $table->string('checklist_title_snapshot')->nullable();
            $table->string('checklist_type_snapshot')->nullable();

            $table->timestamps();

            // ✅ prevent duplicates in same set (same checklist + same trigger)
            $table->unique(
                ['master_set_id', 'maintenance_checklist_id', 'trigger'],
                'ms_mcl_unique'
            );

            $table->foreign('master_set_id', 'ms_mcl_ms_fk')
                ->references('id')->on('master_sets')
                ->cascadeOnDelete();

            $table->foreign('maintenance_checklist_id', 'ms_mcl_mcl_fk')
                ->references('id')->on('maintenance_checklists')
                ->cascadeOnDelete();

            $table->index(['master_set_id', 'trigger'], 'ms_mcl_ms_trigger_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_set_checklists');
    }
};
