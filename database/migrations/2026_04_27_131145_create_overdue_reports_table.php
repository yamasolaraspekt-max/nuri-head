<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overdue_reports', function (Blueprint $table) {
            $table->id();

            $table->string('type', 80)->index();
            $table->unsignedBigInteger('target_id')->index();

            $table->longText('report');

            // auth()->user()->name stores employees.id in your app
            $table->unsignedBigInteger('employee_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'target_id']);
            $table->index(['employee_id', 'created_at']);

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overdue_reports');
    }
};