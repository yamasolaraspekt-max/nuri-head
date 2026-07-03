<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * S1-01 — Globales Unique-Sicherheitsnetz auf invoice_no.
 * Die bestehende unique(account_id, invoice_no) schützt bei account_id = NULL nicht
 * (alle Bestandsrechnungen haben account_id = NULL). MySQL erlaubt beliebig viele NULLs
 * in einem Unique-Index -> Drafts (invoice_no = NULL) bleiben unbeschränkt, vergebene
 * Nummern werden global eindeutig.
 *
 * Voraussetzung geprüft (read-only): keine Duplikate in invoice_no im Bestand.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unique('invoice_no', 'invoices_invoice_no_unique');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique('invoices_invoice_no_unique');
        });
    }
};
