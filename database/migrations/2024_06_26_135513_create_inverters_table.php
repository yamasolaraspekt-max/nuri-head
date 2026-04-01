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
        Schema::create('inverters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('article_group_id')->nullable(); 
            $table->string('company')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('available')->nullable();
            $table->string('version')->nullable(); 
            $table->string('user_id')->nullable();
            $table->float('dc_nominal_power')->nullable();
            $table->float('max_dc_power')->nullable();
            $table->integer('dc_nominal_voltage')->nullable();
            $table->integer('max_input_voltage')->nullable();
            $table->integer('max_input_current')->nullable();
            $table->integer('max_dc_short_circuit_current')->nullable();
            $table->integer('num_dc_inputs')->nullable();
            $table->float('ac_nominal_power')->nullable();
            $table->float('max_ac_power')->nullable();
            $table->integer('ac_nominal_voltage')->nullable();
            $table->integer('num_phases')->nullable();
            $table->string('with_transformer')->nullable();
            $table->float('efficiency_change')->nullable();
            $table->integer('min_feed_power')->nullable();
            $table->float('standby_consumption')->nullable();
            $table->float('night_consumption')->nullable();
            $table->float('power_range_below_20')->nullable();
            $table->float('power_range_above_20')->nullable();
            $table->string('parallel_operation')->nullable();
            $table->integer('num_mpp_trackers')->nullable();
            $table->string('identical_properties')->nullable();
            $table->integer('max_input_current_per_mpp')->nullable();
            $table->integer('max_short_circuit_current_per_mpp')->nullable();
            $table->float('max_input_power_per_mpp')->nullable();
            $table->integer('min_mpp_voltage')->nullable();
            $table->integer('max_mpp_voltage')->nullable();
            $table->float('efficiency_0')->nullable();
            $table->float('efficiency_5')->nullable();
            $table->float('efficiency_10')->nullable();
            $table->float('efficiency_20')->nullable();
            $table->float('efficiency_30')->nullable();
            $table->float('efficiency_50')->nullable();
            $table->float('efficiency_75')->nullable();
            $table->float('efficiency_100')->nullable();
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
        Schema::dropIfExists('inverters');
    }
};
