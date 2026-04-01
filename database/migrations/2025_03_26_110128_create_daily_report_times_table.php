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
        Schema::create('daily_report_times', function (Blueprint $table) {
            $table->id();
        
            // Links
            $table->unsignedBigInteger('daily_report_id')->nullable();
            $table->unsignedBigInteger('work_place_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
        
            // Date and time
            $table->date('report_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('hours_spent', 5, 2)->nullable();
            $table->string('billing_type', 20)->nullable();      // billable, non_billable, internal
            $table->string('activity_category', 50)->nullable(); // e.g. Planung, Montage
            $table->boolean('is_travel')->default(false);   // Reisezeit?
        
            // Polymorphic link (optional)
            $table->string('reportable_type')->nullable(); // e.g. App\Models\PersonalTask
            $table->unsignedBigInteger('reportable_id')->nullable();
        
            // Meta info
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->string('work_status')->nullable(); // e.g. checked_in
        
            // Location/IP info
            $table->string('address')->nullable();
            $table->decimal('lat', 9, 6)->nullable(); 
            $table->decimal('lon', 9, 6)->nullable();
            $table->string('ip')->nullable();

     
            $table->timestamps();
        
            // Foreign keys
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('work_place_id')->references('id')->on('daily_report_work_places')->onDelete('cascade');
            $table->foreign('daily_report_id')->references('id')->on('daily_reports')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
        
            // Polymorphic index
            $table->index(['reportable_type', 'reportable_id']);
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_times');
    }
};
