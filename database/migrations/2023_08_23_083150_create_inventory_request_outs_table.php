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
        Schema::create('inventory_request_outs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('responsible_id')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('requester_id')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->text('reason')->nullable();
            $table->unsignedInteger('quantity')->default(1);

            $table->string('status')->default('Unpublished');

            $table->string('add_by')->nullable();
            $table->dateTime('add_date')->nullable();

            $table->string('edit_by')->nullable();
            $table->dateTime('edit_date')->nullable();

            $table->string('delete_by')->nullable();
            $table->dateTime('delete_date')->nullable();

            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index(['requester_id', 'responsible_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_request_outs');
    }
};
