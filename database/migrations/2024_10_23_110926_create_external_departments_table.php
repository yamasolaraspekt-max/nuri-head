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
        Schema::create('external_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id');
            $table->string('department');
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('office')->nullable();
            $table->string('home')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->foreign('external_id')->references('id')->on('external_personals')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_departments');
    }
};
