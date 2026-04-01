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
        Schema::create('time_management_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->date('work_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('break_minutes')->default(0);
            $table->decimal('hours', 5, 2);
            $table->timestamps();

            $table->foreign('plan_id')
                ->references('id')->on('time_management_plans')
                ->onDelete('cascade');

            $table->unique(['plan_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_management_entries');
    }
};
