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
        Schema::create('planner_item_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('planner_item_id')->index();
            
            // Optional: Link back to original Master Set ID for reference
            $table->unsignedBigInteger('master_set_id')->nullable(); 

            // Material Details (Snapshot)
            $table->string('name'); // Product Name
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable(); // Cached total

            $table->timestamps();

            $table->foreign('planner_item_id')
                  ->references('id')->on('planner_items')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planner_item_materials');
    }
};
