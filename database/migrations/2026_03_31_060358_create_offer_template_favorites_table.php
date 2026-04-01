<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('offer_template_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_template_id')->constrained('offer_templates')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->timestamps();
            
            // Prevent duplicate favorites for the same user and template
            $table->unique(['offer_template_id', 'employee_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('offer_template_favorites');
    }
};