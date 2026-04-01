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
        Schema::create('assets_handover', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('assets_id')->unsigned();
            $table->foreign('assets_id')->references('id')->on('assets')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('handover_id')->unsigned();
            $table->foreign('handover_id')->references('id')->on('handovers')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets_handover');
    }
};
