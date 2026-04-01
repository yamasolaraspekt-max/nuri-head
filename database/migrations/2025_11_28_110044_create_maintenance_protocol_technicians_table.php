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
        Schema::create('maintenance_protocol_technicians', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('maintenance_protocol_id');
            $table->unsignedBigInteger('employee_id');

            $table->string('role')->nullable();          // lead, assistant, helper ...
            $table->string('status')->default('planned'); // planned / started / completed

            $table->decimal('planned_hours', 5, 2)->nullable();
            $table->decimal('actual_hours', 5, 2)->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->foreign('maintenance_protocol_id', 'mpt_protocol_fk')
                ->references('id')->on('maintenance_protocols')
                ->cascadeOnDelete();

            $table->foreign('employee_id', 'mpt_employee_fk')
                ->references('id')->on('employees')
                ->cascadeOnDelete();

            $table->unique(
                ['maintenance_protocol_id', 'employee_id'],
                'mpt_protocol_employee_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_protocol_technicians');
    }
};
