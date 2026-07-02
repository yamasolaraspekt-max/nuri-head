<?php

namespace Database\Seeders\Testing;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gemeinsame Basis fuer alle [TEST-HARNESS]-Seeder.
 *
 * - TAG: Markierung in Textfeldern jeder Testzeile -> Teardown-Schluessel (kein Schema-Eingriff).
 * - guardLocal(): wirft ausserhalb der local-Umgebung (schuetzt Prod/Staging).
 * - upsertId(): idempotentes Insert/Update ueber einen Marker-Schluessel (Re-Seed dupliziert nicht,
 *   setzt mutable Felder auf den Soll-Zustand zurueck -> deterministisch).
 * - context()/employees(): lesen den Test-Kontext bzw. die Test-Mitarbeiter ueber die Marker,
 *   damit die Seeder voneinander entkoppelt sind.
 */
trait HarnessSupport
{
    /** Markierung in Textfeldern jeder Testzeile; Teardown-Schluessel. */
    public const TAG = '[TEST-HARNESS]';

    /** Eigene Test-Domain fuer Marker-User (Teardown loescht per E-Mail-Domain). */
    public const USER_DOMAIN = '@test-harness.local';

    /** Wirft ausserhalb von local. In JEDEM Seeder (inkl. Teardown) als erste Zeile aufrufen. */
    protected function guardLocal(): void
    {
        if (!app()->environment('local')) {
            throw new \RuntimeException(
                self::TAG . ' Seeder darf ausschliesslich in der local-Umgebung laufen. Aktuell: '
                . app()->environment()
            );
        }
    }

    /** Baut einen Marker-String: "[TEST-HARNESS] <suffix>". */
    protected function mark(string $suffix): string
    {
        return self::TAG . ' ' . $suffix;
    }

    /**
     * Idempotentes Upsert ueber $match; gibt die id zurueck.
     * - existiert die Zeile: $extra wird aktualisiert (mutable Felder zuruecksetzen).
     * - sonst: Insert aus $match + $extra.
     * created_at/updated_at werden nur gesetzt, wenn die Tabelle sie hat.
     */
    protected function upsertId(string $table, array $match, array $extra = []): int
    {
        $now = now();
        $hasCreated = Schema::hasColumn($table, 'created_at');
        $hasUpdated = Schema::hasColumn($table, 'updated_at');

        $existing = DB::table($table)->where($match)->value('id');

        if ($existing) {
            $upd = $extra;
            if ($hasUpdated) {
                $upd['updated_at'] = $now;
            }
            if (!empty($upd)) {
                DB::table($table)->where('id', $existing)->update($upd);
            }
            return (int) $existing;
        }

        $ins = array_merge($match, $extra);
        if ($hasCreated) {
            $ins['created_at'] = $now;
        }
        if ($hasUpdated) {
            $ins['updated_at'] = $now;
        }

        return (int) DB::table($table)->insertGetId($ins);
    }

    /**
     * Test-Kontext ueber die Marker: Produkt, Kunde, Objekt, lead_product_list,
     * task_phase, die zwei Taetigkeiten (mit/ohne Anforderung).
     * Wirft, wenn der HarnessContextSeeder noch nicht lief.
     */
    protected function context(): object
    {
        $productId  = DB::table('article_groups')->where('article_group', $this->mark('Produkt'))->value('id');
        $customerId = DB::table('new_leads')->where('name', $this->mark('Kunde'))->value('id');
        $objectId   = DB::table('lead_alternative_adds')->where('object_name', $this->mark('Objekt'))->value('id');

        $lplId = ($customerId && $productId)
            ? DB::table('lead_product_lists')->where('customer_id', $customerId)->where('product_id', $productId)->value('id')
            : null;

        $phaseId        = DB::table('task_phases')->where('phase_name', $this->mark('Phase'))->value('id');
        $activityReqId  = DB::table('phase_activities')->where('title', $this->mark('Montage (Meister erforderlich)'))->value('id');
        $activityFreeId = DB::table('phase_activities')->where('title', $this->mark('Aufmass (keine Anforderung)'))->value('id');

        if (!$productId || !$customerId || !$objectId || !$lplId || !$phaseId || !$activityReqId || !$activityFreeId) {
            throw new \RuntimeException(self::TAG . ' Kontext unvollstaendig - HarnessContextSeeder zuerst ausfuehren.');
        }

        return (object) compact(
            'productId', 'customerId', 'objectId', 'lplId', 'phaseId', 'activityReqId', 'activityFreeId'
        );
    }

    /** Test-Mitarbeiter ueber den Marker (lastname = TAG, Rolle ueber name). */
    protected function employees(): object
    {
        $byRole = fn(string $role) => DB::table('employees')
            ->where('lastname', self::TAG)
            ->where('name', $role)
            ->value('id');

        return (object) [
            'qual'     => $byRole('MonteurQual'),
            'unqual'   => $byRole('MonteurUnqual'),
            'reviewer' => $byRole('Pruefer'),
            'other'    => $byRole('Fremder'),
        ];
    }
}
