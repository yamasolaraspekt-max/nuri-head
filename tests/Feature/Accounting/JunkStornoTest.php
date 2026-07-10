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
}
