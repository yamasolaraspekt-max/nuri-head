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
        Schema::create('machine_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machine_id');
            $table->foreign('machine_id')->references('id')->on('machines')->onDelete('cascade');
            $table->string('service_type');
            $table->date('service_date');
            $table->string('service_by')->nullable();
            $table->double('price', 10, 2)->nullable();
            $table->string('service_station')->nullable(); 
            $table->string('technician')->nullable();
            $table->string('location');
            $table->string('email')->nullable();
            $table->string('phone')->nullable(); 
            $table->string('service_report')->nullable(); 
            $table->unsignedInteger('maintenance_interval')->nullable();  
            $table->longText('fault_description')->nullable();
            $table->longText('repair_description')->nullable();
            $table->dateTime('fault_detected_at')->nullable(); 
            $table->string('fault_detected_by')->nullable(); 
            $table->string('fault_detected_location')->nullable();
            $table->string('status')->default('pending'); 
            $table->string('paid_by');
            $table->date('deleted_at')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_services');
    }
};
