<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Auftragsbestätigungen — Welle A2 (2026-07-16, Planner-Spec docs/planner-spec-auftragseingang-ab.md).
 * ADDITIV: neue Tabelle, append-only (Korrektur = neue AB, nie Edit). Die Kette (deals,
 * offer_details, invoices) wird NICHT verändert — die AB friert den Stand zum Erzeugungszeitpunkt ein.
 * AB-Nummer = deals.order_number (eine Wahrheit; fehlt sie, bleibt das Feld leer — nichts erfinden).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_confirmations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id');                 // Anker auf deals (read-only)
            $table->string('ab_no')->nullable();                   // Snapshot von deals.order_number zum Zeitpunkt
            $table->string('recipient_name');
            $table->json('positions')->nullable();                 // eingefrorene Positionsliste aus dem Angebots-Snapshot
            $table->decimal('total_net', 12, 2)->nullable();       // Summen aus offer_details (nicht neu gerechnet)
            $table->decimal('tax_rate', 6, 3)->nullable();
            $table->decimal('total_gross', 12, 2)->nullable();
            $table->boolean('ohne_snapshot')->default(false);      // Kante 1: Auftrag ohne Angebots-Snapshot
            $table->text('freitext')->nullable();                  // Zahlungs-/Lieferhinweis
            $table->unsignedBigInteger('created_by')->nullable();  // Auth-User (ohne FK, users/employees-Dualität)
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->index('deal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_confirmations');
    }
};
