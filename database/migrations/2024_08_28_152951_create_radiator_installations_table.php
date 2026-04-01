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
        Schema::create('radiator_installations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id'); 
            $table->string('postcode')->nullable();
            $table->string('number')->nullable();
            $table->string('type')->nullable();
            $table->string('radiator_type')->nullable();
            $table->string('floor')->nullable();
            $table->string('room')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('depth')->nullable();
            $table->integer('niche_top')->nullable();
            $table->integer('niche_bottom')->nullable();
            $table->integer('niche_left')->nullable();
            $table->integer('niche_right')->nullable();
            $table->string('has_window_sill')->nullable();
            $table->string('supply_valve')->nullable();
            $table->string('supply_valve_presettable')->nullable();
            $table->string('return_valve')->nullable();
            $table->string('return_valve_present')->nullable();
            $table->string('design')->nullable();
            $table->string('renew_thermostat_head')->nullable();
            $table->string('has_socket')->nullable();
            $table->integer('socket_distance')->nullable();
            $table->integer('size')->nullable();
            $table->string('limbs')->nullable();
            $table->string('image')->nullable(); 
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiator_installations');
    }
};
