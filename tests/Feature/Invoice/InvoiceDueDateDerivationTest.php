<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * A3 — Zentrale due_date-Ableitung: due_date = issue_date + Invoice::ZAHLUNGSZIEL_TAGE (Kalendertage),
 * aber NUR wenn due_date leer ist. Ein explizit gesetztes due_date wird NIEMALS ueberschrieben
 * (Operanden-Gate). Die Regel lebt EINMAL im Invoice-Model-saving-Hook und deckt damit alle
 * Erzeugungs-Pfade (heutige + kuenftige), die Eloquent nutzen.
 *
 * Schema-Befunde (empirisch, MySQL lokal, siehe JunkStornoTest):
 *  - invoices NOT NULL ohne Default: customer_id, type, issue_date. status Default 'draft'.
 *  - FK invoices.customer_id -> new_leads. Fixtures: new_leads-Zeile via customer_type='private'.
 *
 * DatabaseTransactions: alles rollt nach jedem Test zurueck; keine Bestandsdaten beruehrt.
 */
class InvoiceDueDateDerivationTest extends TestCase
{
    use DatabaseTransactions;

    private int $customerId;

    protected function setUp(): void
    {
        parent::setUp();
        // new_leads: einziges Pflichtfeld ist customer_type (Rest nullable/default).
        $this->customerId = DB::table('new_leads')->insertGetId(['customer_type' => 'private']);
    }

    private function admin(): User
    {
        // is_admin-Bypass -> passiert InvoiceMiddleware; CSRF ist in Feature-Tests aus.
        // Name bewusst NICHT-numerisch: InvoiceController::employeeId() liefert dann null
        // (statt einer nicht existierenden employees.id), so bleibt der created_by-FK sauber.
        return User::factory()->create([
            'password' => 'password',
            'name'     => 'due-date-admin-' . random_int(1, 9999),
            'is_admin' => true,
        ]);
    }

    private function baseAttributes(array $overrides = []): array
    {
        return array_merge([
            'customer_id' => $this->customerId,
            'type' => 'Rechnung',
            'status' => 'draft',
            'currency' => 'EUR',
            'subtotal' => 100, 'tax_rate' => 19, 'tax_amount' => 19, 'total_amount' => 119,
            'paid_amount' => 0,
        ], $overrides);
    }

    /** 1) Ableitung: ohne due_date -> issue_date + 14 Kalendertage. Freitag bleibt +14 (kein Werktags-Skip). */
    public function test_leitet_due_date_ab_wenn_leer(): void
    {
        // 2026-01-02 ist ein Freitag; +14 Kalendertage = 2026-01-16 (ebenfalls Freitag).
        $issue = '2026-01-02';
        $this->assertSame('Friday', Carbon::parse($issue)->format('l'));

        $invoice = Invoice::create($this->baseAttributes([
            'issue_date' => $issue,
            // KEIN due_date
        ]));

        $this->assertSame(
            '2026-01-16',
            $invoice->fresh()->due_date->toDateString(),
            'due_date muss issue_date + 14 Kalendertage sein (kein Werktags-/Feiertags-Skip).'
        );
        $this->assertSame(14, (int) Carbon::parse($issue)->diffInDays($invoice->fresh()->due_date));
    }

    /**
     * A3-DELTA) Gutschrift + Stornorechnung tragen KEIN Kunden-Zahlungsziel -> keine Ableitung.
     * Gegenprobe: ein normaler Rechnung-Typ bekommt WEITER +14 (Ausnahme nicht zu breit).
     * Typ-Strings exakt wie im Code (InvoiceController $financialTypes: 'Gutschrift', 'Stornorechnung').
     */
    public function test_gutschrift_und_stornorechnung_ohne_zahlungsziel(): void
    {
        $issue = '2026-01-02';

        $gutschrift = Invoice::create($this->baseAttributes([
            'type' => 'Gutschrift',
            'issue_date' => $issue,
            // KEIN due_date
        ]));
        $this->assertNull(
            $gutschrift->fresh()->due_date,
            'Gutschrift darf kein Zahlungsziel ableiten.'
        );

        $storno = Invoice::create($this->baseAttributes([
            'type' => 'Stornorechnung',
            'issue_date' => $issue,
        ]));
        $this->assertNull(
            $storno->fresh()->due_date,
            'Stornorechnung darf kein Zahlungsziel ableiten.'
        );

        // Gegen-Assert: normaler Rechnung-Typ bekommt weiter +14 -> Ausnahme greift nicht zu breit.
        $rechnung = Invoice::create($this->baseAttributes([
            'type' => 'Rechnung',
            'issue_date' => $issue,
        ]));
        $this->assertSame('2026-01-16', $rechnung->fresh()->due_date->toDateString());
    }

