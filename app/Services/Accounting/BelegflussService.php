<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * FiBu Stufe (iii) — Belegfluss-Anker: erzeugt aus einer festgeschriebenen Rechnung (invoices, führende
 * Schiene) den Kopf-Buchungssatz „Debitor an Erlös + Umsatzsteuer". Konten werden ausschließlich über
 * account_mappings (mapping_key → Konto) aufgelöst — KEINE harten Kontonummern (Betriebsordnung).
 *
 * Additiv: liest invoices read-only, schreibt nur in die FiBu-eigenen Tabellen (accounting_documents,
 * accounting_journal_entries/_lines). Idempotent: existiert bereits ein Beleg mit source_invoice_id,
 * wird der vorhandene Buchungssatz zurückgegeben (kein Doppel). Doppelte Buchführung wird geprüft
 * (Soll = Haben), sonst Abbruch (GoBD).
 */
class BelegflussService
{
    /**
     * Bucht eine Rechnung. Gibt ['journal_entry_id', 'document_id', 'soll', 'haben', 'lines', 'skipped'] zurück.
     */
    public function bucheRechnung(int $invoiceId, int|string $erfasserId = 'system'): array
    {
        $erfasser = (string) $erfasserId;

        $invoice = DB::table('invoices')->where('id', $invoiceId)->first();
        if ($invoice === null) {
            throw new RuntimeException("Rechnung #{$invoiceId} nicht gefunden.");
        }

        // Idempotenz: bereits gebucht?
        $existing = DB::table('accounting_documents')->where('source_invoice_id', $invoiceId)->first();
        if ($existing !== null) {
            return ['journal_entry_id' => $existing->id, 'document_id' => $existing->id, 'skipped' => true,
                'soll' => (float) $existing->gross_amount, 'haben' => (float) $existing->gross_amount, 'lines' => 0];
        }

        $client = DB::table('accounting_clients')->where('is_active', true)->orderBy('id')->first();
        if ($client === null) {
            throw new RuntimeException('Kein aktiver Mandant (accounting_clients) — Stufe (ii) zuerst seeden.');
        }
        $chartId = $client->default_chart_of_account_id;

        $net = (float) $invoice->subtotal;
        $tax = (float) $invoice->tax_amount;
        $gross = (float) $invoice->total_amount;
        $rate = (float) $invoice->tax_rate;

        // Erlös-/USt-Key nach Steuersatz.
        $erloesKey = $rate >= 19 ? 'erloes_19' : ($rate > 0 ? 'erloes_7' : 'erloes_steuerfrei');
        $ustKey = $rate >= 19 ? 'ust_19' : ($rate > 0 ? 'ust_7' : null);

        $debitor = $this->konto($client->id, $chartId, 'forderung_debitor');
        $erloes = $this->konto($client->id, $chartId, $erloesKey);
        $ust = $ustKey ? $this->konto($client->id, $chartId, $ustKey) : null;

        return DB::transaction(function () use ($invoice, $client, $net, $tax, $gross, $rate, $debitor, $erloes, $ust, $erfasser) {
            $now = now();

            // Beleg (accounting_documents) — Anker an source_invoice_id.
            $documentId = DB::table('accounting_documents')->insertGetId([
                'accounting_client_id' => $client->id,
                'document_type' => 'ausgangsrechnung',
                'document_date' => $invoice->issue_date,
                'posting_date' => $now->toDateString(),
                'due_date' => $invoice->due_date,
                'source_invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'deal_id' => $invoice->deal_id,
                'net_amount' => $net, 'tax_amount' => $tax, 'gross_amount' => $gross, 'currency' => 'EUR',
                'origin' => 'invoice',
                'description' => 'Ausgangsrechnung #'.$invoice->id,
                'created_at' => $now, 'updated_at' => $now,
            ]);

            // Buchungssatz-Kopf.
            $entryId = DB::table('accounting_journal_entries')->insertGetId([
                'accounting_client_id' => $client->id,
                'document_id' => $documentId,
                'booking_date' => $now->toDateString(),
                'document_date' => $invoice->issue_date,
                'booking_text' => 'Ausgangsrechnung #'.$invoice->id,
                'origin' => 'ausgangsrechnung', 'origin_id' => $invoice->id,
                'status' => 'entwurf', 'created_by' => $erfasser,
                'created_at' => $now, 'updated_at' => $now,
            ]);

            // Buchungszeilen: Debitor (Soll, brutto) an Erlös (Haben, netto) + USt (Haben, steuer).
            $lines = [];
            $ln = 1;
            $lines[] = ['journal_entry_id' => $entryId, 'line_number' => $ln++, 'account_id' => $debitor,
                'debit_amount' => $gross, 'credit_amount' => 0, 'net_amount' => $net, 'tax_amount' => $tax,
                'gross_amount' => $gross, 'description' => 'Forderung', 'created_at' => $now, 'updated_at' => $now];
            $lines[] = ['journal_entry_id' => $entryId, 'line_number' => $ln++, 'account_id' => $erloes,
                'debit_amount' => 0, 'credit_amount' => $net, 'net_amount' => $net, 'tax_amount' => 0,
                'gross_amount' => $net, 'description' => 'Erlös '.number_format($rate, 0).' %', 'created_at' => $now, 'updated_at' => $now];
            if ($ust !== null && $tax > 0) {
                $lines[] = ['journal_entry_id' => $entryId, 'line_number' => $ln++, 'account_id' => $ust,
                    'debit_amount' => 0, 'credit_amount' => $tax, 'net_amount' => 0, 'tax_amount' => $tax,
                    'gross_amount' => $tax, 'description' => 'Umsatzsteuer '.number_format($rate, 0).' %', 'created_at' => $now, 'updated_at' => $now];
            }
            DB::table('accounting_journal_lines')->insert($lines);

            // Doppelte Buchführung: Soll = Haben (GoBD-Gate).
            $soll = array_sum(array_column($lines, 'debit_amount'));
            $haben = array_sum(array_column($lines, 'credit_amount'));
            if (round($soll - $haben, 2) !== 0.0) {
                throw new RuntimeException("Buchungssatz unausgeglichen: Soll {$soll} ≠ Haben {$haben} (Rechnung #{$invoice->id}).");
            }

            return ['journal_entry_id' => $entryId, 'document_id' => $documentId, 'skipped' => false,
                'soll' => $soll, 'haben' => $haben, 'lines' => count($lines)];
        });
    }

    /** mapping_key → account_id für Mandant/Kontenrahmen; wirft, wenn kein Mapping existiert. */
    private function konto(int $clientId, int $chartId, string $key): int
    {
        $accId = DB::table('account_mappings')
            ->where('accounting_client_id', $clientId)->where('chart_of_account_id', $chartId)
            ->where('mapping_key', $key)->where('is_active', true)->value('account_id');
        if ($accId === null) {
            throw new RuntimeException("Kein account_mapping für '{$key}' (Mandant {$clientId}, Kontenrahmen {$chartId}).");
        }

        return (int) $accId;
    }
}
