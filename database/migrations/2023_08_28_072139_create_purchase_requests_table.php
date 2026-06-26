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
       Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand')
                ->nullable()
                ->constrained('brands')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('new_brand')->nullable();

            $table->foreignId('distributor_id')
                ->nullable()
                ->constrained('distributors')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('new_distributor')->nullable();

            $table->string('product');
            $table->string('model')->nullable();
            $table->string('color')->nullable();

            $table->foreignId('request_from')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('request_to')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('measure_unit')->nullable();
            $table->string('price_unit')->nullable();

            $table->decimal('retail_price', 12, 2)->nullable();
            $table->string('retail_discount_type')->nullable();
            $table->decimal('retail_discount', 12, 2)->nullable();

            $table->decimal('purchase_price', 12, 2)->nullable();

            $table->longText('short_description')->nullable();
            $table->string('used')->nullable();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->unsignedBigInteger('problem_id')->nullable();

            $table->string('link')->nullable();
            $table->string('image')->nullable();

            $table->decimal('quantity', 12, 2)->nullable();

            $table->string('status')->default('Unpublished');

            $table->string('add_by')->nullable();
            $table->dateTime('add_date')->nullable();

            $table->string('edit_by')->nullable();
            $table->dateTime('edit_date')->nullable();

            $table->string('delete_by')->nullable();
            $table->dateTime('delete_date')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['brand', 'distributor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
