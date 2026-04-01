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
        Schema::table('master_set_components', function (Blueprint $table) {
            // Pricing & Margins
            $table->decimal('purchase_price', 10, 2)->default(0)->after('unit_price'); // EK Preis
            $table->decimal('margin', 5, 2)->default(50)->after('purchase_price'); // Marge in %

            // Commercial Terms
            $table->decimal('skonto', 5, 2)->default(0)->after('margin'); // %
            $table->integer('payment_terms')->default(14)->after('skonto'); // Tage Ziel
            $table->boolean('availability')->default(true)->after('payment_terms');

            // Classification & Properties
            $table->string('type')->default('haupt')->after('availability'); // 'haupt' oder 'zubehoer'
            $table->boolean('is_stammartikel')->default(false)->after('type');
            $table->boolean('is_favorite')->default(false)->after('is_stammartikel');
            
            // Measurement
            $table->string('measure')->default('Stk.')->after('is_favorite'); // Einheit
            $table->decimal('vpe', 10, 2)->default(1)->after('measure'); // VPE
 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_set_components', function (Blueprint $table) {
            //
        });
    }
};
