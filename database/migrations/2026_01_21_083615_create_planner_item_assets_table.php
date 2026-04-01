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
        Schema::create('planner_item_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('planner_item_id')->index();
            $table->unsignedBigInteger('asset_id')->index(); // your inventory/assets table id
            $table->unsignedInteger('qty')->default(1);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['planner_item_id', 'asset_id'], 'planner_item_assets_unique');
            $table->foreign('planner_item_id')->references('id')->on('planner_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planner_item_assets');
    }
};
