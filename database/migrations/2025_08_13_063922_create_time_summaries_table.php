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
         Schema::create('time_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('section_id');        // (= service_id)
            $table->enum('scope', ['total','phase']);
            $table->unsignedBigInteger('phase_id')->nullable();

            $table->unsignedInteger('plan_minutes')->default(0);
            $table->unsignedInteger('actual_minutes')->default(0);
            $table->integer('diff_minutes')->default(0);
            $table->unsignedInteger('completed_cap_minutes')->default(0);
            $table->unsignedTinyInteger('weighted_percent')->default(0);

            $table->unsignedInteger('activities_count')->default(0);
            $table->unsignedInteger('done_activities_count')->default(0);
            $table->unsignedInteger('half_activities_count')->default(0);
            $table->unsignedInteger('overruns_count')->default(0);
            $table->date('latest_done_date')->nullable();

            $table->timestamps();

            // Remove the long auto-named index line entirely

            // Keep a short unique name
            $table->unique(
                ['customer_id','alternative_id','product_id','section_id','scope','phase_id'],
                'ts_uniq'
            );
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_summaries');
    }
};
