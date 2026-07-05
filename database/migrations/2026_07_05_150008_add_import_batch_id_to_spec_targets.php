<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Spec-Standard M-B (Baustufe 2): Lauf-Marker import_batch_id an ALLEN von spec:import beschriebenen
 * Tabellen (products + die 4 Spec-Ziele), damit der Batch-Rückbau lauf-genau über alle berührten Tabellen
 * greift. Additiv/nullable + Index, nur ticket_testing; main = M5-Deploy-Paket.
 */
return new class extends Migration
{
    private array $tabellen = ['products', 'product_heat_pump_specs', 'product_pv_module_specs', 'inverters', 'batteries'];

    public function up(): void
    {
        foreach ($this->tabellen as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->uuid('import_batch_id')->nullable()->index()
                    ->comment('spec:import Lauf-Marker (Batch-Rückbau)');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tabellen as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn('import_batch_id');
            });
        }
    }
};
