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
        Schema::create('distributor_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('d_id');
            $table->string('d_department')->nullable();
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('office')->nullable();
            $table->string('home')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('Unpublished');
            $table->timestamps();

            $table->foreign('d_id')->references('id')->on('distributors')->onDelete('cascade'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_departments');
    }
};
