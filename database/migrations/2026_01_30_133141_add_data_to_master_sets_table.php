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
        Schema::table('master_sets', function (Blueprint $table) {
            $table->unsignedBigInteger('task_phase_id')->nullable()->after('id');
            $table->unsignedBigInteger('phase_activity_id')->nullable()->after('task_phase_id');

            $table->foreign('task_phase_id')->references('id')->on('task_phases')->onDelete('set null');
            $table->foreign('phase_activity_id')->references('id')->on('phase_activities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_sets', function (Blueprint $table) {
            //
        });
    }
};
