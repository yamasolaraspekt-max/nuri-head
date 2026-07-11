<?php

namespace Tests\Feature\Arbeitsliste;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Arbeitsliste — Robustheit am ECHTEN Stack (kein gemockter View-Zustand).
 *
 * Beweist die Fehler-Isolation je Kategorie am realen Weg: EINE Kategorie-Query wirft WIRKLICH
 * (ueber einen DB::listen-Wächter, der GENAU die followup-Query trifft — die einzige personal_tasks-
 * Query des Requests mit source_type + task_status). Erwartung: kein 500, sondern die try/catch-
 * Kapselung in ArbeitslisteController::category() faengt den echten Fehler; nur die followup-Sektion
 * zeigt ihren begrenzten role="alert", die zwei anderen Sektionen rendern Daten/Leere WEITER.
 *
 * Gegen-Beweis fuer den Evaluator: Entfernt man das try/catch in category(), wird dieser Test ROT
 * (die RuntimeException schlaegt bis zum Framework durch -> 500).
 *
 * DB::listen-Leak: der Listener wird je setUp registriert, feuert aber NUR wenn self::$armed=true
 * (statisches, klassen-lokales Flag, im Test gesetzt/zurueckgesetzt). Andere Testklassen setzen es
 * nie -> keine Fremdwirkung. DatabaseTransactions raeumt die Zeilen auf.
 */
class ArbeitslisteResilienceTest extends TestCase
{
    use DatabaseTransactions;

    /** Nur wenn true feuert der Wächter — verhindert Streuwirkung auf andere Tests/Queries. */
    private static bool $armed = false;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::listen(function ($query): void {
            if (self::$armed
                && str_contains($query->sql, 'personal_tasks')
                && str_contains($query->sql, 'source_type')
                && str_contains($query->sql, 'task_status')
            ) {
                throw new \RuntimeException('kat-fehler (Test-Wächter): followup-Query bewusst geworfen');
            }
        });
    }

    protected function tearDown(): void
    {
        self::$armed = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        parent::tearDown();
    }

    private function admin(): User
    {
        return User::factory()->create([
            'password' => 'password',
            'name'     => (string) random_int(1, 9999),
            'is_admin' => true,
        ]);
    }

    public function test_eine_kategorie_query_wirft_am_echten_stack_isoliert_bleibt_der_rest(): void
    {
        // 1 Kunde + 1 ueberfaellige Rechnung -> overdue rendert DATEN (al-list).
        $customerId = DB::table('new_leads')->insertGetId([
            'name' => 'Resilienz', 'customer_type' => 'private',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('invoices')->insert([
            'customer_id' => $customerId, 'type' => 'invoice', 'status' => 'sent',
            'issue_date' => now()->subDays(30)->toDateString(),
            'due_date' => now()->subDays(10)->toDateString(),
            'total_amount' => 500, 'paid_amount' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // deals deterministisch LEER machen: jeder real qualifizierende Auftrag (status=deal, ohne
        // nicht-geloeschte Rechnung) bekommt eine ADDITIVE, NICHT-ueberfaellige Rechnung (paid, kein
        // due_date) -> er faellt aus der deals-Sektion, taucht aber NICHT in overdue auf. Rein additiv,
        // in der Test-Transaktion, wird zurueckgerollt (DAUERDIREKTIVE: keine Bestandsdaten geaendert).
        $qualifyingDeals = DB::table('deals')->where('status', 'deal')
            ->whereNotExists(fn ($q) => $q->from('invoices')
                ->whereColumn('invoices.deal_id', 'deals.id')->whereNull('invoices.deleted_at'))
            ->pluck('id');
        foreach ($qualifyingDeals as $dealId) {
            DB::table('invoices')->insert([
                'customer_id' => $customerId, 'deal_id' => $dealId, 'type' => 'invoice',
                'status' => 'paid', 'issue_date' => now()->toDateString(),
                'total_amount' => 0, 'paid_amount' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        self::$armed = true;
        $res = $this->actingAs($this->admin())->get('/arbeitsliste');
        self::$armed = false;

        $html = $res->getContent();

        // KEIN 500 — die category()-Isolation faengt den echten Fehler.
        $res->assertStatus(200);

        // GENAU EIN role="alert" (nur die followup-Sektion), KEIN globaler Fehler-Panel.
        $this->assertSame(1, substr_count($html, 'role="alert"'));
        $this->assertSame(0, substr_count($html, 'class="al-error al-error-panel"'));

        // Alle drei Sektions-Ueberschriften da (Teilausfall kippt die Seite nicht).
        $this->assertStringContainsString('Überfällige Rechnungen', $html);
        $this->assertStringContainsString('Offene Angebots-Aufgaben', $html);
        $this->assertStringContainsString('Aufträge ohne Rechnung', $html);

        // overdue -> gefuellte Liste; deals -> leise Inline-Zeile; kein globaler Empty-Block.
        $this->assertGreaterThanOrEqual(1, substr_count($html, 'class="al-list"'));
        $this->assertGreaterThanOrEqual(1, substr_count($html, 'class="al-section-empty"'));
        $this->assertSame(0, substr_count($html, 'class="al-empty"'));
    }
}
