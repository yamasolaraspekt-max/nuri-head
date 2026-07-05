<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Accounting Stufe (i) — Journal (Buchungssatz-Kopf + Zeilen).
 *
 * Additiv, FiBu-eigen. Trägt die GoBD-Felder als SCHEMA (Maker-Checker created/reviewed/approved/
 * booked_by, Festschreibung is_finalized, Storno-Kette). Die durchsetzende LOGIK (append-only,
 * lückenloser Nummernkreis, Festschreib-Gate) ist Stufe (iv) — hier nur die Tabellen.
 * booking_stack_id bleibt ohne FK (Buchungsstapel = spätere Stufe).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_client_id')->constrained('accounting_clients')->cascadeOnDelete();
            $table->foreignId('fiscal_year_id')->nullable()->constrained('accounting_fiscal_years')->nullOnDelete();
            $table->string('period_id', 7)->nullable();               // YYYY-MM
            $table->unsignedBigInteger('booking_stack_id')->nullable()->index(); // Stapel: spätere Stufe (kein FK)

            $table->string('booking_number', 50)->nullable();         // lückenlos: Stufe (iv)
            $table->date('booking_date')->nullable();
            $table->date('document_date')->nullable();
            $table->date('service_date')->nullable();

            $table->foreignId('document_id')->nullable()->constrained('accounting_documents')->nullOnDelete();
            $table->string('document_number', 100)->nullable();
            $table->string('booking_text', 500)->nullable();

            $table->enum('origin', ['ausgangsrechnung', 'eingangsrechnung', 'bank', 'kasse', 'zahlungszuordnung', 'gutschrift', 'storno', 'manuell', 'geldtransit', 'jahresabschluss'])->nullable();
            $table->unsignedBigInteger('origin_id')->nullable();

            // Status/GoBD:
            $table->enum('status', ['entwurf', 'geprueft', 'freigegeben', 'gebucht', 'storniert', 'gesperrt', 'festgeschrieben', 'exportiert'])->default('entwurf');
            $table->boolean('is_booked')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_finalized')->default(false);          // Festschreibung
            $table->timestamp('finalized_at')->nullable();
            $table->string('finalized_by', 100)->nullable();
            $table->enum('datev_export_status', ['nicht_vorbereitet', 'vorbereitet', 'exportiert'])->default('nicht_vorbereitet');

            // Storno-Kette (Storno statt Edit — GoBD):
            $table->boolean('is_storno')->default(false);
            $table->unsignedBigInteger('storno_of_id')->nullable()->index();   // self (Storno-Referenz, ohne harten FK)
            $table->string('storno_reason', 500)->nullable();
            $table->string('storno_by', 100)->nullable();
            $table->timestamp('storno_at')->nullable();

            // Maker-Checker:
            $table->string('created_by', 100)->nullable();
            $table->string('reviewed_by', 100)->nullable();
            $table->string('approved_by', 100)->nullable();
            $table->string('booked_by', 100)->nullable();
            $table->timestamp('booked_at')->nullable();
            $table->timestamps();

            $table->index(['accounting_client_id', 'status'], 'aje_client_status_idx');
            $table->index(['accounting_client_id', 'booking_number'], 'aje_client_booking_idx');
        });

        Schema::create('accounting_journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('accounting_journal_entries')->cascadeOnDelete();
            $table->unsignedSmallInteger('line_number')->default(1);
            $table->foreignId('account_id')->constrained('accounts')->restrictOnDelete();
            $table->foreignId('contra_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable();
            $table->decimal('gross_amount', 15, 2)->nullable();
            $table->foreignId('tax_code_id')->nullable()->constrained('tax_codes')->nullOnDelete();
            $table->string('description', 500)->nullable();
            $table->timestamps();

            $table->index('journal_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_journal_lines');
        Schema::dropIfExists('accounting_journal_entries');
    }
};
