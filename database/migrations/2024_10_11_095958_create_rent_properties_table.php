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
        Schema::create('rent_properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('object_id');
            $table->string('owner'); 
            $table->integer('living_space')->nullable();
            $table->integer('parking')->nullable();
            $table->decimal('parking_cost', 10,2)->nullable();
            $table->integer('parking_count')->nullable(); 
            $table->string('contract_type')->nullable(); 
            $table->date('contract_date');
            $table->date('termination_date')->nullable(); 
            $table->string('termination_type')->nullable();
            $table->double('cold_rent', 10.2)->nullable(); 
            $table->double('extra_cost', 10.2)->nullable(); 
            $table->double('advance_rent', 10.2)->nullable();
            $table->string('bank_user')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('iban')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->foreign('object_id')->references('id')->on('branch_rents')->onDelete('cascade'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_properties');
    }
};
