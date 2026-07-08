<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * FiBu Stufe (iv) — GoBD-Buchungs-Engine. Kapselt die unveränderliche Festschreibung, den
 * lückenlosen Nummernkreis, Maker-Checker (Erfasser ≠ Festschreiber) und das Storno (statt Edit/Delete).
 *
 * GoBD-Prinzipien:
 * - **Append-only:** ein festgeschriebener Buchungssatz wird nie geändert/gelöscht. Korrektur = Storno.
 * - **Lückenloser Nummernkreis:** booking_number je Mandant/Geschäftsjahr fortlaufend ohne Lücke,
 *   vergeben in einer gesperrten Transaktion (Konkurrenz-fest).
 * - **Maker-Checker:** wer erfasst (created_by), darf nicht selbst festschreiben (finalized_by).
 * - **Audit-Trail:** created_by/finalized_by/booked_by + Zeitstempel; Storno referenziert das Original.
 */
class BuchungsEngine
{
    /**
     * Schreibt einen Entwurfs-Buchungssatz fest: vergibt Nummer, sperrt, markiert gebucht.
     *
     * @return array{booking_number: string, entry_id: int}
     */
    public function festschreiben(int $entryId, int|string $festschreiberId): array
    {
        $festschreiber = (string) $festschreiberId;

        return DB::transaction(function () use ($entryId, $festschreiber) {
            $entry = DB::table('accounting_journal_entries')->where('id', $entryId)->lockForUpdate()->first();
            if ($entry === null) {
                throw new RuntimeException("Buchungssatz #{$entryId} nicht gefunden.");
            }
            if ((bool) $entry->is_finalized) {
                throw new RuntimeException("Buchungssatz #{$entryId} ist bereits festgeschrieben (unveränderlich).");
            }
            // Maker-Checker: Erfasser darf nicht selbst festschreiben.
            if ($entry->created_by !== null && (string) $entry->created_by === $festschreiber) {
                throw new RuntimeException('Maker-Checker: Erfasser darf den eigenen Buchungssatz nicht festschreiben.');
            }
            // Balance-Gate (doppelte Buchführung) vor Festschreibung.
            $sum = DB::table('accounting_journal_lines')->where('journal_entry_id', $entryId)
                ->selectRaw('COALESCE(SUM(debit_amount),0) AS soll, COALESCE(SUM(credit_amount),0) AS haben')->first();
            if (round((float) $sum->soll - (float) $sum->haben, 2) !== 0.0) {
                throw new RuntimeException("Unausgeglichen: Soll {$sum->soll} ≠ Haben {$sum->haben} — keine Festschreibung.");
            }

            $now = now();
            $number = $this->naechsteNummer((int) $entry->accounting_client_id, $entry->fiscal_year_id, $entry->document_date ?? $now->toDateString());

            DB::table('accounting_journal_entries')->where('id', $entryId)->update([
                'booking_number' => $number,
                'is_finalized' => true, 'finalized_at' => $now, 'finalized_by' => $festschreiber,
                'is_locked' => true, 'is_booked' => true, 'booked_by' => $festschreiber, 'booked_at' => $now,
                'status' => 'festgeschrieben', 'updated_at' => $now,
            ]);

            return ['booking_number' => $number, 'entry_id' => $entryId];
        });
    }

