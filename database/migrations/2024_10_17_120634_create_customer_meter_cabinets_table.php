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
        Schema::create('customer_meter_cabinets', function (Blueprint $table) {
            $table->id();
            $table->string('meter_cabinet'); // OK, upgrade, new
            $table->integer('cabinet_size'); // 550, 800, 1100
            $table->unsignedBigInteger('meter_cabinet_company'); // Reference to the manufacturer (electro)
            $table->unsignedBigInteger('customer_id');   
            $table->integer('postcode');   

            // Component checkboxes 
            $table->string('wp_meter_adapter_plate')->default(false);
            $table->string('wp_ac_surge_protection')->default(false);
            $table->string('wp_ac_switch')->default(false);
            $table->string('wp_apz_field')->default(false);
            $table->string('wp_disconnect_relay')->default(false);
            $table->string('wp_equipotential_bonding')->default(false);

            $table->timestamps();

            // Foreign key to the manufacturer (electro table)
            $table->foreign('meter_cabinet_company')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); 
 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_meter_cabinets');
    }
};
