<?php

namespace Tests\Feature\Invoice;

use App\Exceptions\InvoiceStatusTransitionException;
use App\Models\Invoice;
use App\Services\Invoice\InvoiceNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * S1-01 — Transaktionaler, lückenarmer Rechnungsnummernkreis.
 *
 * WICHTIG: läuft mit RefreshDatabase -> ausschließlich gegen eine ISOLIERTE Test-DB
 * (z. B. ticket_test). Vor dem Lauf erfolgt eine harte Guard-Prüfung, dass die
 * Testverbindung NICHT auf die echte DB `ticket` zeigt.
 *
 * Selbstversorgend: erzeugt den benötigten new_leads-Datensatz (FK invoices.customer_id)
 * selbst, setzt keine Bestandsdaten voraus und nutzt keinen Seeder mit Nummernlogik.
 */
class InvoiceNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    private int $customerId;

    protected function setUp(): void
    {
        parent::setUp();
        // new_leads: einziges Pflichtfeld ist customer_type (Rest nullable/default).
        $this->customerId = DB::table('new_leads')->insertGetId(['customer_type' => 'private']);
    }

    private function makeInvoice(string $status = 'draft'): Invoice
    {
        return (new Invoice())->forceFill([
            'customer_id' => $this->customerId,
            'type' => 'Rechnung',
            'status' => $status,
            'issue_date' => now()->toDateString(),
            'currency' => 'EUR',
            'subtotal' => 100, 'tax_rate' => 19, 'tax_amount' => 19, 'total_amount' => 119,
        ]);
    }

    private function currentYy(): string
    {
        return now()->format('y');
    }

    /** Draft bekommt keine Rechnungsnummer. */
    public function test_draft_bekommt_keine_nummer(): void
    {
        $inv = $this->makeInvoice('draft');
        $inv->save();

        $this->assertNull($inv->fresh()->invoice_no);
    }

    /** Finalisierung erzeugt genau eine Nummer im Format RE-yy####, Sequenz startet bei 0001. */
    public function test_finalisierung_erzeugt_genau_eine_nummer_und_format(): void
    {
        $inv = $this->makeInvoice('sent');
        $inv->save();

        $no = (string) $inv->fresh()->invoice_no;
        $this->assertMatchesRegularExpression('/^RE-' . $this->currentYy() . '\d{4}$/', $no);
        $this->assertSame('RE-' . $this->currentYy() . '0001', $no);
        $this->assertSame(1, Invoice::where('invoice_no', $no)->count());
    }

    /** Wiederholte Finalisierung / Folgestatus vergibt keine neue Nummer. */
    public function test_nummernvergabe_ist_idempotent(): void
    {
        $inv = $this->makeInvoice('sent');
        $inv->save();
        $first = $inv->fresh()->invoice_no;

        $inv->status = 'paid';
        $inv->save();

        $this->assertSame($first, $inv->fresh()->invoice_no);
    }

    /** Sequenz zählt bei mehreren Ausstellungen korrekt hoch. */
    public function test_sequenz_zaehlt_hoch(): void
    {
        $a = $this->makeInvoice('sent'); $a->save();
        $b = $this->makeInvoice('sent'); $b->save();

        $na = (int) substr((string) $a->fresh()->invoice_no, -4);
        $nb = (int) substr((string) $b->fresh()->invoice_no, -4);

        $this->assertSame(1, $na);
        $this->assertSame(2, $nb);
    }

    /** sent -> draft ist gesperrt (Rücknahme nur via Storno). */
    public function test_sent_zu_draft_ist_gesperrt(): void
    {
        $inv = $this->makeInvoice('sent');
        $inv->save();

        $this->expectException(InvoiceStatusTransitionException::class);
        $inv->status = 'draft';
        $inv->save();
    }

    /** Mehrere NULL-Drafts sind erlaubt (Unique-Index toleriert NULL). */
    public function test_mehrere_null_drafts_erlaubt(): void
    {
        $this->makeInvoice('draft')->save();
        $this->makeInvoice('draft')->save();

        $this->assertSame(2, Invoice::whereNull('invoice_no')->count());
    }

    /** Unique-Schutz greift bei bereits vergebenen Nummern. */
    public function test_unique_schutz_bei_vergebenen_nummern(): void
    {
        $a = $this->makeInvoice('sent');
        $a->save();
        $no = (string) $a->fresh()->invoice_no;

        $this->expectException(\Illuminate\Database\QueryException::class);
        // Nummer manuell doppelt setzen -> Hook überspringt (nicht leer) -> DB-Unique greift.
        $dup = $this->makeInvoice('sent');
        $dup->invoice_no = $no;
        $dup->save();
    }

    /** Nummernreservierung ist Teil der umgebenden Transaktion (Atomarität). */
    public function test_reservierung_und_speichern_atomar(): void
    {
        $countBefore = Invoice::count();

        try {
            DB::transaction(function () {
                $inv = $this->makeInvoice('sent');
                $inv->save();
                $this->assertNotNull($inv->invoice_no);          // Nummer reserviert ...
                throw new \RuntimeException('__rollback__');       // ... dann Abbruch
            });
        } catch (\RuntimeException $e) {
            $this->assertSame('__rollback__', $e->getMessage());
        }

        // Weder Rechnung noch Sequenz-Fortschreibung dürfen überleben.
        $this->assertSame($countBefore, Invoice::count());
        $this->assertSame(0, DB::table('invoice_number_ranges')->count());
    }

    /** Startwert ignoriert Fremdformate (Altbestand bleibt unberührt). */
    public function test_startwert_ignoriert_fremdformate(): void
    {
        DB::table('invoices')->insert([
            'customer_id' => $this->customerId,
            'invoice_no' => 'RE-' . now()->format('Y') . '-00999', // Fremdformat RE-2026-#####
            'type' => 'final', 'status' => 'paid', 'issue_date' => now()->toDateString(),
            'currency' => 'EUR', 'subtotal' => 0, 'tax_rate' => 19, 'tax_amount' => 0, 'total_amount' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $inv = $this->makeInvoice('sent');
        $inv->save();

        $this->assertSame('RE-' . $this->currentYy() . '0001', (string) $inv->fresh()->invoice_no);
    }

    /** Legacy-Vergabestellen existieren nicht mehr (kein zweiter Nummernpfad). */
    public function test_keine_legacy_makeinvoiceno(): void
    {
        $this->assertFalse(method_exists(Invoice::class, 'makeInvoiceNo'));
        $this->assertFalse(method_exists(\App\Http\Controllers\Invoice\InvoiceCanvasController::class, 'makeInvoiceNo'));
    }

    /** Der Demo-Seeder enthält keine eigene Rechnungsnummernlogik mehr (D2-A). */
    public function test_seeder_ohne_eigene_nummernlogik(): void
    {
        $src = file_get_contents(base_path('database/seeders/DemoCrmPipelineSeeder.php'));

        $this->assertStringNotContainsString("'RE-' . date(", $src);
        $this->assertStringNotContainsString('makeInvoiceNo', $src);
    }

    /** reserve() vergibt nur für ausgestellte Status, nie für Drafts. */
    public function test_service_reserve_nur_fuer_ausgestellte(): void
    {
        $svc = app(InvoiceNumberService::class);

        $this->assertNull($svc->reserve($this->makeInvoice('draft')));
        $this->assertNotNull($svc->reserve($this->makeInvoice('overdue')));
    }
}
