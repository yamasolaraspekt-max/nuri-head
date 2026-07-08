<?php

namespace Tests\Feature\Accounting;

use App\Services\Accounting\AuswertungsService;
use App\Services\Accounting\BuchungsEngine;
use App\Services\Accounting\EingangsBelegflussService;
use Database\Seeders\KontenrahmenSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * FiBu Stufe (viii, Kern) — Eingangsrechnung/Kreditor: „Wareneingang + Vorsteuer an Kreditor".
 * Prüft ausgeglichene Buchung, Idempotenz, Zusammenspiel mit Festschreibung (iv) und dass die
 * Vorsteuer in der UStVA (v) korrekt als KZ66 erscheint.
 */
class EingangsBelegflussTest extends TestCase
{
    use DatabaseTransactions;

    private EingangsBelegflussService $service;

    private int $clientId;

    private int $chartId;

    private int $kreditorAccId;

    protected function setUp(): void
    {
        parent::setUp();
        (new KontenrahmenSeeder)->run();
        $this->service = new EingangsBelegflussService;
        $client = DB::table('accounting_clients')->where('name', 'Demo-Mandant')->first();
        $this->clientId = $client->id;
        $this->chartId = $client->default_chart_of_account_id;
        // Kreditor-Personenkonto (accounts).
        $this->kreditorAccId = DB::table('accounts')->insertGetId([
            'chart_of_account_id' => $this->chartId, 'accounting_client_id' => $this->clientId,
            'account_number' => '70001', 'account_name' => 'Kreditor Musterlieferant', 'account_type' => 'passiv',
            'account_category' => 'verbindlichkeiten', 'normal_balance' => 'haben', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function accId(string $key): int
    {
        return (int) DB::table('account_mappings')->where('accounting_client_id', $this->clientId)
            ->where('chart_of_account_id', $this->chartId)->where('mapping_key', $key)->value('account_id');
    }

    private function daten(array $over = []): array
    {
        return array_merge([
            'creditor_id' => $this->kreditorAccId, 'external_number' => 'LIEF-2026-042', 'document_date' => '2026-05-10',
            'net' => 500.00, 'tax' => 95.00, 'rate' => 19.0,
        ], $over);
    }

    public function test_bucht_wareneingang_und_vorsteuer_an_kreditor(): void
    {
        $r = $this->service->bucheEingangsrechnung($this->daten());
        $this->assertFalse($r['skipped']);
        $this->assertEquals(595.00, $r['soll']);
        $this->assertEquals($r['soll'], $r['haben']);
        $this->assertSame(3, $r['lines']);

        $lines = DB::table('accounting_journal_lines')->where('journal_entry_id', $r['journal_entry_id'])->get();
        $this->assertEquals(500.00, (float) $lines->firstWhere('account_id', $this->accId('wareneingang_19'))->debit_amount);
        $this->assertEquals(95.00, (float) $lines->firstWhere('account_id', $this->accId('vorsteuer_19'))->debit_amount);
        // Haben auf dem Kreditor-Personenkonto.
        $this->assertEquals(595.00, (float) $lines->firstWhere('account_id', $this->kreditorAccId)->credit_amount);

        // Beleg trägt Kreditor-Anker.
        $doc = DB::table('accounting_documents')->find($r['document_id']);
        $this->assertSame('eingangsrechnung', $doc->document_type);
        $this->assertSame('kreditor', $doc->partner_type);
        $this->assertSame($this->kreditorAccId, (int) $doc->creditor_id);
        $this->assertSame('LIEF-2026-042', $doc->external_number);
    }

    public function test_ist_idempotent_ueber_kreditor_und_belegnummer(): void
    {
        $a = $this->service->bucheEingangsrechnung($this->daten());
        $b = $this->service->bucheEingangsrechnung($this->daten());
        $this->assertFalse($a['skipped']);
        $this->assertTrue($b['skipped']);
        $this->assertSame(1, DB::table('accounting_documents')->where('external_number', 'LIEF-2026-042')->count());
    }

    public function test_kann_festgeschrieben_werden_und_erscheint_in_ustva_vorsteuer(): void
    {
        $r = $this->service->bucheEingangsrechnung($this->daten(['document_date' => '2026-05-10']));
        // Buchungsdatum ist heute (booking_date=now); für die Auswertung Zeitraum weit fassen.
        $bookingDate = DB::table('accounting_journal_entries')->where('id', $r['journal_entry_id'])->value('booking_date');

        (new BuchungsEngine)->festschreiben($r['journal_entry_id'], 'pruefer');
        $entry = DB::table('accounting_journal_entries')->find($r['journal_entry_id']);
        $this->assertSame(1, (int) $entry->is_finalized);
        $this->assertNotNull($entry->booking_number);

        $ustva = (new AuswertungsService)->ustVoranmeldung($this->clientId, $bookingDate, $bookingDate);
        $this->assertEquals(95.00, $ustva['kz66_vorsteuer']);
        $this->assertEquals(-95.00, $ustva['kz83_zahllast']); // reine Vorsteuer → Erstattung
    }
}
