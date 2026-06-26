<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();

            // Stable backend key, for example: clock, hr, focus, personalChart
            $table->string('key')->unique();

            // Display title
            $table->string('title');

            // Optional subtitle
            $table->string('subtitle')->nullable();

            // Icon name for lucide
            $table->string('icon')->nullable();

            // blue, green, warning, danger
            $table->string('color')->default('blue');

            // personal, department, company, global
            $table->string('default_view')->default('personal');

            // tags used by filter/search
            $table->json('tags')->nullable();

            // default grid size
            $table->unsignedTinyInteger('default_col_span')->default(4);
            $table->unsignedTinyInteger('default_row_span')->default(4);

            // Blade partial/component name if later needed
            $table->string('component')->nullable();

            // allow multiple instances? notes/empty can be true
            $table->boolean('allow_multiple')->default(false);

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
    }
};