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
        Schema::create('checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->date('mount_plan_start');
            $table->date('mount_plan_end');
            $table->integer('total_days');
            $table->unsignedBigInteger('article_group');
            $table->string('checklist_title');

            $table->date('direct_delivery_date')->nullable();
            $table->time('direct_delivery_time')->nullable();
            $table->string('direct_delivery_inner_unit')->default('false');
            $table->string('direct_delivery_outer_unit')->default('false');
            $table->string('direct_delivery_buffer_storage')->default('false');

            // Crane hub fields
            $table->date('crane_hub_date')->nullable();
            $table->time('crane_hub_time')->nullable();
            $table->string('crane_hub_inner_unit')->default('false');
            $table->string('crane_hub_outer_unit')->default('false');
            $table->string('crane_hub_buffer_storage')->default('false');

            // Old system fields
            $table->string('old_system_oil_heating')->default('false');
            $table->string('old_system_gas_heating')->default('false');
            $table->string('old_system_night_storage')->default('false');
            $table->string('old_system_old_heat_pump')->default('false');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade'); 
           
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};
