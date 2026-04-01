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
        Schema::create('brand_departments', function (Blueprint $table) {
            $table->id();
            $table->integer('brand_id');
            $table->string('brand_department')->nullable();
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('office')->nullable();
            $table->string('home')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('Unpublished');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_departments');
    }
};
