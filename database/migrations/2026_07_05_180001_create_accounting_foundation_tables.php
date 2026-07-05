<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Accounting Stufe (i) — Fundament (SKR-neutral, KEIN Seed).
 *
 * FiBu-eigene Tabellen, rein additiv (berühren keinen ticket-Bestand). Quelle: playground-FiBu,
 * adaptiert: Weiche 5 (kein project_id), imported_from-Marker auf seedbaren Referenztabellen,
 * mapping_key-Architektur (Buchungs-Engine kennt keine harten Kontonummern).
 * Kontenrahmen-Seed (SKR03/SKR04) ist ein EIGENER Posten nach Stufe (i).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Kontenrahmen (global: SKR03/SKR04) — Mechanik, hier ungeseedet.
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();        // SKR03 / SKR04
            $table->string('name');
            $table->text('description')->nullable();
            $table->char('country', 2)->default('DE');
            $table->string('version', 20)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('imported_from', 40)->nullable()->index();
            $table->timestamps();
        });

        // 2) Mandant — wählt seinen Rahmen bei Anlage (vor dem ersten Buchungssatz).
        Schema::create('accounting_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('tax_number', 30)->nullable();
            $table->string('vat_id', 30)->nullable();
            $table->foreignId('default_chart_of_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->char('default_currency', 3)->default('EUR');
            $table->unsignedTinyInteger('fiscal_year_start_month')->default(1);
            $table->boolean('is_active')->default(true);
            // DATEV-Stammdaten (Mandant/Berater) — für spätere EXTF-Stufe.
            $table->string('datev_berater_nr', 10)->nullable();
            $table->string('datev_mandant_nr', 10)->nullable();
            $table->boolean('is_datev_enabled')->default(false);
            $table->string('imported_from', 40)->nullable()->index();
            $table->timestamps();
        });

        // 3) Konten — je Rahmen.
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->foreignId('accounting_client_id')->nullable()->constrained('accounting_clients')->cascadeOnDelete(); // null = rahmenweit
            $table->string('account_number', 20);
            $table->string('account_name');
            $table->string('account_type', 40)->nullable();      // aktiv/passiv/erloes/aufwand/...
            $table->string('account_category', 60)->nullable();
            $table->enum('normal_balance', ['soll', 'haben'])->nullable();
            $table->boolean('is_tax_relevant')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('datev_account_number', 20)->nullable();
            $table->string('imported_from', 40)->nullable()->index();
            $table->timestamps();
            $table->unique(['chart_of_account_id', 'accounting_client_id', 'account_number'], 'accounts_chart_client_number_uq');
        });

        // 4) Steuerschlüssel — je Mandant.
        Schema::create('tax_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_client_id')->constrained('accounting_clients')->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('name');
            $table->enum('tax_type', ['normal', 'ermaessigt', 'steuerfrei', 'innergemeinschaftlich', 'reverse_charge', 'bauleistung', 'nicht_steuerbar'])->default('normal');
            $table->enum('tax_direction', ['vorsteuer', 'umsatzsteuer', 'keine'])->default('umsatzsteuer');
            $table->decimal('rate_percent', 5, 2)->nullable();
            $table->foreignId('input_tax_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('output_tax_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->string('imported_from', 40)->nullable()->index();
            $table->timestamps();
            $table->unique(['accounting_client_id', 'code'], 'tax_codes_client_code_uq');
        });

        // 5) mapping_keys — ARCHITEKTUR-KERN: semantischer Schlüssel → Konto (je Rahmen/Mandant).
        //    Die Buchungs-Engine löst NUR über mapping_key auf, nie über harte Kontonummern.
        Schema::create('account_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_client_id')->constrained('accounting_clients')->cascadeOnDelete();
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->string('mapping_key', 80);            // erloes_19, forderung_debitor, ust_19, ...
            $table->string('mapping_name')->nullable();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('tax_code_id')->nullable()->constrained('tax_codes')->nullOnDelete();
            $table->string('applies_to', 80)->nullable();
            $table->unsignedSmallInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('imported_from', 40)->nullable()->index();
            $table->timestamps();
            $table->unique(['accounting_client_id', 'chart_of_account_id', 'mapping_key', 'applies_to'], 'account_mappings_key_uq');
        });

        // 6) Geschäftsjahre — GoBD-Perioden (Festschreibung/Abschluss hängen daran).
        Schema::create('accounting_fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_client_id')->constrained('accounting_clients')->cascadeOnDelete();
            $table->string('label', 20);                  // z.B. "2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_closed')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->unique(['accounting_client_id', 'label'], 'fiscal_years_client_label_uq');
        });
    }

    public function down(): void
    {
        // Reihenfolge FK-sicher (umgekehrt).
        Schema::dropIfExists('accounting_fiscal_years');
        Schema::dropIfExists('account_mappings');
        Schema::dropIfExists('tax_codes');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('accounting_clients');
        Schema::dropIfExists('chart_of_accounts');
    }
};
