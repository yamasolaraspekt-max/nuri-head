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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->string('serial_no')->nullable();
            $table->string('article_no')->nullable();
            $table->string('item');
            $table->string('model');
            $table->string('category');

            // Self-reference to parent asset
            $table->foreignId('parent_id')->nullable()
                ->constrained('assets')->nullOnDelete();

            // 💰 money should be decimal, not integer/double
            $table->decimal('purchase_price', 12, 2)->nullable();

            $table->string('purchase_type');

            $table->string('leasing_from')->nullable();
            $table->date('leasing_date')->nullable();
            $table->date('leasing_end_date')->nullable();
            $table->decimal('leasing_price', 12, 2)->nullable(); // fixed (was double 10.2)

            $table->date('purchase_date')->nullable();
            $table->date('expire_date')->nullable();

            $table->string('location')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();

            $table->integer('quantity');
            $table->string('status');
            $table->string('pdf')->nullable();

            // FKs (keep your existing names)
            $table->foreignId('handover_id')->nullable()
                ->constrained('employees')->cascadeOnDelete();

            $table->foreignId('branch_id')->nullable()
                ->constrained('branches')->cascadeOnDelete();

            $table->unsignedBigInteger('used_for')->nullable();

            $table->timestamps();

            // FK for used_for -> article_groups.id
            $table->foreign('used_for')
                ->references('id')->on('article_groups')
                ->cascadeOnDelete();

            // Helpful indexes
            $table->unique('serial_no');        // if serials must be unique; drop if not guaranteed
            $table->index(['category']);
            $table->index(['purchase_type']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
