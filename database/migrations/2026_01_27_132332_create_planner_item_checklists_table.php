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
      Schema::create('planner_item_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('planner_item_id')->index();
            
            $table->string('title');
            $table->boolean('is_completed')->default(false);
            $table->integer('sort_order')->default(0);
            
            // Optional: Link back to original source if you want 2-way sync later
            // e.g., 'ticket_task_id' or generic 'source_external_id'
            $table->unsignedBigInteger('source_external_id')->nullable(); 
            
            $table->timestamps();

            $table->foreign('planner_item_id')
                ->references('id')
                ->on('planner_items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planner_item_checklists');
    }
};
