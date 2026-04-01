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
        Schema::create('branch_contract_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rent_properties_id');
            $table->foreign('rent_properties_id')->references('id')->on('rent_properties')->onDelete('cascade');
            $table->string('position');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('home');
            $table->string('office');
            $table->string('address');
            $table->string('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_contract_details');
    }
};
