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
        Schema::create('daily_report_work_places', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('place_name')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('address')->nullable();
            $table->decimal('lat', 9, 6)->nullable(); 
            $table->decimal('lon', 9, 6)->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branch_addresses')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_work_places');
    }
};
