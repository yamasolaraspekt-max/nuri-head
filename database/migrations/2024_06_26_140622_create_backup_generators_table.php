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
        Schema::create('backup_generators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('article_group_id')->nullable(); 
            $table->string('company')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('available')->nullable();
            $table->string('version')->nullable(); 
            $table->string('user_id')->nullable();
            $table->integer('ac_nominal_voltage')->nullable();
            $table->integer('ac_nominal_current')->nullable();
            $table->float('ac_nominal_power')->nullable();
            $table->float('max_ac_power')->nullable();
            $table->integer('num_phases')->nullable();
            $table->float('load_0')->nullable();
            $table->float('load_25')->nullable();
            $table->float('load_50')->nullable();
            $table->float('load_75')->nullable();
            $table->float('load_100')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('article_group_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_generators');
    }
};
