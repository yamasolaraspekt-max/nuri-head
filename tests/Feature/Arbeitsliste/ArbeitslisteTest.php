<?php

namespace Tests\Feature\Arbeitsliste;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Arbeitsliste — Inbox „Was braucht mich jetzt?" (server-gerendert).
 *
 * Fährt den echten Stack: HTTP-GET /arbeitsliste als eingeloggter Admin -> Route -> Controller ->
 * View. Prueft die drei Sektions-Ueberschriften, mit Fixtures die Pills MIT Text, den rechtsbuendigen
 * Betrag als Zahl, und den Leer-Zustand ohne Fixtures.
 *
 * Schema-Muster wie bestehende Accounting/Security-Tests: synthetische FK-IDs, daher
 * FOREIGN_KEY_CHECKS kurz aus; DatabaseTransactions raeumt auf. is_admin-Bypass fuer Auth.
 */
class ArbeitslisteTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }

    protected function tearDown(): void
    {
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

    /** Kunde in new_leads (fuer Kundenname im Meta). */
    private function makeCustomer(string $name = 'Testkunde'): int
    {
        return DB::table('new_leads')->insertGetId([
            'name'          => $name,
            'customer_type' => 'private',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    /** Ueberfaellige, unbezahlte Rechnung (due_date in der Vergangenheit). */
    private function makeOverdueInvoice(int $customerId): int
    {
        return DB::table('invoices')->insertGetId([
            'customer_id'  => $customerId,
            'type'         => 'invoice',
            'status'       => 'sent',
            'issue_date'   => now()->subDays(30)->toDateString(),
            'due_date'     => now()->subDays(10)->toDateString(),
            'total_amount' => 1234.56,
            'paid_amount'  => 0,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    /** Offene Angebots-Aufgabe (A1 Follow-up auf personal_tasks). */
    private function makeFollowUpTask(int $customerId): int
    {
        return DB::table('personal_tasks')->insertGetId([
            'task_title'  => 'Angebot für Vorgang',
            'type'        => 'follow_up',
            'source_type' => 'lead_product_list',
            'source_id'   => random_int(100000, 999999),
            'task_status' => 'open',
            'customer_id' => $customerId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    /** Auftrag ohne Rechnung (deals status='deal'). */
    private function makeDealWithoutInvoice(int $customerId): int
    {
        return DB::table('deals')->insertGetId([
            'customer_id'    => $customerId,
            'product_id'     => random_int(100000, 999999),
            'alternative_id' => random_int(100000, 999999),
            'service'        => 'Test',
            'employee_id'    => random_int(100000, 999999),
            'status'         => 'deal',
            'order_number'   => 'SA-TEST-01',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    public function test_daten_zustand_rendert_mit_drei_sektions_ueberschriften(): void
    {
        // Sektionen erscheinen im DATEN-Zustand (nach der Glättung sind sie im global-leeren
        // Zustand bewusst unterdrueckt) -> je eine Fixture, damit alle drei Sektionen rendern.
        $customerId = $this->makeCustomer('Max Mustermann');
        $this->makeOverdueInvoice($customerId);
        $this->makeFollowUpTask($customerId);
        $this->makeDealWithoutInvoice($customerId);

        $res = $this->actingAs($this->admin())->get('/arbeitsliste');

        $res->assertStatus(200);
        $res->assertSee('Überfällige Rechnungen', false);
        $res->assertSee('Offene Angebots-Aufgaben', false);
        $res->assertSee('Aufträge ohne Rechnung', false);
    }

    public function test_mit_fixtures_erscheinen_pills_mit_text_und_betrag(): void
    {
        $customerId = $this->makeCustomer('Max Mustermann');
        $this->makeOverdueInvoice($customerId);
        $this->makeFollowUpTask($customerId);
        $this->makeDealWithoutInvoice($customerId);

        $res = $this->actingAs($this->admin())->get('/arbeitsliste');

        $res->assertStatus(200);

        // Pills tragen Farbe UND Text.
        $res->assertSee('überfällig', false);
        $res->assertSee('Angebot fehlt', false);
        $res->assertSee('Rechnung fehlt', false);

        // Betrag als deutsche Zahl, in der rechtsbuendigen Betrags-Zelle (tabular-nums).
        $res->assertSee('al-row-amount', false);
        $res->assertSee('1.234,56 €', false);
    }

    public function test_leer_zustand_ohne_fixtures(): void
    {
        // Sicherstellen, dass keine passenden Bestandszeilen die Leer-Aussage kippen.
        $hasOverdue = DB::table('invoices')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereColumn('paid_amount', '<', 'total_amount')
            ->whereNotIn(DB::raw('LOWER(type)'), ['gutschrift', 'stornorechnung'])
            ->exists();

        $res = $this->actingAs($this->admin())->get('/arbeitsliste');

        $res->assertStatus(200);

        $hasFollowUp = DB::table('personal_tasks')->where('type', 'follow_up')
            ->where('source_type', 'lead_product_list')
            ->whereNotIn('task_status', ['completed', 'done', 'cancelled'])
            ->whereNull('deleted_at')->exists();
        $hasDeal = DB::table('deals')->where('status', 'deal')
            ->whereNotExists(fn ($q) => $q->from('invoices')->whereColumn('invoices.deal_id', 'deals.id')->whereNull('invoices.deleted_at'))
            ->exists();

        if (!$hasOverdue && !$hasFollowUp && !$hasDeal) {
            // Glättung: genau EIN Empty-Block (der globale), Sektionen unterdrueckt.
            $res->assertSee('Nichts offen', false);                       // handlungsleitender globaler Empty-State
            $res->assertSee('Neue Posten erscheinen hier automatisch', false); // sagt, was passiert (nicht „keine Daten")
            $res->assertDontSee('Überfällige Rechnungen', false);         // Sektions-Ueberschrift unterdrueckt
            $res->assertDontSee('Aufträge ohne Rechnung', false);
        } else {
            $this->markTestSkipped('Bestands-Daten enthalten passende Zeilen; Leer-Fall nicht isolierbar.');
        }
    }
}
