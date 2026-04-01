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
        Schema::create('customer_offer_w_p_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->string('type_of_building')->nullable();
            $table->string('heat_generator')->default('no');
            $table->string('technologies')->nullable();
            $table->integer('min_coverage_ratio')->default(85); 
            $table->integer('room_temperature')->nullable();
            $table->integer('outdoor_temperature')->nullable();
            $table->integer('heating_limit_temperature');
            $table->boolean('has_blocking_time')->default(false);
            $table->integer('blocking_time_duration')->nullable();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_offer_w_p_s');
    }
};
