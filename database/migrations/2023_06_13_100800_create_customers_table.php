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
      Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_type')->nullable();
            $table->string('customer_no')->nullable();
            $table->string('title')->nullable();
            $table->string('firma')->nullable();
            $table->string('name');
            $table->string('lastname');
            $table->string('street');
            $table->string('postcode');
            $table->string('city');
            $table->string('phone')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email');
            $table->unsignedBigInteger('contact_person')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('number_people')->nullable();
            $table->integer('building_type')->nullable();
            $table->integer('living_space')->nullable();
            $table->integer('construction_year')->nullable();
            $table->integer('heating_type')->nullable();
            $table->integer('consumption')->nullable();
            $table->integer('underfloor_heating')->nullable();
            $table->integer('radiator')->nullable();
            $table->integer('heating_manufacture_year')->nullable();
            $table->double('heating_load', 10, 2)->nullable();
            $table->double('efficiency', 10, 2)->nullable();
            $table->double('heating_output', 10, 2)->nullable();
            $table->decimal('lat', 35, 30)->nullable();
            $table->decimal('lon', 35, 30)->nullable();
            $table->double('polygon_height', 10, 2)->nullable();
            $table->double('polygon_width', 10, 2)->nullable();
            $table->double('polygon_area', 10, 2)->nullable();
            $table->double('elevation', 10, 2)->nullable();
            $table->string('alternative_address')->nullable();
            $table->date('request_date')->nullable();
            $table->string('document')->nullable();
            $table->date('date')->nullable();
            $table->string('consultation')->nullable();
            $table->string('source')->nullable();
            $table->string('source_info')->nullable();
            $table->integer('interest_rating')->nullable();
            $table->integer('seriousness_rating')->nullable();
            $table->integer('price_information_rating')->nullable();
            $table->string('periority')->nullable();
            $table->longText('note')->nullable();
            $table->string('initial_consultation')->nullable();
            $table->string('status')->default('open');
            $table->string('customer')->default('lead');
            $table->integer('annual_consumption')->nullable();
            $table->string('roof_type')->nullable();
            $table->integer('roof_age')->nullable();
            $table->integer('house_year')->nullable();
            $table->integer('heating_system_age')->nullable();
            $table->integer('heating_system_year')->nullable();
            $table->string('heating_system_type')->nullable();
            $table->integer('annual_heating_energy_consumption')->nullable();
            $table->string('electric_car')->nullable();
            $table->string('electric_car_plan')->nullable();
            $table->integer('total_number')->nullable();
            $table->integer('answered_number')->nullable();
            $table->string('inquiry_screenshot')->nullable();
             $table->text('info')->nullable();
            $table->date('appointment')->nullable();
            $table->string('appointment_by')->nullable();
            $table->string('objective')->nullable();
            $table->integer('unusable_space')->nullable();
            $table->integer('number_we')->nullable();
            $table->integer('number_stories')->nullable();
            $table->string('installation_location')->nullable();
            $table->string('installation_location_extra')->nullable();
            $table->string('tile_name')->nullable();
            $table->integer('annual_heating_energy_consumption_kwh')->nullable();
            $table->timestamps();
            $table->foreign('contact_person')->references('id')->on('employees')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
