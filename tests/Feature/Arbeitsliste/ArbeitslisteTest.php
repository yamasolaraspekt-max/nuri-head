<?php

namespace Tests\Feature\Arbeitsliste;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Arbeitsliste — Inbox „Was braucht mich jetzt?" (server-gerendert).
 *
 * Prueft die EINE Zustandsmaschine ($viewState: loading|data|empty|error): die View rendert
 * immer genau EINEN Zustand. Daten -> Sektionen; einzeln leere Sektion -> leise Inline-Zeile;
 * komplett leer -> GENAU ein globaler, positiver Empty-State (role="status"); Fehler -> nur ein
 * role="alert"-Panel, keine Empty-States.
 *
 * Schema-Muster wie bestehende Tests: synthetische FK-IDs, FOREIGN_KEY_CHECKS kurz aus;
 * DatabaseTransactions raeumt auf. is_admin-Bypass fuer Auth.
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

    private function makeCustomer(string $name = 'Testkunde'): int
    {
        return DB::table('new_leads')->insertGetId([
            'name'          => $name,
            'customer_type' => 'private',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

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

    /** Sind ausser den Test-Fixtures schon passende Bestandszeilen da? (Leer-Fall isolierbar?) */
    private function hasAnyRealItems(): bool
    {
        $overdue = DB::table('invoices')->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereColumn('paid_amount', '<', 'total_amount')
            ->whereNotIn(DB::raw('LOWER(type)'), ['gutschrift', 'stornorechnung'])->exists();
        $followUp = DB::table('personal_tasks')->where('type', 'follow_up')
            ->where('source_type', 'lead_product_list')
            ->whereNotIn('task_status', ['completed', 'done', 'cancelled'])
            ->whereNull('deleted_at')->exists();
        $deal = DB::table('deals')->where('status', 'deal')
            ->whereNotExists(fn ($q) => $q->from('invoices')->whereColumn('invoices.deal_id', 'deals.id')->whereNull('invoices.deleted_at'))
            ->exists();

        return $overdue || $followUp || $deal;
    }

    public function test_daten_zustand_rendert_mit_drei_sektions_ueberschriften(): void
    {
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
        // Betrag als deutsche Zahl, rechtsbuendige Betrags-Zelle (tabular-nums).
        $res->assertSee('al-row-amount', false);
        $res->assertSee('1.234,56 €', false);
    }

    /** DATEN mit EINZELN leerer Sektion: nur die leise Inline-Zeile, NICHT der grosse globale Block. */
    public function test_einzeln_leere_sektion_zeigt_leise_inline_zeile(): void
    {
        $customerId = $this->makeCustomer('Max Mustermann');
        $this->makeOverdueInvoice($customerId); // nur Kategorie 1 hat Items -> data-Zustand

        if ($this->hasAnyRealItems() && false) {
            // (Guard nur zur Doku; makeOverdueInvoice sichert den data-Zustand ohnehin.)
        }

        $res = $this->actingAs($this->admin())->get('/arbeitsliste');
        $html = $res->getContent();

        $res->assertStatus(200);
        // Gefuellte Sektion -> Liste; leere Sektionen -> leise Inline-Zeile (Element, nicht CSS-Regel).
        $this->assertStringContainsString('class="al-list"', $html);
        $this->assertStringContainsString('class="al-section-empty"', $html);
        // NICHT der grosse globale Empty-Block und KEIN Alert im data-Zustand.
        $this->assertStringNotContainsString('class="al-empty"', $html);
        $this->assertStringNotContainsString('role="alert"', $html);
    }

    /** LEER = ERFOLG: genau EIN globaler Block, role="status", positiver Text, Sektionen unterdrueckt. */
    public function test_leer_zustand_genau_ein_positiver_status_block(): void
    {
        if ($this->hasAnyRealItems()) {
            $this->markTestSkipped('Bestands-Daten enthalten passende Zeilen; Leer-Fall nicht isolierbar.');
        }

        $res = $this->actingAs($this->admin())->get('/arbeitsliste');
        $html = $res->getContent();

        $res->assertStatus(200);
        // Genau EIN globaler Empty-Block.
        $this->assertSame(1, substr_count($html, 'class="al-empty"'));
        // Positiv + handlungsleitend (nicht „keine Daten"), hoeflich (role="status"), KEIN alert.
        $res->assertSee('Nichts offen — du bist auf dem Stand.', false);
        $res->assertSee('Neue Posten erscheinen hier automatisch', false);
        $this->assertStringContainsString('role="status"', $html);
        $this->assertStringNotContainsString('role="alert"', $html);
        // Sektionen unterdrueckt.
        $res->assertDontSee('Überfällige Rechnungen', false);
        $res->assertDontSee('Aufträge ohne Rechnung', false);
    }

    /** GESAMTAUSFALL (alle Kategorien errored): ein globaler role="alert"-Panel, keine Sektionen. */
    public function test_gesamtausfall_globaler_alert_keine_sektionen(): void
    {
        $this->actingAs($this->admin());

        $html = view('admin.arbeitsliste.index', [
            'viewState'       => 'error',
            'overdueInvoices' => ['ok' => false, 'items' => [], 'error' => 'x'],
            'followUpTasks'   => ['ok' => false, 'items' => [], 'error' => 'x'],
            'dealsNoInvoice'  => ['ok' => false, 'items' => [], 'error' => 'x'],
        ])->render();

        $this->assertStringContainsString('class="al-error al-error-panel"', $html);
        $this->assertStringContainsString('role="alert"', $html);
        // Genau EIN globaler Alert-Panel, KEIN Empty-Block, KEINE Sektions-Ueberschrift.
        $this->assertSame(1, substr_count($html, 'class="al-error al-error-panel"'));
        $this->assertStringNotContainsString('class="al-empty"', $html);
        $this->assertStringNotContainsString('class="al-section-empty"', $html);
        $this->assertStringNotContainsString('Überfällige Rechnungen', $html);
    }

    /**
     * TEILAUSFALL (graceful degradation): GENAU EINE Kategorie errored -> nur DIESE Sektion zeigt
     * ihren begrenzten role="alert"; die anderen zwei rendern ihre Daten/Leere WEITER, kein globaler
     * Alert, kein globaler Erfolg. Der Sektions-Alert ist der In-Sektion-Streifen (.al-error OHNE
     * .al-error-panel).
     */
    public function test_teilausfall_nur_diese_sektion_faellt_rest_rendert(): void
    {
        $this->actingAs($this->admin());

        $html = view('admin.arbeitsliste.index', [
            'viewState'       => 'data',
            'overdueInvoices' => ['ok' => false, 'items' => [], 'error' => 'Boom-Kategorie-1'],
            'followUpTasks'   => ['ok' => true, 'items' => [
                ['title' => 'Angebot X', 'meta' => 'Kunde Y', 'amount' => null, 'href' => '#'],
            ]],
            'dealsNoInvoice'  => ['ok' => true, 'items' => []],
        ])->render();

        // Alle drei Sektionen rendern weiter (Ueberschriften da).
        $this->assertStringContainsString('Überfällige Rechnungen', $html);
        $this->assertStringContainsString('Offene Angebots-Aufgaben', $html);
        $this->assertStringContainsString('Aufträge ohne Rechnung', $html);
        // GENAU EIN Sektions-Alert (In-Sektion-Streifen), KEIN globaler Alert-Panel.
        $this->assertStringContainsString('Boom-Kategorie-1', $html);
        $this->assertSame(1, substr_count($html, 'role="alert"'));
        $this->assertStringNotContainsString('class="al-error al-error-panel"', $html);
        // Die anderen zwei rendern weiter: befuellte -> Liste, leere -> leise Inline-Zeile.
        $this->assertStringContainsString('class="al-list"', $html);
        $this->assertStringContainsString('class="al-section-empty"', $html);
        // KEIN globaler Erfolg (leer=Erfolg) bei Teilausfall.
        $this->assertStringNotContainsString('class="al-empty"', $html);
    }
}
