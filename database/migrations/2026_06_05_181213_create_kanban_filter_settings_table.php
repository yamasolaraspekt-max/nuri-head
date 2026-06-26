<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kanban_filter_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('employee_id')->nullable()->index();
            $table->string('name', 120)->default('Mein Kanban Filter');
            $table->json('filters');
            $table->boolean('is_default')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_default']);
            $table->unique(['user_id', 'name'], 'kanban_filter_user_name_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanban_filter_settings');
    }
};
