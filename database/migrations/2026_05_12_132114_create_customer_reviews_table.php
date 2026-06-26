<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('new_leads')
                ->cascadeOnDelete();

            $table->foreignId('alternative_id')
                ->nullable()
                ->constrained('lead_alternative_adds')
                ->nullOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('article_groups')
                ->nullOnDelete();

            $table->unsignedBigInteger('employee_id')->nullable();

            $table->unsignedTinyInteger('stars')->default(0);

            $table->string('behavior')->nullable();

            $table->text('caution_note')->nullable();

            $table->text('internet_feedback')->nullable();

            $table->text('internal_note')->nullable();

            $table->string('source')->nullable();

            $table->boolean('is_critical')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'alternative_id', 'product_id']);
            $table->index('employee_id');
            $table->index('stars');
            $table->index('is_critical');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_reviews');
    }
};