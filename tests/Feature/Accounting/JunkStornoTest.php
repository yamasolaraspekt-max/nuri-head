<?php

namespace Tests\Feature\Accounting;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * BUG-1 — Junk-Storno-Loch: DealController::junk() markierte einen Auftrag als 'Junk', rechnete aber
 * die deal-verknuepften Rechnungen NICHT ab (im Gegensatz zu destroy()) -> Phantom-Forderungen im
 * Mahnwesen / offene Posten. Fix: junk() ruft nun — atomar gespiegelt zu destroy() — cancelInvoicesForDeal().
 *
 * Verhalten cancelInvoicesForDeal (unveraendert): offen (paid_amount=0) -> 'storniert';
 * bezahlt (paid_amount>0) -> 'storniert_bezahlt_pruefen'; idempotent (bereits stornierte uebersprungen).
 *
 * Schema-Befunde (empirisch, MySQL lokal):
 *  - deals NOT NULL ohne Default: customer_id, product_id, alternative_id, service, employee_id.
 *  - invoices NOT NULL ohne Default: customer_id, type, issue_date. status Default 'draft', paid_amount Default 0.00.
 *  Fixtures per DB::table()->insert() mit diesen Minimal-Spalten (kein FK-Konflikt beobachtet).
 */
class JunkStornoTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        // is_admin-Bypass -> passiert authorizeDealDelete(); CSRF ist in Feature-Tests aus.
        return User::factory()->create([
            'password' => 'password',
            'name'     => (string) random_int(1, 9999),
            'is_admin' => true,
        ]);
    }

    /**
     * Minimal-Deal anlegen (nur NOT-NULL-ohne-Default-Spalten + status).
     * deals hat FKs auf customer_id/product_id/alternative_id/employee_id -> fuer die isolierte
     * Fixture FK-Checks kurz aus (wie bestehende Security-Tests, DatabaseTransactions raeumt auf).
     */
    private function makeDeal(): int
    {
        $now = now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $id = DB::table('deals')->insertGetId([
            'customer_id'    => random_int(100000, 999999),
            'product_id'     => random_int(100000, 999999),
            'alternative_id' => random_int(100000, 999999),
            'service'        => 'Test',
            'employee_id'    => random_int(100000, 999999),
            'status'         => 'deal',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return $id;
    }

    /** Minimal-Rechnung an einen Deal haengen. */
    private function makeInvoice(int $dealId, string $status, float $paid, float $total): int
    {
        $now = now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $id = DB::table('invoices')->insertGetId([
            'customer_id'  => random_int(100000, 999999),
            'type'         => 'invoice',
            'issue_date'   => '2026-04-01',
            'deal_id'      => $dealId,
            'status'       => $status,
            'paid_amount'  => $paid,
            'total_amount' => $total,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return $id;
    }

    private function invStatus(int $id): string
    {
        return (string) DB::table('invoices')->where('id', $id)->value('status');
    }

    /** Summe der noch offenen (nicht-stornierten) Rechnungen des Deals. */
    private function offenePostenSumme(int $dealId): float
    {
        return (float) DB::table('invoices')
            ->where('deal_id', $dealId)
            ->whereNotIn('status', ['storniert', 'storniert_bezahlt_pruefen'])
            ->sum('total_amount');
    }

    /** Fall 1 (Kern) + Fall 2 (Offene-Posten-Summe vorher/nachher). */
    public function test_junk_storniert_offene_und_markiert_bezahlte_rechnungen(): void
    {
        $dealId = $this->makeDeal();
        $offen  = $this->makeInvoice($dealId, 'versendet', 0.0, 1000.00);
        $bezahlt = $this->makeInvoice($dealId, 'bezahlt', 500.00, 500.00);

        // Fall 2 (vorher): offene Posten > 0.
        $this->assertSame(1500.00, $this->offenePostenSumme($dealId), 'Offene Posten vor junk muessen > 0 sein.');

        $this->actingAs($this->admin())->get('/deal_junk/' . $dealId)->assertRedirect();

        // Fall 1: Statuswechsel korrekt.
        $this->assertSame('storniert', $this->invStatus($offen), 'Offene Rechnung muss storniert sein.');
        $this->assertSame('storniert_bezahlt_pruefen', $this->invStatus($bezahlt), 'Bezahlte Rechnung muss zur Pruefung markiert sein.');
        $this->assertSame('Junk', (string) DB::table('deals')->where('id', $dealId)->value('status'));

        // Fall 2 (nachher): keine offenen Posten mehr.
        $this->assertSame(0.0, $this->offenePostenSumme($dealId), 'Nach junk duerfen keine offenen Posten mehr existieren.');
    }

    /** Fall 3 — Idempotenz: zweiter junk-Aufruf aendert nichts, keine Exception. */
    public function test_junk_ist_idempotent(): void
    {
        $dealId = $this->makeDeal();
        $offen  = $this->makeInvoice($dealId, 'versendet', 0.0, 800.00);
        $admin  = $this->admin();

        $this->actingAs($admin)->get('/deal_junk/' . $dealId)->assertRedirect();
        $this->assertSame('storniert', $this->invStatus($offen));

        // Zweiter Aufruf: Status bleibt, kein Fehler.
        $this->actingAs($admin)->get('/deal_junk/' . $dealId)->assertRedirect();
        $this->assertSame('storniert', $this->invStatus($offen));
        $this->assertSame(0.0, $this->offenePostenSumme($dealId));
    }

    /** Fall 4 — Leerfall: Deal ohne jede Rechnung laeuft ohne 500 durch. */
    public function test_junk_ohne_rechnungen_laeuft_durch(): void
    {
        $dealId = $this->makeDeal();

        $res = $this->actingAs($this->admin())->get('/deal_junk/' . $dealId);
        $res->assertRedirect();
        $this->assertNotSame(500, $res->getStatusCode());

        $this->assertSame('Junk', (string) DB::table('deals')->where('id', $dealId)->value('status'));
    }

    /**
     * Deal + passende lead_product_lists-Zeile (gleiche customer/product/alternative, status+stage='deal')
     * anlegen, damit restoreLeadStageForDeal() das UPDATE auf lead_product_lists WIRKLICH feuert
     * (Voraussetzung fuer den Rollback-Test unten). Gibt [dealId, lplId] zurueck.
     */
    private function makeDealWithLeadStage(): array
    {
        $now = now();
        $customerId    = random_int(100000, 999999);
        $productId     = random_int(100000, 999999);
        $alternativeId = random_int(100000, 999999);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $dealId = DB::table('deals')->insertGetId([
            'customer_id'    => $customerId,
            'product_id'     => $productId,
            'alternative_id' => $alternativeId,
            'service'        => 'Test',
            'employee_id'    => random_int(100000, 999999),
            'status'         => 'deal',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
        $lplId = DB::table('lead_product_lists')->insertGetId([
            'customer_id'    => $customerId,
            'product_id'     => $productId,
            'alternative_id' => $alternativeId,
            'status'         => 'deal',
            'stage'          => 'deal',
            'old_stage'      => 'accepted',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return [$dealId, $lplId];
    }

    /**
     * BUG-1b Kern-Test — ATOMARITAET: ein halber Storno ist unmoeglich.
     *
     * Ausloesung des Fehlers MITTEN in der Transaktion: ein Query-Listener wirft beim LETZTEN
     * Schreibvorgang (UPDATE auf lead_product_lists, Schritt 3 in junk()). DB::listen-Callbacks laufen
     * synchron im selben Call-Stack direkt nach der Query -> die Exception propagiert aus dem ->update()
     * hoch bis zur DB::transaction, die zurueckrollt und die Exception weiterreicht.
     * Da der Fehler ERST beim 3. Write feuert, sind Status-Wechsel (Schritt 1: deals) UND Rechnungs-Storno
     * (Schritt 2: invoices) bereits in der Transaktion angewandt — der Rollback MUSS beide rueckgaengig machen.
     *
     * (Query-Builder DB::table()->update() feuert KEINE Eloquent-Model-Events -> Model-Mocks scheiden aus,
     *  Query-Listener ist der einzig deterministische Weg ohne Produktivcode-Eingriff.)
     */
    public function test_junk_rollt_bei_fehler_mitten_in_der_transaktion_alles_zurueck(): void
    {
        [$dealId, $lplId] = $this->makeDealWithLeadStage();
        $offen   = $this->makeInvoice($dealId, 'versendet', 0.0, 1000.00);
        $bezahlt = $this->makeInvoice($dealId, 'bezahlt', 500.00, 500.00);

        // Ausgangs-DB-Zustand festhalten (fuer scharfe Unveraendert-Asserts nach dem Rollback).
        $this->assertSame('deal', (string) DB::table('deals')->where('id', $dealId)->value('status'));
        $this->assertSame('versendet', $this->invStatus($offen));
        $this->assertSame('bezahlt', $this->invStatus($bezahlt));
        $this->assertSame('deal', (string) DB::table('lead_product_lists')->where('id', $lplId)->value('status'));
        $this->assertSame('deal', (string) DB::table('lead_product_lists')->where('id', $lplId)->value('stage'));

        // Listener wird ueber ein Flag scharf geschaltet und im finally wieder inert gestellt, damit er
        // nachfolgende Tests im selben Prozess nicht beeinflusst (DB::listen hat keine unlisten-API).
        $armed = true;
        DB::listen(function ($q) use (&$armed) {
            if ($armed && stripos($q->sql, 'update `lead_product_lists`') !== false) {
                throw new \RuntimeException('simulierter Storno-Fehler nach Rechnungs-Storno');
            }
        });

        $this->withoutExceptionHandling();
        try {
            $this->actingAs($this->admin())->get('/deal_junk/' . $dealId);
            $this->fail('Erwartete RuntimeException aus der Transaktion wurde nicht geworfen.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('simulierter Storno-Fehler', $e->getMessage());
        } finally {
            $armed = false;
        }

        // ROLLBACK-BEWEIS — alle drei betroffenen DB-Zustaende MUESSEN unveraendert sein (echte DB-Abfragen):
        // 1) deals: Status NICHT 'Junk' (Schritt 1 zurueckgerollt).
        $this->assertSame('deal', (string) DB::table('deals')->where('id', $dealId)->value('status'),
            'deals.status muss nach Rollback der Ausgangswert sein, NICHT Junk.');
        // 2) invoices: KEINE Rechnung storniert (Schritt 2 zurueckgerollt — sogar der schon ausgefuehrte Storno).
        $this->assertSame('versendet', $this->invStatus($offen),
            'Offene Rechnung muss nach Rollback unveraendert sein (NICHT storniert).');
        $this->assertSame('bezahlt', $this->invStatus($bezahlt),
            'Bezahlte Rechnung muss nach Rollback unveraendert sein (NICHT storniert_bezahlt_pruefen).');
        // 3) lead_product_lists: Stufe unveraendert (Schritt 3 nie committet).
        $this->assertSame('deal', (string) DB::table('lead_product_lists')->where('id', $lplId)->value('status'),
            'lead_product_lists.status muss nach Rollback unveraendert bleiben.');
        $this->assertSame('deal', (string) DB::table('lead_product_lists')->where('id', $lplId)->value('stage'),
            'lead_product_lists.stage muss nach Rollback unveraendert bleiben.');
    }

    /**
     * BUG-1b — WARNTEXT: bei >=1 bezahlter Rechnung wird die Session-Warnung wirklich ausgegeben
     * (nicht nur der Status gesetzt). Gegenprobe: ohne bezahlte Rechnung KEINE Warnung.
     */
    public function test_junk_gibt_warnung_bei_bezahlter_rechnung_und_keine_ohne(): void
    {
        // Mit bezahlter Rechnung -> Warnung erwartet.
        $dealId = $this->makeDeal();
        $this->makeInvoice($dealId, 'bezahlt', 500.00, 500.00);

        $res = $this->actingAs($this->admin())->get('/deal_junk/' . $dealId);
        $res->assertRedirect();
        $res->assertSessionHas('warning');
        $warning = (string) session('warning');
        $this->assertStringContainsString('bezahlte Rechnung', $warning, 'Warnung muss auf bezahlte Rechnung(en) hinweisen.');
        $this->assertStringContainsString('1 bezahlte Rechnung', $warning, 'Warnung muss die Anzahl (1) enthalten.');
        $this->assertStringContainsString('500,00', $warning, 'Warnung muss die Summe (500,00) enthalten.');
        $this->assertSame('Junk', (string) DB::table('deals')->where('id', $dealId)->value('status'));

        // Gegenprobe: nur offene Rechnung -> KEINE Warnung.
        $dealOhne = $this->makeDeal();
        $this->makeInvoice($dealOhne, 'versendet', 0.0, 800.00);

        $res2 = $this->actingAs($this->admin())->get('/deal_junk/' . $dealOhne);
        $res2->assertRedirect();
        $res2->assertSessionMissing('warning');
    }

    /**
     * BUG-1b — MEHRFACH-BEZAHLT: Deal mit 2 bezahlten + 1 offenen Rechnung.
     * Nach junk: beide bezahlten -> 'storniert_bezahlt_pruefen', offene -> 'storniert';
     * die Warnung deckt ALLE bezahlten ab (paid_count=2, Summe = Summe ihrer total_amount).
     */
    public function test_junk_mit_mehreren_bezahlten_rechnungen_storniert_alle_und_warnt_summiert(): void
    {
        $dealId   = $this->makeDeal();
        $bezahltA = $this->makeInvoice($dealId, 'bezahlt', 500.00, 500.00);
        $bezahltB = $this->makeInvoice($dealId, 'teilbezahlt', 200.00, 700.00);
        $offen    = $this->makeInvoice($dealId, 'versendet', 0.0, 300.00);

        $res = $this->actingAs($this->admin())->get('/deal_junk/' . $dealId);
        $res->assertRedirect();

        // Alle bezahlten (paid_amount>0) zur Pruefung markiert, offene hart storniert.
        $this->assertSame('storniert_bezahlt_pruefen', $this->invStatus($bezahltA));
        $this->assertSame('storniert_bezahlt_pruefen', $this->invStatus($bezahltB));
        $this->assertSame('storniert', $this->invStatus($offen));
        $this->assertSame('Junk', (string) DB::table('deals')->where('id', $dealId)->value('status'));
        $this->assertSame(0.0, $this->offenePostenSumme($dealId));

        // Warnung deckt alle bezahlten ab: paid_count=2, Summe = 500,00 + 700,00 = 1.200,00.
        $res->assertSessionHas('warning');
        $warning = (string) session('warning');
        $this->assertStringContainsString('2 bezahlte Rechnung', $warning, 'paid_count (2) muss in der Warnung stehen.');
        $this->assertStringContainsString('1.200,00', $warning, 'Summe der total_amount aller bezahlten (1.200,00) muss in der Warnung stehen.');
    }
}
