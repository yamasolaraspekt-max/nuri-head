<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * S1-01 — Rechnungsnummer erst bei Ausstellung.
 * Drafts müssen `invoice_no = NULL` tragen können. Bisher war die Spalte NOT NULL.
 * NOT NULL -> nullable ist nicht-destruktiv (bestehende Werte bleiben unverändert).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_no', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Achtung: schlägt fehl, falls (Draft-)Zeilen mit invoice_no = NULL existieren.
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_no', 50)->nullable(false)->change();
        });
    }
};
