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
        Schema::create('wp_fusion_form_fields', function (Blueprint $table) {
            $table->id();
             $table->bigInteger('form_id');
            $table->string('field_name', 256);
            $table->string('field_label', 256)->nullable();
            $table->longText('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wp_fusion_form_fields');
    }
};
