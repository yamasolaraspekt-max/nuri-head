<?php

 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Attendance Table
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->timestamps();
        });

        // 2. Travel Logs are stored inside 'planner_items' -> 'meta' -> 'events' 
        // based on your previous code, so no new table is strictly needed for travel
        // if you stick to the JSON meta approach.
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};