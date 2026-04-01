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
        Schema::create('p_v_long_roofs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('roof_id')->nullable();
            $table->string('roof_dimensions')->nullable();
            $table->string('rafter_left_overhang')->nullable();
            $table->string('roof_width')->nullable();
            $table->string('roof_height')->nullable();
            $table->string('rafter_right_overhang')->nullable();
            $table->string('rafter_thickness')->nullable();
            $table->string('rafter_reinforcement_needed')->nullable();
            $table->string('statics_available')->nullable();
            $table->string('conduit_available')->nullable();
            $table->string('cable_routing_through' )->nullable();
            $table->string('lightning_protection' )->nullable();
            $table->string('dachsanierung')->nullable();
            $table->date('geplante_termin')->nullable();
            $table->string('dachdecker')->nullable();
            $table->string('dauer')->nullable();
            $table->string('ort')->nullable();
            $table->string('solarhalteziegel')->nullable();
            $table->string('ansprechpartner')->nullable();
            $table->string('geliefert_durch')->nullable();
            $table->string('geruestnutzung')->nullable();
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('roof_id')->references('id')->on('p_v_roofs')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_v_long_roofs');
    }
};
