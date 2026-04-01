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
        Schema::create('batteries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('article_group_id');
            $table->string('company')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('available')->nullable();
            $table->string('version')->nullable(); 
            $table->string('user_id')->nullable();
            $table->string('battery_type')->nullable();
            $table->float('cell_voltage')->nullable();
            $table->integer('num_cells')->nullable();
            $table->float('nominal_voltage')->nullable();
            $table->integer('num_strings')->nullable();
            $table->float('internal_resistance')->nullable();
            $table->float('self_discharge')->nullable();
            $table->integer('dod_20')->nullable();
            $table->integer('dod_40')->nullable();
            $table->integer('dod_60')->nullable();
            $table->integer('dod_80')->nullable();
            $table->float('capacity_10min')->nullable();
            $table->float('capacity_30min')->nullable();
            $table->float('capacity_1h')->nullable();
            $table->float('capacity_5h')->nullable();
            $table->float('capacity_10h')->nullable();
            $table->float('capacity_100h')->nullable();
            $table->float('length')->nullable();
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            
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
        Schema::dropIfExists('batteries');
    }
};
