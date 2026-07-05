<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Accounting Stufe (i) — Belege (accounting_documents).
 *
 * Additiv, FiBu-eigen. Adaptiert: Weiche 5 (kein project_id), Ketten-Anker als NULLBARE
 * Referenzen auf ticket-Bestand: source_invoice_id (FK → invoices, read-only Anker der
 * Umsatzdefinition), customer_id/deal_id (lose Referenz, kein FK — koppelt die FiBu nicht an
 * den CRM-Lebenszyklus). Kein imported_from (transaktional, nicht geseedet).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_client_id')->constrained('accounting_clients')->cascadeOnDelete();
            $table->foreignId('fiscal_year_id')->nullable()->constrained('accounting_fiscal_years')->nullOnDelete();
            $table->string('accounting_period_id', 20)->nullable();   // YYYY-MM

            $table->string('internal_number', 50)->nullable();
            $table->string('external_number', 100)->nullable();
            $table->string('document_type', 50)->nullable();          // ausgangsrechnung/eingangsrechnung/...

            $table->date('document_date')->nullable();
            $table->date('service_date')->nullable();
            $table->date('capture_date')->nullable();
            $table->date('posting_date')->nullable();
            $table->date('due_date')->nullable();

            // Ketten-Anker (read-only, additiv):
            $table->foreignId('source_invoice_id')->nullable()->constrained('invoices')->nullOnDelete(); // Umsatzdefinition-Anker
            $table->unsignedBigInteger('customer_id')->nullable()->index();  // lose → customers (kein FK)
            $table->unsignedBigInteger('deal_id')->nullable()->index();      // lose → deals (Weiche 5: kein project_id)

            $table->string('partner_type', 20)->nullable();
            $table->foreignId('debtor_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('creditor_id')->nullable()->constrained('accounts')->nullOnDelete();

            $table->decimal('net_amount', 15, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable();
            $table->decimal('gross_amount', 15, 2)->nullable();
            $table->char('currency', 3)->default('EUR');
            $table->foreignId('tax_code_id')->nullable()->constrained('tax_codes')->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('contra_account_id')->nullable()->constrained('accounts')->nullOnDelete();

            $table->string('payment_status', 30)->nullable();
            $table->string('posting_status', 30)->default('offen');
            $table->string('check_status', 30)->nullable();
            $table->string('approval_status', 30)->nullable();
            $table->string('locking_status', 20)->nullable();
            $table->string('origin', 50)->nullable();
            $table->text('description')->nullable();
            $table->text('remark')->nullable();

            $table->boolean('is_reversal')->default(false);
            $table->unsignedBigInteger('original_document_id')->nullable();  // self, ohne harten FK (Storno-Kette)

            // Maker-Checker (Namen als String — kein FK auf employees, lose Kopplung):
            $table->string('created_by', 100)->nullable();
            $table->string('checked_by', 100)->nullable();
            $table->string('approved_by', 100)->nullable();
            $table->timestamps();

            $table->index(['accounting_client_id', 'document_type']);
            $table->index('source_invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_documents');
    }
};
