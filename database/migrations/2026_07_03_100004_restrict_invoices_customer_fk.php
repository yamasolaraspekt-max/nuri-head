<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * S1-02 — DB-Sicherheitsnetz gegen Kaskaden-Löschung von Rechnungen.
 * Bisher: invoices.customer_id -> new_leads.id ON DELETE CASCADE
 *  => Hard-Delete eines Kunden hätte dessen Rechnungen (inkl. ausgestellter) mitgelöscht.
 * Neu: ON DELETE RESTRICT — der App-Guard blockiert bereits; die DB ist der harte Backstop.
 *
 * Nicht-destruktiv: ändert nur das ON-DELETE-Verhalten, keine Daten. Bestehende Zeilen
 * verletzen die FK nicht (alle invoices.customer_id verweisen weiterhin gültig auf new_leads).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['customer_id']); // invoices_customer_id_foreign
            $table->foreign('customer_id')->references('id')->on('new_leads')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('new_leads')->cascadeOnDelete();
        });
    }
};
