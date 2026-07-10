<?php

namespace Tests\Feature\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * A5 — „Voll bezahlt" als ABGELEITETE, SQL-filterbare EINE Wahrheit (KEIN gespeichertes Bool-Feld).
 * Regel: NICHT ausgenommener Typ UND paid_amount >= total_amount, decimal-exakt via bccomp/SQL.
 * Typ-Gate: Gutschrift/Stornorechnung (Invoice::TYPEN_OHNE_ZAHLUNG) nie „bezahlt".
 * Konsistent zum bestehenden open_amount-Accessor: open_amount == 0 ⇔ voll bezahlt (nicht-ausgenommen).
 *
 * Schema-Befunde (empirisch, MySQL lokal, wie InvoiceDueDateDerivationTest):
 *  - invoices NOT NULL ohne Default: customer_id, type, issue_date. status Default 'draft'.
 *  - total_amount/paid_amount = decimal(12,2). FK customer_id -> new_leads.
 *
 * DatabaseTransactions: alles rollt nach jedem Test zurueck; keine Bestandsdaten beruehrt.
 */
class InvoicePaidDerivationTest extends TestCase
{
    use DatabaseTransactions;

    private int $customerId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerId = DB::table('new_leads')->insertGetId(['customer_type' => 'private']);
    }

    private function makeInvoice(array $overrides = []): Invoice
    {
        return Invoice::create(array_merge([
            'customer_id' => $this->customerId,
            'type'        => 'Rechnung',
            'status'      => 'draft',
            'currency'    => 'EUR',
            'issue_date'  => '2026-01-02',
            'due_date'    => '2026-01-16',
            'subtotal'    => 100,
            'tax_rate'    => 0,
            'tax_amount'  => 0,
            'total_amount'=> 100,
            'paid_amount' => 0,
        ], $overrides));
    }

    /** 1) paid == total -> bezahlt; in scopePaid; open_amount == 0 (Konsistenz). */
    public function test_paid_gleich_total_ist_bezahlt(): void
    {
        $inv = $this->makeInvoice(['total_amount' => 100, 'paid_amount' => 100]);

        $this->assertTrue($inv->isFullyPaid());
        $this->assertTrue(Invoice::paid()->whereKey($inv->id)->exists());
        $this->assertSame(0.0, $inv->fresh()->open_amount, 'open_amount == 0 ⇔ voll bezahlt.');
    }

    /** 2) paid < total -> NICHT bezahlt; NICHT in scopePaid; open_amount > 0. */
    public function test_teilzahlung_ist_nicht_bezahlt(): void
    {
        $inv = $this->makeInvoice(['total_amount' => 100, 'paid_amount' => 40]);

        $this->assertFalse($inv->isFullyPaid());
        $this->assertFalse(Invoice::paid()->whereKey($inv->id)->exists());
        $this->assertTrue(Invoice::unpaid()->whereKey($inv->id)->exists());
        $this->assertGreaterThan(0, $inv->fresh()->open_amount);
    }

    /** 3) Ueberzahlung paid > total -> bezahlt (>=). */
    public function test_ueberzahlung_ist_bezahlt(): void
    {
        $inv = $this->makeInvoice(['total_amount' => 100, 'paid_amount' => 150]);

        $this->assertTrue($inv->isFullyPaid());
        $this->assertTrue(Invoice::paid()->whereKey($inv->id)->exists());
    }

    /** 4) total = 0 -> bezahlt (nichts offen, paid >= 0 immer wahr). */
    public function test_total_null_ist_bezahlt(): void
    {
        $inv = $this->makeInvoice(['subtotal' => 0, 'total_amount' => 0, 'paid_amount' => 0]);

        $this->assertTrue($inv->isFullyPaid());
        $this->assertTrue(Invoice::paid()->whereKey($inv->id)->exists());
        $this->assertSame(0.0, $inv->fresh()->open_amount);
    }

    /**
     * 5) Decimal-Exaktheit: total = 100.00, paid = 99.99. Als Float wuerde 0.1+0.2-artiger Drift
     * (bzw. 100.00 - 99.99 = 0.010000000000005) zu Fehlklassifikation fuehren; bccomp("99.99","100.00",2)
     * = -1 entscheidet exakt: NICHT bezahlt. Gegenprobe 100.00 vs 100.00 -> exakt bezahlt.
     */
    public function test_decimal_exaktheit(): void
    {
        // Beleg des reinen Vergleichs (unabhaengig von DB-Rundung):
        $this->assertSame(-1, bccomp('99.99', '100.00', 2));
        $this->assertSame(0, bccomp('100.00', '100.00', 2));

        $knapp = $this->makeInvoice(['total_amount' => 100.00, 'paid_amount' => 99.99]);
        $this->assertFalse($knapp->isFullyPaid(), '99.99 < 100.00 -> NICHT bezahlt, decimal-exakt.');
        $this->assertFalse(Invoice::paid()->whereKey($knapp->id)->exists());

        $exakt = $this->makeInvoice(['total_amount' => 100.00, 'paid_amount' => 100.00]);
        $this->assertTrue($exakt->isFullyPaid());
        $this->assertTrue(Invoice::paid()->whereKey($exakt->id)->exists());
    }

    /**
     * 6) Typ-Gate (Gegen-Beweis): Gutschrift/Stornorechnung mit paid >= total -> NICHT bezahlt
     * und NICHT in scopePaid; normale Rechnung GLEICHER Betraege -> bezahlt (Ausnahme nicht zu breit).
     */
    public function test_typ_gate_gutschrift_und_storno(): void
    {
        $gutschrift = $this->makeInvoice(['type' => 'Gutschrift', 'total_amount' => 100, 'paid_amount' => 100]);
        $storno     = $this->makeInvoice(['type' => 'Stornorechnung', 'total_amount' => 100, 'paid_amount' => 100]);
        $rechnung   = $this->makeInvoice(['type' => 'Rechnung', 'total_amount' => 100, 'paid_amount' => 100]);

        $this->assertFalse($gutschrift->isFullyPaid(), 'Gutschrift: kein Kunden-Zahlungsziel -> nie bezahlt.');
        $this->assertFalse($storno->isFullyPaid(), 'Stornorechnung: aufgehoben -> nie bezahlt.');
        $this->assertTrue($rechnung->isFullyPaid(), 'Normale Rechnung gleicher Betraege -> bezahlt.');

        $paidIds = Invoice::paid()->pluck('id');
        $this->assertFalse($paidIds->contains($gutschrift->id));
        $this->assertFalse($paidIds->contains($storno->id));
        $this->assertTrue($paidIds->contains($rechnung->id));
    }

    /**
     * 7) scopePaid ist echtes SQL (nicht Collection-Filter): mehrere Rechnungen anlegen,
     * Invoice::paid()->pluck('id') liefert EXAKT die erwartete Menge.
     */
    public function test_scope_paid_ist_echtes_sql(): void
    {
        $voll   = $this->makeInvoice(['total_amount' => 100, 'paid_amount' => 100]); // bezahlt
        $ueber  = $this->makeInvoice(['total_amount' => 100, 'paid_amount' => 120]); // bezahlt
        $teil   = $this->makeInvoice(['total_amount' => 100, 'paid_amount' => 30]);  // nicht
        $offen  = $this->makeInvoice(['total_amount' => 100, 'paid_amount' => 0]);   // nicht
        $gut    = $this->makeInvoice(['type' => 'Gutschrift', 'total_amount' => 100, 'paid_amount' => 100]); // Typ-Gate

        $ids = [$voll->id, $ueber->id, $teil->id, $offen->id, $gut->id];
        $paid = Invoice::paid()->whereIn('id', $ids)->pluck('id')->sort()->values()->all();

        $this->assertSame(
            collect([$voll->id, $ueber->id])->sort()->values()->all(),
            $paid,
            'scopePaid liefert exakt die betrags-bezahlten, nicht-ausgenommenen Rechnungen.'
        );
    }

    /** 8) isPaid() delegiert auf isFullyPaid() fuer mehrere Faelle (EINE Wahrheit). */
    public function test_ispaid_delegiert_auf_isfullypaid(): void
    {
        $cases = [
            $this->makeInvoice(['total_amount' => 100, 'paid_amount' => 100]),                       // bezahlt
            $this->makeInvoice(['total_amount' => 100, 'paid_amount' => 40]),                        // nicht
            $this->makeInvoice(['total_amount' => 0, 'paid_amount' => 0, 'subtotal' => 0]),          // bezahlt
            $this->makeInvoice(['type' => 'Gutschrift', 'total_amount' => 100, 'paid_amount' => 100]), // Typ-Gate
        ];

        foreach ($cases as $inv) {
            $this->assertSame($inv->isFullyPaid(), $inv->isPaid());
        }
    }
}
