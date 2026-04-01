<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('planner_item_master_sets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('planner_item_id')->index();
            $table->unsignedBigInteger('master_set_id')->index();

            $table->timestamps();

            // prevent duplicates
            $table->unique(['planner_item_id', 'master_set_id'], 'pims_unique');

            // short FK names to avoid "identifier too long"
            $table->foreign('planner_item_id', 'pims_item_fk')
                ->references('id')->on('planner_items')
                ->onDelete('cascade');

            $table->foreign('master_set_id', 'pims_set_fk')
                ->references('id')->on('master_sets')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planner_item_master_sets');
    }
};