    /** 2) Operanden-Gate: explizit gesetztes due_date bleibt exakt erhalten (NICHT auf +14 veraendert). */
    public function test_explizites_due_date_wird_nie_ueberschrieben(): void
    {
        $issue = '2026-01-02';
        $explicit = Carbon::parse($issue)->addDays(30)->toDateString(); // 2026-02-01

        $invoice = Invoice::create($this->baseAttributes([
            'issue_date' => $issue,
            'due_date' => $explicit,
        ]));

        $this->assertSame($explicit, $invoice->fresh()->due_date->toDateString());
        // Gegenprobe: es ist NICHT die 14-Tage-Ableitung.
        $this->assertNotSame(
            Carbon::parse($issue)->addDays(Invoice::ZAHLUNGSZIEL_TAGE)->toDateString(),
            $invoice->fresh()->due_date->toDateString()
        );
    }

    /** 3) Update (reines Status-Update via Eloquent) veraendert bestehendes due_date NICHT. */
    public function test_update_aendert_bestehendes_due_date_nicht(): void
    {
        $issue = '2026-01-02';
        $explicit = Carbon::parse($issue)->addDays(30)->toDateString();

        $invoice = Invoice::create($this->baseAttributes([
            'issue_date' => $issue,
            'due_date' => $explicit,
        ]));

        $invoice->status = 'sent';
        $invoice->save();

        $this->assertSame($explicit, $invoice->fresh()->due_date->toDateString());
    }

    /**
     * 4a) InvoiceController@store (HTTP): Rechnung ohne due_date -> issue_date + 14.
     * Deckt den regulaeren Store-Pfad ueber die echte Route ab.
     */
    public function test_invoice_controller_store_leitet_ab(): void
    {
        $issue = '2026-01-02';

        $response = $this->actingAs($this->admin())->postJson(route('admin.invoices.store'), [
            'customer_id' => $this->customerId,
            'type' => 'Rechnung',
            'status' => 'draft',
            'issue_date' => $issue,
            // KEIN due_date
            'items' => [[
                'title' => 'Position A',
                'qty' => 1,
                'unit_price' => 100,
            ]],
        ]);

        $response->assertOk()->assertJson(['ok' => true]);

        $invoice = Invoice::find($response->json('invoice_id'));
        $this->assertSame('2026-01-16', $invoice->due_date->toDateString());
    }

    /**
     * 4b) Canvas-Pfad ueber den direkten Model-Create, exakt wie InvoiceCanvasController@storeDraftFromOfferDetail
     * nach der A3-Aenderung 'due_date' => $data['due_date'] ?? null reicht. Bewusst NICHT ueber HTTP:
     * der Canvas-Store setzt einen vollstaendigen OfferDetail-Graphen (offer/customer/alternative/branch)
     * voraus; die A3-relevante Aenderung ist die eine `?? null`-Durchreichung, deren Ergebnis der
     * Model-saving-Hook identisch ableitet. Beide Pfade liefern damit dasselbe due_date.
     */
    public function test_canvas_pfad_leitet_identisch_ab(): void
    {
        $issue = '2026-01-02';

        // Spiegelt den (geaenderten) Canvas-Store: due_date wird als null durchgereicht.
        $canvasDue = null;

        $invoice = Invoice::create($this->baseAttributes([
            'status' => 'draft',
            'issue_date' => $issue,
            'due_date' => $canvasDue ?? null,
        ]));

        $this->assertSame('2026-01-16', $invoice->fresh()->due_date->toDateString());
    }

    /**
     * 5) Randfall: leeres issue_date -> keine Ableitung, kein Crash, due_date bleibt null.
     *
     * Schema-Befund: invoices.issue_date ist NOT NULL, ein echtes Persistieren mit null wird von
     * der DB (zu Recht) abgelehnt. Der A3-relevante Punkt ist die IN-MEMORY-Guard im saving-Hook:
     * bei leerem issue_date darf er kein due_date erfinden. Das wird hier isoliert geprueft, indem
     * das eloquent.saving-Event fuer die (unpersistierte) Instanz gefeuert wird — genau der Moment,
     * in dem der Hook laeuft. Kein Crash, due_date bleibt null.
     */
    public function test_ohne_issue_date_keine_ableitung(): void
    {
        $invoice = new Invoice($this->baseAttributes([
            'issue_date' => null,
            'due_date' => null,
        ]));
        $invoice->exists = false;

        // Feuert die saving-Listener (inkl. A3-Ableitung + Nummern-Hook) ohne DB-Zugriff.
        event('eloquent.saving: ' . Invoice::class, $invoice);

        $this->assertNull($invoice->issue_date, 'issue_date bleibt leer.');
        $this->assertNull($invoice->due_date, 'Ohne issue_date darf kein due_date abgeleitet werden.');
    }
}
