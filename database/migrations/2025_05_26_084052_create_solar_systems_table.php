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
        Schema::create('solar_systems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('kwp_size', 5, 2);
            $table->decimal('self_consumption_rate', 5, 2);
            $table->decimal('feed_in_tariff', 5, 2);
            $table->decimal('system_price', 10, 2);
            $table->decimal('battery_capacity', 5, 2)->nullable();
            $table->decimal('battery_price', 10, 2)->nullable();
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
        Schema::dropIfExists('solar_systems');
    }
};
