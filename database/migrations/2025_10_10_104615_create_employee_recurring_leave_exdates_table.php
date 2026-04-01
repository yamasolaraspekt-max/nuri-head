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
            Schema::create('employee_recurring_leave_exdates', function (Blueprint $table) {
               $table->bigIncrements('id');
                $table->unsignedBigInteger('leave_id');
                $table->date('date'); // original occurrence date
                $table->unique(['leave_id','date'], 'uq_exdate_leave_date');

                $table->foreign('leave_id', 'fk_exdate_leave')
                    ->references('id')->on('employee_recurring_leaves')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_recurring_leave_exdates');
    }
};
