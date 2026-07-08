<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * FiBu Stufe (viii, Kern) — Eingangsrechnungs-/Kreditoren-Buchung: „Wareneingang + Vorsteuer an
 * Verbindlichkeit/Kreditor". Symmetrisch zum {@see BelegflussService} (Ausgang), Konten über
 * mapping_keys (wareneingang_*, vorsteuer_*, verbindlichkeit_kreditor) — keine harten Kontonummern.
 *
 * ⚠️ Scope-Grenze (Betriebsordnung, Backlog viii „eigener Block, später"): dieser Service ist das
 * additive Buchungs-Primitiv. Es gibt in ticket noch KEINE Eingangsrechnungs-Quelltabelle; die
 * Anbindung an eine reale Quelle (neue Tabelle vs. purchase_requests) und die Bestelllisten-/
 * DealMaterialList-Feinkartierung + Kreditoren-UI bleiben Yama-Entscheidung. Daher nimmt die Methode
 * die Belegdaten explizit entgegen und liest keine (noch nicht existierende) Quelltabelle.
 */
class EingangsBelegflussService
{
    /**
     * Bucht eine Eingangsrechnung als Entwurf (Festschreibung = zweiter Schritt via BuchungsEngine).
     * Idempotent über (Mandant, Kreditor, external_number).
     *
     * @param array{creditor_id?:int|null, external_number:string, document_date:string, net:float, tax:float, rate:float, aufwand_key?:string, erfasser?:string} $daten
     *   creditor_id = Kreditor-Personenkonto (accounts.id); null ⇒ Buchung auf das Sammelkonto verbindlichkeit_kreditor.
     * @return array{journal_entry_id:int, document_id:int, soll:float, haben:float, lines:int, skipped:bool}
     */
    public function bucheEingangsrechnung(array $daten): array
    {
        $erfasser = (string) ($daten['erfasser'] ?? 'system');
        $net = round((float) $daten['net'], 2);
        $tax = round((float) $daten['tax'], 2);
        $gross = round($net + $tax, 2);
        $rate = (float) $daten['rate'];
        $externalNumber = (string) $daten['external_number'];

        $client = DB::table('accounting_clients')->where('is_active', true)->orderBy('id')->first();
        if ($client === null) {
            throw new RuntimeException('Kein aktiver Mandant (accounting_clients) — Stufe (ii) zuerst seeden.');
        }
        $chartId = $client->default_chart_of_account_id;

        $aufwandKey = $daten['aufwand_key'] ?? 'wareneingang_19';
        $vstKey = $rate >= 19 ? 'vorsteuer_19' : ($rate > 0 ? 'vorsteuer_7' : null);

        $aufwand = $this->konto($client->id, $chartId, $aufwandKey);
        $vst = $vstKey ? $this->konto($client->id, $chartId, $vstKey) : null;

        // Kreditor = übergebenes Personenkonto (accounts.id) oder Sammelkonto verbindlichkeit_kreditor.
        $kreditorId = isset($daten['creditor_id']) && $daten['creditor_id'] !== null ? (int) $daten['creditor_id'] : null;
        if ($kreditorId !== null && ! DB::table('accounts')->where('id', $kreditorId)->exists()) {
            throw new RuntimeException("Kreditor-Personenkonto #{$kreditorId} (accounts) existiert nicht.");
        }
        $kreditor = $kreditorId ?? $this->konto($client->id, $chartId, 'verbindlichkeit_kreditor');

        // Idempotenz (Mandant + Kreditor-Konto + Belegnummer).
        $existing = DB::table('accounting_documents')
            ->where('accounting_client_id', $client->id)->where('creditor_id', $kreditor)
            ->where('external_number', $externalNumber)->first();
        if ($existing !== null) {
            return ['journal_entry_id' => $existing->id, 'document_id' => $existing->id, 'skipped' => true,
                'soll' => (float) $existing->gross_amount, 'haben' => (float) $existing->gross_amount, 'lines' => 0];
        }

        return DB::transaction(function () use ($daten, $client, $net, $tax, $gross, $rate, $aufwand, $kreditor, $vst, $externalNumber, $erfasser) {
            $now = now();
            $documentId = DB::table('accounting_documents')->insertGetId([
                'accounting_client_id' => $client->id, 'document_type' => 'eingangsrechnung',
                'document_date' => $daten['document_date'], 'posting_date' => $now->toDateString(),
                'external_number' => $externalNumber, 'partner_type' => 'kreditor', 'creditor_id' => $kreditor,
                'net_amount' => $net, 'tax_amount' => $tax, 'gross_amount' => $gross, 'currency' => 'EUR',
                'origin' => 'eingangsrechnung', 'description' => 'Eingangsrechnung '.$externalNumber,
                'created_by' => $erfasser, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $entryId = DB::table('accounting_journal_entries')->insertGetId([
                'accounting_client_id' => $client->id, 'document_id' => $documentId,
                'booking_date' => $now->toDateString(), 'document_date' => $daten['document_date'],
                'booking_text' => 'Eingangsrechnung '.$externalNumber,
                'origin' => 'eingangsrechnung', 'origin_id' => $kreditor,
                'status' => 'entwurf', 'created_by' => $erfasser, 'created_at' => $now, 'updated_at' => $now,
            ]);

            // Wareneingang (Soll, netto) + Vorsteuer (Soll, steuer) an Kreditor (Haben, brutto).
            $lines = [];
            $ln = 1;
            $lines[] = ['journal_entry_id' => $entryId, 'line_number' => $ln++, 'account_id' => $aufwand,
                'debit_amount' => $net, 'credit_amount' => 0, 'net_amount' => $net, 'tax_amount' => 0,
                'gross_amount' => $net, 'description' => 'Wareneingang', 'created_at' => $now, 'updated_at' => $now];
            if ($vst !== null && $tax > 0) {
                $lines[] = ['journal_entry_id' => $entryId, 'line_number' => $ln++, 'account_id' => $vst,
                    'debit_amount' => $tax, 'credit_amount' => 0, 'net_amount' => 0, 'tax_amount' => $tax,
                    'gross_amount' => $tax, 'description' => 'Vorsteuer '.number_format($rate, 0).' %', 'created_at' => $now, 'updated_at' => $now];
            }
            $lines[] = ['journal_entry_id' => $entryId, 'line_number' => $ln++, 'account_id' => $kreditor,
                'debit_amount' => 0, 'credit_amount' => $gross, 'net_amount' => $net, 'tax_amount' => $tax,
                'gross_amount' => $gross, 'description' => 'Verbindlichkeit Kreditor', 'created_at' => $now, 'updated_at' => $now];
            DB::table('accounting_journal_lines')->insert($lines);

            $soll = round(array_sum(array_column($lines, 'debit_amount')), 2);
            $haben = round(array_sum(array_column($lines, 'credit_amount')), 2);
            if (round($soll - $haben, 2) !== 0.0) {
                throw new RuntimeException("Eingangs-Buchung unausgeglichen: Soll {$soll} ≠ Haben {$haben} ({$externalNumber}).");
            }

            return ['journal_entry_id' => $entryId, 'document_id' => $documentId, 'skipped' => false,
                'soll' => $soll, 'haben' => $haben, 'lines' => count($lines)];
        });
    }

    /** mapping_key → account_id; wirft, wenn kein Mapping existiert. */
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
