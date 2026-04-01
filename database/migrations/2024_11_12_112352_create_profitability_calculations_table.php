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
        Schema::create('profitability_calculations', function (Blueprint $table) {
            $table->id();
        
            // 🔗 Foreign Keys
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('employee_id');
        
            // 📄 Basic Info
            $table->string('title');
            $table->string('status')->default('draft');
        
            // 🧮 Current Costs (ALT)
            $table->decimal('current_electricity_cost', 10, 2)->nullable();
            $table->decimal('current_heating_cost', 10, 2)->nullable();
            $table->decimal('current_fuel_cost', 10, 2)->nullable();
            $table->decimal('current_total_yearly_cost', 10, 2)->nullable();
            $table->decimal('current_total_25y_cost', 10, 2)->nullable();
        
            // 🚀 Future Costs (NEU)
            $table->decimal('future_electricity_cost', 10, 2)->nullable();
            $table->decimal('future_heating_cost', 10, 2)->nullable();
            $table->decimal('future_ev_cost', 10, 2)->nullable();
            $table->decimal('future_total_yearly_cost', 10, 2)->nullable();
            $table->decimal('future_total_25y_cost', 10, 2)->nullable();
        
            // 💰 Savings
            $table->decimal('savings_per_year', 10, 2)->nullable();
            $table->decimal('savings_over_25_years', 10, 2)->nullable();
        
            // 📈 Investment + Amortisation
            $table->decimal('investment_cost', 10, 2)->nullable();
            $table->decimal('amortisation_years', 5, 2)->nullable();
            $table->decimal('roi_percent', 5, 2)->nullable();
        
            // 📊 Emissions
            $table->decimal('co2_emission_before', 10, 2)->nullable(); // in kg
            $table->decimal('co2_emission_after', 10, 2)->nullable();  // in kg
            $table->integer('co2_saved_trees_equiv')->nullable();      // 15 kg CO₂ / Baum / Jahr
        
            // 🧾 Notes and assumptions
            $table->text('notes')->nullable();
            $table->string('electricity_price_note')->nullable(); // e.g. \"EVU Preis 0,30€/kWh\"
        
            $table->timestamps();
        
            // 🔐 Foreign keys
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('phase_sections')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profitability_calculations');
    }
};
