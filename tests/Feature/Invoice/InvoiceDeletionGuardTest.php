<?php

namespace Tests\Feature\Invoice;

use App\Exceptions\InvoiceDeletionBlockedException;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Models\NewLeads;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * S1-02 — Loeschsperre fuer rechnungsrelevante/finale Belege.
 *
 * WICHTIG: RefreshDatabase nur gegen isolierte Test-DB ausfuehren
 * (DB_DATABASE=ticket_testing), nie gegen die lokale Arbeitsdatenbank `ticket`.
 */
class InvoiceDeletionGuardTest extends TestCase
{
    use RefreshDatabase;

    private int $customerId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerId = DB::table('new_leads')->insertGetId(['customer_type' => 'private']);
    }

    private function makeInvoice(array $overrides = []): Invoice
    {
        $invoice = new Invoice();
        $invoice->forceFill(array_merge([
            'customer_id' => $this->customerId,
            'type' => 'Rechnung',
            'status' => 'draft',
            'issue_date' => now()->toDateString(),
            'currency' => 'EUR',
            'subtotal' => 100,
            'tax_rate' => 19,
            'tax_amount' => 19,
            'total_amount' => 119,
            'paid_amount' => 0,
        ], $overrides));
        $invoice->save();

        return $invoice->fresh();
    }

    public function test_draft_ohne_nummer_ist_loeschbar(): void
    {
        $invoice = $this->makeInvoice();

        $invoice->delete();

        $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);
    }

    public function test_rechnung_mit_nummer_ist_nicht_loeschbar(): void
    {
        $invoice = $this->makeInvoice(['invoice_no' => 'ALT-123', 'status' => 'draft']);

        $this->expectException(InvoiceDeletionBlockedException::class);

        $invoice->delete();
    }

    public function test_sent_rechnung_ist_nicht_loeschbar(): void
    {
        $invoice = $this->makeInvoice(['status' => 'sent']);

        $this->expectException(InvoiceDeletionBlockedException::class);

        $invoice->delete();
    }

    public function test_bezahlte_rechnung_ist_nicht_loeschbar(): void
    {
        $invoice = $this->makeInvoice(['status' => 'draft', 'paid_amount' => 1]);

        $this->expectException(InvoiceDeletionBlockedException::class);

        $invoice->delete();
    }

    public function test_controller_liefert_422_fuer_finale_rechnung(): void
    {
        $invoice = $this->makeInvoice(['status' => 'sent']);

        $response = app(InvoiceController::class)->destroy($invoice);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['ok']);
        $this->assertNotSoftDeleted('invoices', ['id' => $invoice->id]);
    }

    public function test_datei_finaler_rechnung_wird_nicht_physisch_geloescht(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('customer_invoice/test/final.pdf', 'PDF');

        $invoice = $this->makeInvoice(['status' => 'sent']);
        $file = InvoiceFile::create([
            'invoice_id' => $invoice->id,
            'original_name' => 'final.pdf',
            'stored_name' => 'final.pdf',
            'stored_path' => 'customer_invoice/test/final.pdf',
            'mime' => 'application/pdf',
            'size' => 3,
        ]);

        $response = app(InvoiceController::class)->deleteFile($file);

        $this->assertSame(422, $response->getStatusCode());
        Storage::disk('public')->assertExists('customer_invoice/test/final.pdf');
        $this->assertNotSoftDeleted('invoice_files', ['id' => $file->id]);
    }

    public function test_kunden_softdelete_mit_rechnung_bleibt_erlaubt(): void
    {
        $this->makeInvoice(['status' => 'sent']);
        $lead = NewLeads::findOrFail($this->customerId);

        $lead->delete();

        $this->assertSoftDeleted('new_leads', ['id' => $this->customerId]);
        $this->assertDatabaseHas('invoices', ['customer_id' => $this->customerId]);
    }

    public function test_kunden_force_delete_mit_rechnung_wird_blockiert(): void
    {
        $this->makeInvoice(['status' => 'sent']);
        $lead = NewLeads::findOrFail($this->customerId);

        $this->expectException(InvoiceDeletionBlockedException::class);

        $lead->forceDelete();
    }

    public function test_db_verhindert_hard_delete_kaskade_auf_kunden_mit_rechnung(): void
    {
        $this->makeInvoice(['status' => 'sent']);

        $this->expectException(QueryException::class);

        DB::table('new_leads')->whereKey($this->customerId)->delete();
    }
}
