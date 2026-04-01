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
        Schema::create('customer_stages', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('alternative_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable(); 
            $table->unsignedBigInteger('section_id')->nullable(); 
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable();

            $table->string('version')->nullable(); 
            $table->string('status')->nullable();

            $table->timestamps();

            // Foreign keys with custom constraint names
            $table->foreign('customer_id', 'fk_customer_stages_customer')
                ->references('id')->on('new_leads')->onDelete('set null');  

            $table->foreign('alternative_id', 'fk_customer_stages_alternative')
                ->references('id')->on('lead_alternative_adds')->onDelete('set null');  

            $table->foreign('product_id', 'fk_customer_stages_product')
                ->references('id')->on('article_groups')->onDelete('set null');  

            $table->foreign('section_id', 'fk_customer_stages_section')
                ->references('id')->on('phase_sections')->onDelete('set null');  

            $table->foreign('phase_id', 'fk_customer_phase')
                ->references('id')->on('task_phases')->onDelete('set null');  

            $table->foreign('task_id', 'fk_customer_task')
                ->references('id')->on('phase_activities')->onDelete('set null');  

            $table->foreign('stage_id')->references('id')->on('stages')->onDelete('set null');

        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_stages');
    }
};
