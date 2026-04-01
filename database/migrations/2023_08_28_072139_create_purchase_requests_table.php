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
            $table->integer('brand')->nullabl();
            $table->string('new_brand')->nullable();
            $table->integer('distributor_id')->nullable();
            $table->string('new_distributor')->nullable();
            $table->string('product');
            $table->string('model')->nullable();
            $table->string('color')->nullable();
            $table->integer('request_from')->nullable();
            $table->integer('request_to')->nullable();

            $table->string('measure_unit')->nullable();
            $table->string('price_unit')->nullable();
            $table->float('retail_price',10,1)->nullable();
            $table->string('retail_discount_type')->nullable();
            $table->integer('retail_discount')->nullable();
            $table->float('purchase_price', 10,1)->nullable();
            $table->longText('short_description')->nullable();
            $table->string('used')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('employee_id')->nullable();
            $table->integer('problem_id')->nullable();
            $table->string('link')->nullable();
            $table->string('image')->nullable();
            $table->string('quantity')->nullable();
            $table->string('status')->nullable();
            $table->string('add_by')->nullable();
            $table->date('add_date')->nullable();
            $table->string('edit_by')->nullable();
            $table->date('edit_date')->nullable();
            $table->string('delete_by')->nullable();
            $table->date('delete_date')->nullable();
            $table->timestamps();
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
