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
        Schema::create('branch_insurances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_expenses_id');
            $table->string('insurance_for');
            $table->string('policy_number');
            $table->string('provider');
            $table->double('coverage_amount', 10.2);
            $table->double('monthly_payable', 10.2);
            $table->date('start_date');
            $table->date('end_date');
            $table->foreign('branch_expenses_id')->references('id')->on('branch_expenses')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_insurances');
    }
};
