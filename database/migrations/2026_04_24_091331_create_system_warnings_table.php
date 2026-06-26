<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_warnings', function (Blueprint $table) {
            $table->id();

            $table->boolean('is_active')->default(false)->index();

            $table->string('type')->default('development');
            // development, uploading, fixing, maintenance

            $table->string('title')->default('Wichtiger Hinweis');
            $table->text('message')->nullable();

            $table->string('button_text')->default('Verstanden');
            $table->string('theme')->default('amber');
            // amber, blue, red, green, purple

            $table->boolean('can_close')->default(true);
            $table->boolean('show_backdrop')->default(true);

            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_warnings');
    }
};