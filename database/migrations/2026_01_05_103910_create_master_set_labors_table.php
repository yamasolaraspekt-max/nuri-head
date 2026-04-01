<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_set_labor', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('master_set_id');

            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();

            // snapshot hourly rate at save time
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('hours', 10, 2)->default(1);

            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->foreign('master_set_id')
                ->references('id')->on('master_sets')
                ->onDelete('cascade');

            $table->foreign('department_id')
                ->references('id')->on('departments')
                ->nullOnDelete();

            $table->foreign('position_id')
                ->references('id')->on('positions')
                ->nullOnDelete();

            $table->foreign('employee_id')
                ->references('id')->on('employees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_set_labor');
    }
};
