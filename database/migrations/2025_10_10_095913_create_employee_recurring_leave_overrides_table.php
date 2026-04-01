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
        Schema::create('employee_recurring_leave_overrides', function (Blueprint $table) {
            $table->id();

            // keep the column name if you like
            $table->unsignedBigInteger('employee_recurring_leave_id');

            $table->date('original_date');
            $table->boolean('is_cancelled')->default(false);

            $table->date('new_date')->nullable();
            $table->boolean('new_all_day')->nullable();
            $table->time('new_start_time')->nullable();
            $table->time('new_end_time')->nullable();
            $table->unsignedTinyInteger('new_duration_days')->nullable();

            $table->string('new_title')->nullable();
            $table->text('new_description')->nullable();

            $table->timestamps();

            // ✅ short, explicit names
            $table->foreign('employee_recurring_leave_id', 'erl_override_leave_fk')
                ->references('id')->on('employee_recurring_leaves')
                ->onDelete('cascade');

            $table->unique(
                ['employee_recurring_leave_id','original_date'],
                'erl_override_unique'
            );
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_recurring_leave_overrides');
    }
};
