<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;

/**
 * FiBu Stufe (v) — Auswertungen aus dem festgeschriebenen Journal (nur is_finalized-Buchungen zählen; GoBD).
 *
 * - Summen- und Saldenliste (SuSa): je Konto Soll/Haben/Saldo im Zeitraum.
 * - Umsatzsteuer-Voranmeldung (UStVA): Kennzahlen KZ81/KZ86 (Netto-Umsätze 19/7 %), Umsatzsteuer,
 *   KZ66 (Vorsteuer), KZ83 (Zahllast) — Konten über account_mappings aufgelöst (keine harten Nummern).
 * - Betriebswirtschaftliche Auswertung (BWA): Erlöse, Wareneinsatz, Rohertrag, sonstige Aufwendungen, Ergebnis.
 *
 * Read-only: rührt weder Journal noch Belegkette an.
 */
class AuswertungsService
{
    /**
     * Summen- und Saldenliste: je bewegtes Konto Soll-/Haben-Summe und Saldo (Soll−Haben).
     *
     * @return array<int, array{account_id:int, account_number:string, account_name:string, soll:float, haben:float, saldo:float}>
     */
    public function summenSalden(int $clientId, string $von, string $bis): array
    {
        $rows = DB::table('accounting_journal_lines as l')
            ->join('accounting_journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('e.accounting_client_id', $clientId)
            ->where('e.is_finalized', true)
            ->whereBetween('e.booking_date', [$von, $bis])
            ->groupBy('a.id', 'a.account_number', 'a.account_name')
            ->orderBy('a.account_number')
            ->select('a.id as account_id', 'a.account_number', 'a.account_name',
                DB::raw('SUM(l.debit_amount) as soll'), DB::raw('SUM(l.credit_amount) as haben'))
            ->get();

        return $rows->map(fn ($r) => [
            'account_id' => (int) $r->account_id, 'account_number' => $r->account_number,
            'account_name' => $r->account_name, 'soll' => (float) $r->soll, 'haben' => (float) $r->haben,
            'saldo' => round((float) $r->soll - (float) $r->haben, 2),
        ])->all();
    }

    /**
     * UStVA-Kennzahlen für den Zeitraum.
     *
     * @return array{kz81_netto_19:float, ust_19:float, kz86_netto_7:float, ust_7:float, kz66_vorsteuer:float, kz83_zahllast:float}
     */
    public function ustVoranmeldung(int $clientId, string $von, string $bis): array
    {
        $netto19 = $this->habenAufKonto($clientId, $von, $bis, 'erloes_19');
        $netto7 = $this->habenAufKonto($clientId, $von, $bis, 'erloes_7');
        $ust19 = $this->habenAufKonto($clientId, $von, $bis, 'ust_19');
        $ust7 = $this->habenAufKonto($clientId, $von, $bis, 'ust_7');
        $vst19 = $this->sollAufKonto($clientId, $von, $bis, 'vorsteuer_19');
        $vst7 = $this->sollAufKonto($clientId, $von, $bis, 'vorsteuer_7');
        $vorsteuer = round($vst19 + $vst7, 2);
        $zahllast = round($ust19 + $ust7 - $vorsteuer, 2);

        return [
            'kz81_netto_19' => $netto19, 'ust_19' => $ust19,
            'kz86_netto_7' => $netto7, 'ust_7' => $ust7,
            'kz66_vorsteuer' => $vorsteuer, 'kz83_zahllast' => $zahllast,
        ];
    }

    /**
     * BWA-Grunddaten: Erlöse, Wareneinsatz, Rohertrag, sonstige Aufwendungen, vorläufiges Ergebnis.
     *
     * @return array{erloese:float, wareneinsatz:float, rohertrag:float, sonstige_aufwendungen:float, ergebnis:float}
     */
    public function bwa(int $clientId, string $von, string $bis): array
    {
        // Erlöse: Haben−Soll auf Erlöskonten (normal_balance=haben).
        $erloese = $this->kategorieSaldo($clientId, $von, $bis, 'umsatzerloese', 'haben');
        $wareneinsatz = $this->kategorieSaldo($clientId, $von, $bis, 'wareneinsatz', 'soll');
        $sonstige = $this->kategorieSaldo($clientId, $von, $bis, 'sonstige_aufwendungen', 'soll');
        $rohertrag = round($erloese - $wareneinsatz, 2);
        $ergebnis = round($rohertrag - $sonstige, 2);

        return [
            'erloese' => $erloese, 'wareneinsatz' => $wareneinsatz, 'rohertrag' => $rohertrag,
            'sonstige_aufwendungen' => $sonstige, 'ergebnis' => $ergebnis,
        ];
    }

    /** Haben-Summe auf dem über mapping_key aufgelösten Konto. */
    private function habenAufKonto(int $clientId, string $von, string $bis, string $key): float
    {
        return $this->summeAufMappingKonto($clientId, $von, $bis, $key, 'credit_amount');
    }

    /** Soll-Summe auf dem über mapping_key aufgelösten Konto. */
    private function sollAufKonto(int $clientId, string $von, string $bis, string $key): float
    {
        return $this->summeAufMappingKonto($clientId, $von, $bis, $key, 'debit_amount');
    }

    private function summeAufMappingKonto(int $clientId, string $von, string $bis, string $key, string $spalte): float
    {
        $client = DB::table('accounting_clients')->find($clientId);
        $accId = DB::table('account_mappings')
            ->where('accounting_client_id', $clientId)
            ->where('chart_of_account_id', $client->default_chart_of_account_id)
            ->where('mapping_key', $key)->where('is_active', true)->value('account_id');
        if ($accId === null) {
            return 0.0;
        }

        return round((float) DB::table('accounting_journal_lines as l')
            ->join('accounting_journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->where('e.accounting_client_id', $clientId)->where('e.is_finalized', true)
            ->whereBetween('e.booking_date', [$von, $bis])
            ->where('l.account_id', $accId)->sum('l.'.$spalte), 2);
    }

    /** Saldo einer Konten-Kategorie in Richtung des Normalsaldos (haben ⇒ Haben−Soll, soll ⇒ Soll−Haben). */
    private function kategorieSaldo(int $clientId, string $von, string $bis, string $kategorie, string $richtung): float
    {
        $r = DB::table('accounting_journal_lines as l')
            ->join('accounting_journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('e.accounting_client_id', $clientId)->where('e.is_finalized', true)
            ->whereBetween('e.booking_date', [$von, $bis])
            ->where('a.account_category', $kategorie)
            ->selectRaw('COALESCE(SUM(l.debit_amount),0) as soll, COALESCE(SUM(l.credit_amount),0) as haben')
            ->first();
        $soll = (float) $r->soll;
        $haben = (float) $r->haben;

        return round($richtung === 'haben' ? $haben - $soll : $soll - $haben, 2);
    }
}
