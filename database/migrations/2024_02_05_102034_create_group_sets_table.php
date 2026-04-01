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
        Schema::create('group_sets', function (Blueprint $table) {
            $table->id();
            $table->string('group_set');
            $table->longText('content');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('set_id')->nullable();
            $table->double('material_price', 10.2)->nullable();
            $table->double('material_percent', 10.2)->nullable();
            $table->double('employee_price', 10.2)->nullable();
            $table->double('employee_percent', 10.2)->nullable();
            $table->double('total', 10.2)->nullable();
            $table->string('status')->nullable();
            $table->foreign('set_id')->references('id')->on('product_master_sets')->onDelete('cascade'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_sets');
    }
};
