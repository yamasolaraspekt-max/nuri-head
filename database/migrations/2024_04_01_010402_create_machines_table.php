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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->integer('year');
            $table->string('color');
            $table->string('engine_type')->nullable();
            $table->float('mileage')->nullable();
            $table->string('purchase_type')->nullable();
            $table->integer('article_group')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('leasing_from')->nullable();
            $table->date('leasing_start_date')->nullable();
            $table->date('leasing_end_date')->nullable();
            $table->decimal('leasing_price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->date('last_service_date')->nullable();
            $table->boolean('technical_inspection')->nullable();
            $table->date('technical_inspection_date')->nullable();
            $table->integer('branch_id');
            $table->string('owner_name')->nullable();
            $table->string('owner_contact')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
