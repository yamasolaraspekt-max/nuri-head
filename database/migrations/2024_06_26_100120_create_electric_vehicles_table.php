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
        Schema::create('electric_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('article_group_id');
            $table->string('company')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('available')->nullable();
            $table->string('version')->nullable(); 
            $table->string('user_id')->nullable();
            $table->integer('range_wltp')->nullable();
            $table->float('consumption')->nullable();
            $table->float('battery_capacity')->nullable();
            $table->float('discharge_power')->nullable();
            $table->float('motor_power')->nullable();
            $table->integer('empty_weight')->nullable();
            $table->integer('max_speed')->nullable();
            $table->integer('payload')->nullable();
            $table->integer('seats')->nullable();
            $table->string('charging_technology')->nullable();
            $table->float('charging_power')->nullable();
            $table->string('discharge_for_consumption')->nullable();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('article_group_id')->references('id')->on('article_groups')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electric_vehicles');
    }
};
