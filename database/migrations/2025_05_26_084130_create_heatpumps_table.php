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
        Schema::create('heat_pumps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('product_id');
            $table->string('type');
            $table->decimal('cop', 5, 2);
            $table->decimal('installation_cost', 10, 2);
            $table->decimal('annual_costs', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heatpumps');
    }
};
