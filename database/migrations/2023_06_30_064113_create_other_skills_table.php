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
        Schema::create('other_skills', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_id');
            $table->string('skills'); 
            $table->string('proficiency'); 
            $table->string('year_experience'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_skills');
    }
};
