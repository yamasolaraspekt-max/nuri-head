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
        Schema::create('branch_rents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_details_id'); 
            $table->string('object_name');
            $table->double('rent_cost', 10.2)->nullable();
            $table->double('extra_cost', 10.2)->nullable();
            $table->double('total', 10.2)->nullable();
            $table->string('street')->nullable();
            $table->string('house_no');
            $table->string('postcode');
            $table->string('city');
            $table->timestamps();

            $table->foreign('expense_details_id')->references('id')->on('branch_expenses')->onDelete('cascade');  

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_rents');
    }
};
