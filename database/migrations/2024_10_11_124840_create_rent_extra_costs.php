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
     Schema::create('rent_extra_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_rent_infos_id');
            $table->string('title');
            $table->double('cost'); 
            $table->string('paid_to')->nullable(); 
            $table->double('company')->nullable(); 
            $table->string('status')->default('Published');
            $table->foreign('branch_rent_infos_id')->references('id')->on('branch_rent_infos')->onDelete('cascade'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_extra_costs');
    }
};
