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
        Schema::create('brand_maintenance_checklist', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('maintenance_checklist_id');

            $table->timestamps();

            $table->unique(['brand_id', 'maintenance_checklist_id'], 'brand_mcl_unique');

            $table->foreign('brand_id', 'brand_mcl_brand_fk')
                ->references('id')
                ->on('brands')
                ->cascadeOnDelete();

            $table->foreign('maintenance_checklist_id', 'brand_mcl_mcl_fk')
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
        Schema::dropIfExists('brand_maintenance_checklists');
    }
};
