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
        Schema::create('maintenance_protocols', function (Blueprint $table) {
            $table->id();

            // Kunde / Objekt
            $table->unsignedBigInteger('lead_id');        // new_leads.id
            $table->unsignedBigInteger('alternative_id'); // lead_alternative_adds.id

            // Anlage + Vertrag + verwendete Checkliste
            $table->unsignedBigInteger('maintenance_asset_id')->nullable();
            $table->unsignedBigInteger('maintenance_contract_id')->nullable();
            $table->unsignedBigInteger('maintenance_checklist_id')->nullable();

            $table->string('protocol_no')->nullable()->unique();
            $table->string('title')->nullable();          // z.B. "Jahreswartung 2025"

            // Zeitliche Infos
            $table->timestamp('scheduled_at')->nullable(); // geplantes Zeitfenster (Start)
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->string('status')->default('planned'); // planned / in_progress / completed / cancelled

            // Snapshot der Checkliste zum Zeitpunkt der Wartung
            $table->json('checklist_snapshot')->nullable();

            // Antworten strukturiert, z.B.
            // { "pipes_status": {"value":"OK","comment":"..."} , ... }
            $table->json('answers')->nullable();

            $table->text('result_summary')->nullable();
            $table->text('internal_note')->nullable();

            $table->json('meta')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')
                ->references('id')->on('new_leads')
                ->cascadeOnDelete();

            $table->foreign('alternative_id')
                ->references('id')->on('lead_alternative_adds')
                ->cascadeOnDelete();

            $table->foreign('maintenance_asset_id')
                ->references('id')->on('maintenance_assets')
                ->nullOnDelete();

            $table->foreign('maintenance_contract_id')
                ->references('id')->on('maintenance_contracts')
                ->nullOnDelete();

            $table->foreign('maintenance_checklist_id')
                ->references('id')->on('maintenance_checklists')
                ->nullOnDelete();

            $table->index(['lead_id', 'alternative_id']);
            $table->index(['status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_protocols');
    }
};
