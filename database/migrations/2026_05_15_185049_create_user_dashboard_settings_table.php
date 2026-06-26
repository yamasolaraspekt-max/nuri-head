<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_dashboard_settings', function (Blueprint $table) {
            $table->id();

            // Normal Laravel user id
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Your app often stores employee id in auth()->user()->name
            $table->unsignedBigInteger('employee_id')->nullable()->index();

            $table->string('active_view')->default('personal');
            $table->string('theme')->default('default'); // default, soft, contrast
            $table->string('density')->default('normal'); // compact, normal, comfortable

            $table->json('settings')->nullable();

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_dashboard_settings');
    }
};