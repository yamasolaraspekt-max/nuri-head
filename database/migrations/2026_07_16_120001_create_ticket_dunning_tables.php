<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mahnwesen — Welle A1, Paket 2 (2026-07-16). Muster: hausverwaltung dunning_tables (H2),
 * adaptiert auf die ticket-invoices-Schiene. ADDITIV (Dauerdirektive): neue Tabellen,
 * kein Feld der Kette wird verändert; invoices wird nur referenziert (Lese-Anker).
 * Append-only-Prinzip: Mahnläufe/Positionen werden nie editiert (Korrektur = neuer Lauf).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dunning_runs', function (Blueprint $table) {
            $table->id();
            $table->date('run_date');
            $table->json('fees');                                       // Gebühr je Stufe zum Zeitpunkt des Laufs, z. B. {"1":0,"2":5,"3":10}
            $table->unsignedSmallInteger('items_count')->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);         // Summe total_due aller Positionen
            $table->unsignedBigInteger('executed_by')->nullable();      // Auth-User-ID (bewusst ohne FK — users/employees-Dualität in ticket)
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('run_date');
        });

        Schema::create('dunning_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dunning_run_id')->constrained('dunning_runs')->cascadeOnDelete();
            $table->unsignedBigInteger('invoice_id');                   // Anker auf invoices (führende Schiene, read-only)
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('recipient_name');                           // Snapshot des Empfängers zum Mahnzeitpunkt
            $table->unsignedTinyInteger('level');                       // 1 Zahlungserinnerung | 2 1. Mahnung | 3 2. Mahnung (letzte)
            $table->unsignedSmallInteger('days_overdue');
            $table->decimal('open_amount', 12, 2);                      // Snapshot offener Betrag
            $table->decimal('fee', 8, 2)->default(0);
            $table->decimal('interest', 8, 2)->default(0);              // V1: 0 — Verzugszins nur als Hinweis im Schreiben (Basiszins ändert sich halbjährlich)
            $table->decimal('total_due', 12, 2);                        // offen + Gebühr (+ Zinsen, sobald aktiviert)
            $table->date('pay_until');                                  // neue Zahlungsfrist laut Schreiben
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dunning_items');
        Schema::dropIfExists('dunning_runs');
    }
};
