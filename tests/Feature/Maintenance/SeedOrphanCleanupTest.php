<?php

namespace Tests\Feature\Maintenance;

use App\Services\Maintenance\SeedOrphanCleanupService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * MASTER-01 P0-6 — reversibler Seed-Artefakt-Cleanup, geprüft gegen ticket_testing (NICHT Live).
 * Belegt: nur wirklich leere Waisen + die Test-Rechnung werden soft-gelöscht; referenzierte Objekte,
 * echte Objekte und echte Rechnungen bleiben unberührt; Zähl-/Umsatz-Invariante; Rückroll (restore).
 */
class SeedOrphanCleanupTest extends TestCase
{
    use DatabaseTransactions;

    private SeedOrphanCleanupService $service;

    private int $realLead;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SeedOrphanCleanupService;
        $now = now();
        $this->realLead = DB::table('new_leads')->insertGetId(['customer_type' => 'privat', 'created_at' => $now, 'updated_at' => $now]);
    }

    private function objekt(int $leadId): int
    {
        return DB::table('lead_alternative_adds')->insertGetId(['lead_id' => $leadId, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function rechnung(string $no, string $type, float $betrag): int
    {
        return DB::table('invoices')->insertGetId([
            'customer_id' => $this->realLead, 'type' => $type, 'invoice_no' => $no,
            'issue_date' => '2026-06-30', 'total_amount' => $betrag, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_cleanup_ist_praezise_und_reversibel(): void
    {
        // 19 leere Waisen + 1 referenzierte Waise (mit FK-Checks aus erzeugt — genau wie die realen
        // Waisen laut Audit entstanden sind) + 1 echtes Objekt.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $leere = [];
        for ($i = 0; $i < 19; $i++) {
            $leere[] = $this->objekt(999999);
        }
        $referenziert = $this->objekt(999999);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        DB::table('customer_notes')->insert(['customer_id' => $this->realLead, 'alternative_id' => $referenziert, 'created_at' => now(), 'updated_at' => now()]);
        $echtesObjekt = $this->objekt($this->realLead);

        // Rechnungen: Test + echt.
        $testInv = $this->rechnung('TST-OPEN-2337', 'Rechnung', 1000.00);
        $echtInv = $this->rechnung('RE-TESTECHT-1', 'final', 5000.00);

        // findLeereWaisenObjekte: exakt die 19, NICHT die referenzierte, NICHT das echte Objekt.
        $gefunden = $this->service->findLeereWaisenObjekte();
        sort($gefunden);
        $erwartet = $leere;
        sort($erwartet);
        $this->assertSame($erwartet, $gefunden);
        $this->assertNotContains($referenziert, $gefunden);
        $this->assertNotContains($echtesObjekt, $gefunden);
        $this->assertSame($testInv, $this->service->findTestRechnung());

        // Dry-Run ändert nichts.
        $dry = $this->service->purge(dryRun: true);
        $this->assertTrue($dry['dry_run']);
        $this->assertNull(DB::table('lead_alternative_adds')->where('id', $leere[0])->value('deleted_at'));

        // Echter Lauf.
        $res = $this->service->purge(dryRun: false);
        $this->assertCount(19, $res['objekte']);
        $this->assertSame(1000.00, round($res['umsatz_vorher'] - $res['umsatz_nachher'], 2));
        $this->assertSame(1, $res['invoices_vorher'] - $res['invoices_nachher']);

        // Wirkung: 19 weg, Referenzierte + Echtes + echte Rechnung bleiben.
        $this->assertSame(0, DB::table('lead_alternative_adds')->whereIn('id', $leere)->whereNull('deleted_at')->count());
        $this->assertNull(DB::table('lead_alternative_adds')->where('id', $referenziert)->value('deleted_at'));
        $this->assertNull(DB::table('lead_alternative_adds')->where('id', $echtesObjekt)->value('deleted_at'));
        $this->assertNotNull(DB::table('invoices')->where('id', $testInv)->value('deleted_at'));
        $this->assertNull(DB::table('invoices')->where('id', $echtInv)->value('deleted_at'));

        // Rückroll.
        $this->service->restore($res['objekte'], $res['invoice_id']);
        $this->assertSame(19, DB::table('lead_alternative_adds')->whereIn('id', $leere)->whereNull('deleted_at')->count());
        $this->assertNull(DB::table('invoices')->where('id', $testInv)->value('deleted_at'));
    }
}
