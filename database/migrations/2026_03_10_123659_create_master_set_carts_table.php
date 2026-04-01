<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_set_carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->foreignId('article_group_id')
                ->nullable()
                ->constrained('article_groups')
                ->nullOnDelete();

            $table->foreignId('target_master_set_id')
                ->nullable()
                ->constrained('master_sets')
                ->nullOnDelete();

            $table->string('mode', 20)->default('new')->index();
            // new | existing

            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->string('status', 20)->default('draft')->index();
            // draft | converted | archived

            $table->decimal('main_total', 12, 2)->default(0);
            $table->decimal('sub_total', 12, 2)->default(0);
            $table->decimal('labor_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->boolean('is_locked')->default(false);
            $table->timestamp('converted_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['article_group_id', 'mode']);
            $table->index(['creator_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_set_carts');
    }
};