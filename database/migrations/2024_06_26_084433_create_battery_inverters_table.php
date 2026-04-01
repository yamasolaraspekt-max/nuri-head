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
        Schema::create('battery_inverters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('article_group_id'); 
            $table->string('company')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('available')->nullable();
            $table->string('version')->nullable(); 
            $table->string('user_id')->nullable();
            $table->integer('nominal_voltage')->nullable();
            $table->integer('max_ac_current')->nullable();
            $table->integer('continuous_power')->nullable();
            $table->integer('power_30min')->nullable();
            $table->integer('power_60min')->nullable();
            $table->integer('no_load_consumption')->nullable();
            $table->integer('standby_consumption')->nullable();
            $table->integer('battery_voltage')->nullable();
            $table->integer('min_battery_voltage')->nullable();
            $table->integer('max_battery_voltage')->nullable();
            $table->integer('max_battery_charge_current')->nullable();
            $table->float('efficiency_0')->nullable();
            $table->float('efficiency_5')->nullable();
            $table->float('efficiency_10')->nullable();
            $table->float('efficiency_20')->nullable();
            $table->float('efficiency_30')->nullable();
            $table->float('efficiency_50')->nullable();
            $table->float('efficiency_75')->nullable();
            $table->float('efficiency_100')->nullable();
            $table->integer('max_devices_per_phase_single')->nullable();
            $table->integer('max_devices_per_phase_dual')->nullable();
            $table->integer('max_clusters')->nullable();
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
        Schema::dropIfExists('battery_inverters');
    }
};
