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
        Schema::create('note_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->string('type')->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->string('status')->default('published');
            $table->unsignedBigInteger('user'); 
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user')->references('id')->on('employees')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_categories');
    }
};
