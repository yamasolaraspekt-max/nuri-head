<?php

namespace App\Services\Maintenance;

use Illuminate\Support\Facades\DB;

/**
 * MASTER-01 P0-6 — reversibler Cleanup der Seed-Artefakte (Yama-Freigabe als eigener Daten-Posten).
 *
 * Zwei Ziele:
 *  1. **Leere verwaiste Objekte** (`lead_alternative_adds` mit `lead_id` ohne `new_leads`), die über KEINE
 *     der FK-referenzierenden Tabellen referenziert werden (Laufzeit-Re-Check über information_schema —
 *     schützt automatisch: was auf Live doch referenziert ist, wird NICHT angefasst).
 *  2. **Test-Rechnung** `TST-OPEN-2337` (`type='Rechnung'`), die den Umsatz verfälscht.
 *
 * Reversibel per **Soft-Delete** (beide Modelle nutzen SoftDeletes) — kein Snapshot nötig; Rückroll =
 * `restore()`. Immer erst `dryRun` + gegen `ticket_testing` proben; Live nur nach Backup + Re-Check (Yama).
 * KEINE additive-Regel-Verletzung: reiner, explizit beauftragter Daten-Posten (DAUERDIREKTIVE).
 */
class SeedOrphanCleanupService
{
    private const TEST_INVOICE_NO = 'TST-OPEN-2337';

    /** IDs verwaister Objekte OHNE jede Referenz (sicher soft-löschbar). */
    public function findLeereWaisenObjekte(): array
    {
        $verwaist = DB::table('lead_alternative_adds as laa')
            ->leftJoin('new_leads as nl', 'laa.lead_id', '=', 'nl.id')
            ->whereNotNull('laa.lead_id')->whereNull('nl.id')->whereNull('laa.deleted_at')
            ->pluck('laa.id')->map(fn ($v) => (int) $v)->all();
        if ($verwaist === []) {
            return [];
        }

        $benutzt = [];
        foreach ($this->referenzSpalten() as [$table, $col]) {
            $ids = DB::table($table)->whereIn($col, $verwaist)->distinct()->pluck($col);
            foreach ($ids as $id) {
                $benutzt[(int) $id] = true;
            }
        }

        return array_values(array_filter($verwaist, fn ($id) => ! isset($benutzt[$id])));
    }

    /** ID der Test-Rechnung (oder null). */
    public function findTestRechnung(): ?int
    {
        $id = DB::table('invoices')->where('invoice_no', self::TEST_INVOICE_NO)
            ->where('type', 'Rechnung')->whereNull('deleted_at')->value('id');

        return $id !== null ? (int) $id : null;
    }

    /**
     * Soft-löscht die sicheren Objekte + die Test-Rechnung. Dry-Run ändert nichts.
     *
     * @return array{objekte:int[], invoice_id:?int, invoices_vorher:int, invoices_nachher:int, umsatz_vorher:float, umsatz_nachher:float, dry_run:bool}
     */
    public function purge(bool $dryRun = true): array
    {
        $objekte = $this->findLeereWaisenObjekte();
        $invoiceId = $this->findTestRechnung();

        $invVorher = (int) DB::table('invoices')->whereNull('deleted_at')->count();
        $umsatzVorher = (float) DB::table('invoices')->whereNull('deleted_at')->sum('total_amount');

        if (! $dryRun) {
            DB::transaction(function () use ($objekte, $invoiceId) {
                $now = now();
                if ($objekte !== []) {
                    DB::table('lead_alternative_adds')->whereIn('id', $objekte)->whereNull('deleted_at')
                        ->update(['deleted_at' => $now]);
                }
                if ($invoiceId !== null) {
                    DB::table('invoices')->where('id', $invoiceId)->whereNull('deleted_at')
                        ->update(['deleted_at' => $now]);
                }
            });
        }

        $invNachher = (int) DB::table('invoices')->whereNull('deleted_at')->count();
        $umsatzNachher = (float) DB::table('invoices')->whereNull('deleted_at')->sum('total_amount');

        return [
            'objekte' => $objekte, 'invoice_id' => $invoiceId,
            'invoices_vorher' => $invVorher, 'invoices_nachher' => $invNachher,
            'umsatz_vorher' => round($umsatzVorher, 2), 'umsatz_nachher' => round($umsatzNachher, 2),
            'dry_run' => $dryRun,
        ];
    }

    /** Rückroll: hebt die Soft-Deletes wieder auf. */
    public function restore(array $objektIds, ?int $invoiceId): void
    {
        DB::transaction(function () use ($objektIds, $invoiceId) {
            if ($objektIds !== []) {
                DB::table('lead_alternative_adds')->whereIn('id', $objektIds)->update(['deleted_at' => null]);
            }
            if ($invoiceId !== null) {
                DB::table('invoices')->where('id', $invoiceId)->update(['deleted_at' => null]);
            }
        });
    }

    /** Alle (Tabelle, Spalte), die auf lead_alternative_adds.id zeigen — dynamisch, DB-neutral. */
    private function referenzSpalten(): array
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('REFERENCED_TABLE_NAME', 'lead_alternative_adds')
            ->get(['TABLE_NAME', 'COLUMN_NAME'])
            ->map(fn ($r) => [$r->TABLE_NAME, $r->COLUMN_NAME])->all();
    }
}
