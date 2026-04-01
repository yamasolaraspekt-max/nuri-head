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
        Schema::create('branch_rent_infos', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
        
            // Foreign key referencing the 'id' column of the 'expense_types' table
            $table->unsignedBigInteger('expense_details_id');
            $table->foreign('expense_details_id')->references('id')->on('branch_expenses')->onDelete('cascade');
        
            // Foreign key referencing the 'id' column of the 'branch_rents' table 
            $table->unsignedBigInteger('object_id');
            $table->foreign('object_id')->references('id')->on('branch_rents')->onDelete('cascade');
        
            $table->unsignedBigInteger('apartment_id');
            $table->foreign('apartment_id')->references('id')->on('rent_properties')->onDelete('cascade');
        
            // Double columns for costs and fines
            $table->double('cold_rent', 10, 2); 
            $table->double('electricity_cost', 10, 2)->nullable();
            $table->double('heating_cost', 10, 2)->nullable();
            $table->double('repair_cost', 10, 2)->nullable(); 
            $table->double('extra_cost', 10, 2)->nullable(); 
            $table->double('total', 10, 2)->nullable(); 
            // Date column for payment date
            $table->date('payment_date'); 
            // Integer column for payee (could be user ID or another identifier)
            $table->unsignedBigInteger('payee'); 
            $table->foreign('payee')->references('id')->on('employees')->onDelete('cascade');

            // String column for status with default value 'Published'
            $table->string('status')->default('Published'); 
            $table->timestamps(); // Automatically managed created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_rent_infos');
    }
};
