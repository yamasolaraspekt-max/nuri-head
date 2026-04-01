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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('name');
            $table->string('midname')->nullable();
            $table->string('lastname'); 
            $table->string('branch')->nullable();
            $table->date('dob')->nullable(); 
            $table->string('gender')->nullable();
            $table->longText('bio')->nullable();
            $table->double('salary_per_hour', 10, 2)->nullable();
            $table->string('marital_status')->nullable();
            $table->string('kids')->nullable();
            $table->string('color')->nullable();
            $table->string('nationality')->nullable();
            $table->string('country_id')->nullable();
            $table->integer('address_id')->nullable();
            $table->string('cv')->nullable();
            $table->integer('qualification_id')->nullable();
            $table->integer('f_edu_id')->nullable();
            $table->date('contract_date')->nullable();
            $table->string('contract_type_id')->nullable();
            $table->integer('skill_id')->nullable();
            $table->integer('other_skill_id')->nullable();
            $table->integer('reference_id')->nullable();
            $table->integer('appreciation_id')->nullable();
            $table->integer('warning_id')->nullable();
            $table->integer('language_id')->nullable();
            $table->integer('mother_tongue')->nullable();
            $table->integer('supervisor')->nullable();
            $table->integer('working_hour')->nullable();
            $table->string('working_type')->nullable(); // Fixed here
            $table->time('daily_start_time')->default('07:30:00');  
            $table->time('daily_end_time')->default('16:00:00');  
            $table->integer('remaining_day')->nullable();
            $table->integer('sick_leave')->nullable();
            $table->integer('sick_leave_remaining')->nullable();
            $table->integer('leave')->nullable();
            $table->integer('emergency_contact_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('image')->nullable();
            $table->string('insurance_id')->nullable();
            $table->string('health_insurance')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('iban')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('tax_class')->nullable();
            $table->string('pension_no')->nullable();
            $table->string('religion')->nullable();
            $table->string('resident_permit')->nullable();
            $table->date('resident_permit_end_date')->nullable();
            $table->string('work_permit')->nullable();
            $table->date('work_permit_end_date')->nullable();
            $table->date('termination')->nullable();
            $table->date('resignation')->nullable();
            $table->string('trainee')->nullable();
            $table->string('trainee_start_date')->nullable();
            $table->string('trainee_end_date')->nullable(); 
            $table->string('status')->nullable();
            $table->string('status_msg')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Adds deleted_at column for soft deletes
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
