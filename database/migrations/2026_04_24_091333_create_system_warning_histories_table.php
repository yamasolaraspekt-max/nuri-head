<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_warning_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('system_warning_id')
                ->nullable()
                ->constrained('system_warnings')
                ->nullOnDelete();

            $table->string('action');
            // enabled, disabled, updated

            $table->string('type')->nullable();
            $table->string('title')->nullable();
            $table->text('message')->nullable();

            $table->boolean('is_active')->default(false);

            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_warning_histories');
    }
};