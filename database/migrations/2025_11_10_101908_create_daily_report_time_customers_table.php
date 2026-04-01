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
        Schema::create('daily_report_time_customers', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('report_time_id');
            $t->unsignedBigInteger('customer_id');
            // Either use share_hours OR share_percent. Prefer share_hours.
            $t->decimal('share_hours', 5, 2)->nullable();    // e.g. 0.20
            $t->decimal('share_percent', 5, 2)->nullable();  // e.g. 20.00
            $t->text('note')->nullable();
            $t->timestamps();

            $t->unique(['report_time_id','customer_id']); // one customer only once per block
            $t->foreign('report_time_id')->references('id')->on('daily_report_times')->onDelete('cascade');
            $t->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
        });

          DB::statement("
            INSERT INTO daily_report_time_customers (report_time_id, customer_id, share_hours, share_percent, created_at, updated_at)
            SELECT id, customer_id, hours_spent, CASE WHEN hours_spent>0 THEN 100.0 ELSE NULL END, NOW(), NOW()
            FROM daily_report_times
            WHERE customer_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_time_customers');
    }
};
