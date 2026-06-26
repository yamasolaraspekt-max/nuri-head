<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_dashboard_widgets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Your employee id mapping
            $table->unsignedBigInteger('employee_id')->nullable()->index();

            $table->foreignId('dashboard_widget_id')
                ->constrained('dashboard_widgets')
                ->cascadeOnDelete();

            // Unique instance key, for example:
            // clock, hr, focus, notes_1710000000000
            $table->string('instance_key');

            // personal, department, company
            $table->string('view')->default('personal')->index();

            $table->boolean('is_visible')->default(true);

            // frontend order
            $table->unsignedInteger('sort_order')->default(0);

            // size
            $table->unsignedTinyInteger('col_span')->default(4);
            $table->unsignedTinyInteger('row_span')->default(4);

            // extra data, for example widget custom config
            $table->json('config')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'instance_key']);
            $table->index(['user_id', 'view', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_dashboard_widgets');
    }
};