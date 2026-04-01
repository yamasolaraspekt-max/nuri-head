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
        Schema::create('employee_recurring_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');

            $table->string('title')->nullable();       // e.g. "School", "Seminar"
            $table->text('description')->nullable();

            // Recurrence definition
            $table->enum('type', ['weekly','monthly','interval','one_time']); 
            // human-friendly type selection in UI

            $table->enum('frequency', ['daily','weekly','monthly','yearly'])->nullable();
            $table->unsignedInteger('interval')->default(1); 
            // every X (days/weeks/months/years)

            // Which weekdays (if weekly)
            $table->json('weekdays')->nullable(); 
            $table->json('day_of_week')->nullable();

            // e.g. [1,2,3] = Mon/Tue/Wed

            // Week/Month intervals
            $table->unsignedInteger('week_interval')->nullable();   // e.g. every 2nd week
            $table->unsignedInteger('month_interval')->nullable();  // e.g. every 3 months

            // Multi-day span
            $table->unsignedTinyInteger('duration_days')->default(1);
            // e.g. 7 → whole week, 4 → Mon–Thu

            // Monthly specific
            $table->unsignedTinyInteger('day_of_month')->nullable(); 
            // e.g. 15 = every 15th of month

            // Time range
            $table->boolean('all_day')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Overall date span
            $table->date('start_date'); 
            $table->date('end_date')->nullable(); // until when

            $table->boolean('is_active')->default(true);

            $table->json('exdates')->nullable();   // ["2025-10-12","2025-10-26"]
            $table->json('rdates')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_recurring_leaves');
    }
};
