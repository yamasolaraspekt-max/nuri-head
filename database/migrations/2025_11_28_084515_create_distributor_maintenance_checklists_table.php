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
        Schema::create('distributor_maintenance_checklist', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('distributor_id');
            $table->unsignedBigInteger('maintenance_checklist_id');

            $table->timestamps();

            $table->unique(['distributor_id', 'maintenance_checklist_id'], 'dist_mcl_unique');

            $table->foreign('distributor_id', 'dist_mcl_dist_fk')
                ->references('id')
                ->on('distributors')
                ->cascadeOnDelete();

            $table->foreign('maintenance_checklist_id', 'dist_mcl_mcl_fk')
                ->references('id')
                ->on('maintenance_checklists')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_maintenance_checklists');
    }
};
