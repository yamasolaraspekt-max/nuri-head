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
        Schema::create('wp_fusion_forms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('form_id');
            $table->bigInteger('views')->nullable()->default(0);
            $table->bigInteger('submissions_count')->nullable()->default(0);
            $table->longText('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wp_fusion_forms');
    }
};