    /**
     * Storniert einen festgeschriebenen Buchungssatz durch einen GEGEN-Buchungssatz (Soll/Haben getauscht).
     * Das Original bleibt unverändert (append-only). Der Storno wird sofort festgeschrieben.
     *
     * @return array{storno_entry_id: int, booking_number: string}
     */
    public function storno(int $entryId, int|string $stornoUserId, string $grund): array
    {
        $stornoUser = (string) $stornoUserId;

        return DB::transaction(function () use ($entryId, $stornoUser, $grund) {
            $orig = DB::table('accounting_journal_entries')->where('id', $entryId)->lockForUpdate()->first();
            if ($orig === null) {
                throw new RuntimeException("Buchungssatz #{$entryId} nicht gefunden.");
            }
            if (! $orig->is_finalized) {
                throw new RuntimeException('Nur festgeschriebene Buchungssätze können storniert werden.');
            }
            if (DB::table('accounting_journal_entries')->where('storno_of_id', $entryId)->exists()) {
                throw new RuntimeException("Buchungssatz #{$entryId} ist bereits storniert.");
            }

            $now = now();
            // Gegen-Buchungssatz (Kopf).
            $stornoId = DB::table('accounting_journal_entries')->insertGetId([
                'accounting_client_id' => $orig->accounting_client_id,
                'fiscal_year_id' => $orig->fiscal_year_id,
                'document_id' => $orig->document_id,
                'booking_date' => $now->toDateString(), 'document_date' => $orig->document_date,
                'booking_text' => 'Storno zu '.($orig->booking_number ?: ('#'.$entryId)).': '.$grund,
                'origin' => 'storno', 'origin_id' => $orig->origin_id,
                'is_storno' => true, 'storno_of_id' => $entryId, 'storno_reason' => $grund,
                'storno_by' => $stornoUser, 'storno_at' => $now,
                'created_by' => $stornoUser, 'created_at' => $now, 'updated_at' => $now,
            ]);
            // Gegen-Zeilen: Soll/Haben getauscht.
            $lines = DB::table('accounting_journal_lines')->where('journal_entry_id', $entryId)->orderBy('line_number')->get();
            $rows = [];
            foreach ($lines as $l) {
                $rows[] = [
                    'journal_entry_id' => $stornoId, 'line_number' => $l->line_number,
                    'account_id' => $l->account_id, 'contra_account_id' => $l->contra_account_id,
                    'debit_amount' => $l->credit_amount, 'credit_amount' => $l->debit_amount,
                    'net_amount' => $l->net_amount, 'tax_amount' => $l->tax_amount, 'gross_amount' => $l->gross_amount,
                    'tax_code_id' => $l->tax_code_id, 'description' => 'Storno: '.$l->description,
                    'created_at' => $now, 'updated_at' => $now,
                ];
            }
            DB::table('accounting_journal_lines')->insert($rows);

            // Storno sofort festschreiben (durch den Storno-Auslöser; Maker-Checker greift hier bewusst nicht,
            // da created_by=stornoUserId — Storno ist ein bewusster, protokollierter Vorgang).
            $number = $this->naechsteNummer((int) $orig->accounting_client_id, $orig->fiscal_year_id, $now->toDateString());
            DB::table('accounting_journal_entries')->where('id', $stornoId)->update([
                'booking_number' => $number, 'is_finalized' => true, 'finalized_at' => $now,
                'finalized_by' => $stornoUser, 'is_locked' => true, 'is_booked' => true,
                'booked_by' => $stornoUser, 'booked_at' => $now, 'status' => 'festgeschrieben', 'updated_at' => $now,
            ]);

            return ['storno_entry_id' => $stornoId, 'booking_number' => $number];
        });
    }

    /**
     * Lückenlose fortlaufende Nummer je Mandant/Geschäftsjahr im Format "JJJJ-NNNNNN".
     * Innerhalb einer gesperrten Transaktion aufgerufen (Konkurrenz-fest).
     */
    private function naechsteNummer(int $clientId, ?int $fiscalYearId, string $documentDate): string
    {
        $jahr = substr($documentDate, 0, 4);
        $prefix = $jahr.'-';
        $max = DB::table('accounting_journal_entries')
            ->where('accounting_client_id', $clientId)
            ->where('booking_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->selectRaw('MAX(CAST(SUBSTRING(booking_number, 6) AS UNSIGNED)) AS m')
            ->value('m');

        return $prefix.str_pad((string) (((int) $max) + 1), 6, '0', STR_PAD_LEFT);
    }
}
