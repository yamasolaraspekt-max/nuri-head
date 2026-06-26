<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_dashboard_shortcuts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('employee_id')->nullable()->index();

            $table->string('label');
            $table->string('subtitle')->nullable();
            $table->string('icon')->default('zap');
            $table->string('url')->nullable();

            // Example: Inquiry, Customer, Problem, Employee
            $table->string('permission_key')->nullable();

            // is_read, is_add, is_update, is_delete
            $table->string('permission_action')->default('is_read');

            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'is_visible', 'sort_order'], 'uds_user_visible_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_dashboard_shortcuts');
    }
};