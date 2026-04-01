<?php

// database/migrations/2026_03_05_000001_create_costing_sets_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('costing_sets', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // e.g. "Standard 2026"
            $table->string('status')->default('active'); // active|archived

            // AW config
            $table->unsignedInteger('aw_minutes')->default(6); // 1 AW = X minutes

            // Global markups / rules (baseline defaults)
            $table->decimal('material_overhead_percent', 6, 2)->default(0); // Material GK
            $table->decimal('labor_overhead_percent', 6, 2)->default(0);    // Labor GK if not in full rate
            $table->decimal('site_overhead_percent', 6, 2)->default(0);     // Baustelle GK
            $table->decimal('site_overhead_fixed', 12, 2)->default(0);      // or fixed €

            $table->decimal('risk_percent', 6, 2)->default(0);              // Wagnis
            $table->decimal('profit_percent', 6, 2)->default(0);            // Gewinn target

            // Purchasing rules (defaults)
            $table->decimal('small_parts_percent', 6, 2)->default(0);       // Kleinteileaufschlag
            $table->decimal('freight_fixed', 12, 2)->default(0);            // Fracht pauschal
            $table->decimal('handling_fixed', 12, 2)->default(0);           // Handling pauschal
            $table->decimal('disposal_fixed', 12, 2)->default(0);           // Entsorgung pauschal

            // Rounding rules
            $table->decimal('rounding_step', 6, 2)->default(0.05);          // 0.05 / 0.10 / 1.00
            $table->string('rounding_mode')->default('nearest');            // nearest|up|down

            // Commission rule
            $table->string('commission_mode')->default('revenue_percent');  // revenue_percent|fixed|db_percent
            $table->decimal('commission_value', 10, 2)->default(0);         // % or € depending on mode

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costing_sets');
    }
};