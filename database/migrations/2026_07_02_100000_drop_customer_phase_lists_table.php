<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drop der faktisch toten Tabelle customer_phase_lists (Cleanup Schritt 3, final).
 *
 * Hintergrund: customer_phase_lists war funktional kaputt — der gesamte Feature-Code
 * las/schrieb gegen nicht-existente Spalten (customer/product/alternative statt
 * customer_id/product_id/alternative_id) und crashte bei jedem Zugriff mit
 * SQLSTATE[42S22] "Unknown column" (live bewiesen). Der komplette Feature-Code wurde
 * bereits entfernt (Commits f51bb08 + f6a8b4c: Controller, Model, 2 Views, 9 Routen,
 * get_phase, TaskPhase-Relation). Die Tabelle war leer (0 Zeilen) und hatte KEINE
 * eingehenden Fremdschluessel (keine andere Tabelle referenziert sie).
 *
 * Belege: docs/customer-phase-lists-nutzung-geklaert.md.
 *
 * up()   = Tabelle droppen.
 * down() = originalgetreue Wiederherstellung (33 Spalten + 13 Fremdschluessel +
 *          timestamps), 1:1 uebernommen aus der Original-Create-Migration
 *          2025_04_01_091560_create_customer_phase_lists_table.php, damit der Drop
 *          per `php artisan migrate:rollback` vollstaendig umkehrbar bleibt.
 *          Die alte Create-Migration wird bewusst NICHT geloescht (Historie).
 */
return new class extends Migration
{
    /**
     * Run the migrations. (Drop der toten Tabelle.)
     */
    public function up(): void
    {
        Schema::dropIfExists('customer_phase_lists');
    }

    /**
     * Reverse the migrations. (Vollstaendige Wiederherstellung — Backup-Sicherheitsnetz.)
     * Struktur 1:1 aus 2025_04_01_091560_create_customer_phase_lists_table.php.
     */
    public function down(): void
    {
        Schema::create('customer_phase_lists', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('checklist_id')->nullable();
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->unsignedBigInteger('activities_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('service')->nullable();
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('contact_person')->nullable();
            $table->unsignedBigInteger('responsible_person')->nullable();
            $table->unsignedBigInteger('outside_service')->nullable();
            $table->unsignedBigInteger('outside_company')->nullable();

            $table->string('color')->nullable();
            $table->string('active_by')->nullable();
            $table->string('jump_steps')->nullable();
            $table->string('jump_steps_by')->nullable();

            $table->enum('done', ['true', 'false']); // Constraint on done values
            $table->enum('type', ['main', 'sub']); // Constraint on type values
            $table->integer('main_id')->nullable();
            $table->string('outside_type')->default('internal');
            $table->date('done_date')->nullable();
            $table->longText('reason')->nullable();
            $table->string('done_status')->nullable();
            $table->string('status')->nullable();
            $table->integer('work_progress')->nullable();
            $table->time('more_time')->nullable();
            $table->decimal('total_time', 10, 2)->nullable();

            // Foreign Key Constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('checklist_id')->references('id')->on('project_montage_checklists')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade');
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');
            $table->foreign('activities_id')->references('id')->on('phase_activities')->onDelete('cascade');
            $table->foreign('contact_person')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('responsible_person')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('outside_service')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('outside_company')->references('id')->on('external_personals')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('phase_sections')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');

            $table->timestamps();
        });
    }
};
