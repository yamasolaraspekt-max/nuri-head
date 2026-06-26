<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overdue_report_reads', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('report_id')->index();
            $table->unsignedBigInteger('employee_id')->index();

            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->unique(['report_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overdue_report_reads');
    }
};