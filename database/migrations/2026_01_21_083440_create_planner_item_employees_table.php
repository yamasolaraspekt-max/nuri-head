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
       Schema::create('planner_item_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('planner_item_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->string('role', 30)->default('member'); // lead|member
            $table->timestamps();

            $table->unique(['planner_item_id', 'employee_id'], 'planner_item_employees_unique');
            $table->foreign('planner_item_id')->references('id')->on('planner_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planner_item_employees');
    }
};
