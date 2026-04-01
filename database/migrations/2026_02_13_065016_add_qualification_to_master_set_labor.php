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
        Schema::table('master_set_labor', function (Blueprint $table) {
            $table->unsignedBigInteger('qualification_id')->nullable()->after('master_set_id');
            
            // Make sure these are nullable if they aren't already
            $table->unsignedBigInteger('department_id')->nullable()->change();
            $table->unsignedBigInteger('position_id')->nullable()->change();
            $table->unsignedBigInteger('employee_id')->nullable()->change();

            $table->foreign('qualification_id')
                ->references('id')->on('position_qualifications')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_set_labor', function (Blueprint $table) {
            $table->dropColumn([
                'qualification_id',
                'department_id',
                'position_id',
                'employee_id', 
                 
            ]);
        });
    }
};
