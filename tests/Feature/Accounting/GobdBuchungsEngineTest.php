<?php

namespace Tests\Feature\Accounting;

use App\Services\Accounting\BuchungsEngine;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * FiBu Stufe (iv) — GoBD-Beweis-Testsatz (Pflicht-Gate, Betriebsordnung 2.3):
 * kein produktiver Buchungssatz vor grünem Beweis der vier GoBD-Kernprinzipien.
 *
 * 1) Unveränderlichkeit (festgeschrieben = kein zweites Festschreiben)
 * 2) Lückenloser Nummernkreis (fortlaufend, ohne Lücke, je Mandant/Jahr)
 * 3) Maker-Checker (Erfasser ≠ Festschreiber)
 * 4) Audit-Trail + Storno statt Edit (Original unverändert, Gegenbuchung erzeugt)
 *
 * Läuft gegen die Test-DB in einer Transaktion (Rollback), keine Restdaten.
 */
class GobdBuchungsEngineTest extends TestCase
{
    use DatabaseTransactions;

    private int $clientId;

    private int $debitorAccId;

    private int $erloesAccId;

    private BuchungsEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new BuchungsEngine;
        $now = now();

        $chartId = DB::table('chart_of_accounts')->insertGetId([
            'code' => 'TESTSKR', 'name' => 'Test-Kontenrahmen', 'country' => 'DE', 'version' => '2026',
            'is_default' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->clientId = DB::table('accounting_clients')->insertGetId([
            'name' => 'GoBD-Testmandant', 'legal_name' => 'GoBD Test GmbH',
            'default_chart_of_account_id' => $chartId, 'default_currency' => 'EUR',
            'fiscal_year_start_month' => 1, 'is_active' => true, 'is_datev_enabled' => false,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->debitorAccId = $this->konto($chartId, '1400', 'Forderungen', 'aktiv', 'soll');
        $this->erloesAccId = $this->konto($chartId, '8400', 'Erlöse 19 %', 'erloes', 'haben');
    }

    private function konto(int $chartId, string $nr, string $name, string $typ, string $saldo): int
    {
        $now = now();

        return DB::table('accounts')->insertGetId([
            'chart_of_account_id' => $chartId, 'accounting_client_id' => $this->clientId,
            'account_number' => $nr, 'account_name' => $name, 'account_type' => $typ,
            'account_category' => 'test', 'normal_balance' => $saldo, 'is_active' => true,
            'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    /** Erzeugt einen ausgeglichenen Entwurfs-Buchungssatz (100,00 Soll Debitor an Haben Erlös). */
    private function entwurf(string $createdBy = 'erfasser', string $date = '2026-03-15'): int
    {
        $now = now();
        $entryId = DB::table('accounting_journal_entries')->insertGetId([
            'accounting_client_id' => $this->clientId, 'booking_date' => $date, 'document_date' => $date,
            'booking_text' => 'GoBD-Test', 'origin' => 'manuell', 'status' => 'entwurf',
            'created_by' => $createdBy, 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('accounting_journal_lines')->insert([
            ['journal_entry_id' => $entryId, 'line_number' => 1, 'account_id' => $this->debitorAccId,
                'debit_amount' => 100.00, 'credit_amount' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['journal_entry_id' => $entryId, 'line_number' => 2, 'account_id' => $this->erloesAccId,
                'debit_amount' => 0, 'credit_amount' => 100.00, 'created_at' => $now, 'updated_at' => $now],
        ]);

        return $entryId;
    }

    /** 1) Unveränderlichkeit: festgeschrieben ⇒ zweites Festschreiben wird abgewiesen. */
    public function test_festgeschriebener_buchungssatz_ist_unveraenderlich(): void
    {
        $id = $this->entwurf();
        $this->engine->festschreiben($id, 'pruefer');

        $entry = DB::table('accounting_journal_entries')->find($id);
        $this->assertSame(1, (int) $entry->is_finalized);
        $this->assertSame(1, (int) $entry->is_locked);
        $this->assertSame('festgeschrieben', $entry->status);
        $this->assertNotNull($entry->booking_number);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('bereits festgeschrieben');
        $this->engine->festschreiben($id, 'pruefer2');
    }

    /** 2) Lückenloser Nummernkreis: drei Festschreibungen ⇒ 000001, 000002, 000003. */
    public function test_nummernkreis_ist_lueckenlos_und_fortlaufend(): void
    {
        $n1 = $this->engine->festschreiben($this->entwurf(date: '2026-03-01'), 'pruefer')['booking_number'];
        $n2 = $this->engine->festschreiben($this->entwurf(date: '2026-03-02'), 'pruefer')['booking_number'];
        $n3 = $this->engine->festschreiben($this->entwurf(date: '2026-03-03'), 'pruefer')['booking_number'];

        $this->assertSame('2026-000001', $n1);
        $this->assertSame('2026-000002', $n2);
        $this->assertSame('2026-000003', $n3);
    }

    /** 3) Maker-Checker: Erfasser darf den eigenen Buchungssatz nicht festschreiben. */
    public function test_maker_checker_verbietet_selbstfestschreibung(): void
    {
        $id = $this->entwurf(createdBy: 'anna');

        try {
            $this->engine->festschreiben($id, 'anna');
            $this->fail('Selbst-Festschreibung hätte scheitern müssen.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Maker-Checker', $e->getMessage());
        }

        // Ein anderer Prüfer darf.
        $res = $this->engine->festschreiben($id, 'bernd');
        $this->assertSame('2026-000001', $res['booking_number']);
        $this->assertSame('bernd', DB::table('accounting_journal_entries')->where('id', $id)->value('finalized_by'));
    }

    /** 4) Storno statt Edit: Original bleibt unverändert, Gegenbuchung mit getauschtem Soll/Haben. */
    public function test_storno_erzeugt_gegenbuchung_und_laesst_original_unveraendert(): void
    {
        $id = $this->entwurf();
        $this->engine->festschreiben($id, 'pruefer');
        $origBefore = DB::table('accounting_journal_entries')->find($id);

        $storno = $this->engine->storno($id, 'pruefer', 'Falscher Betrag');
        $stornoId = $storno['storno_entry_id'];

        // Original unverändert.
        $origAfter = DB::table('accounting_journal_entries')->find($id);
        $this->assertEquals($origBefore->booking_number, $origAfter->booking_number);
        $this->assertSame(1, (int) $origAfter->is_finalized);

        // Storno-Kopf.
        $stornoEntry = DB::table('accounting_journal_entries')->find($stornoId);
        $this->assertSame(1, (int) $stornoEntry->is_storno);
        $this->assertSame($id, (int) $stornoEntry->storno_of_id);
        $this->assertSame('2026-000002', $stornoEntry->booking_number);
        $this->assertSame(1, (int) $stornoEntry->is_finalized);

        // Gegenzeilen: Soll/Haben getauscht (Debitor jetzt Haben, Erlös jetzt Soll).
        $debitorLine = DB::table('accounting_journal_lines')->where('journal_entry_id', $stornoId)
            ->where('account_id', $this->debitorAccId)->first();
        $this->assertEquals(100.00, (float) $debitorLine->credit_amount);
        $this->assertEquals(0.0, (float) $debitorLine->debit_amount);

        // Summe Original + Storno = 0 (neutralisiert).
        $sumSoll = DB::table('accounting_journal_lines')->whereIn('journal_entry_id', [$id, $stornoId])->sum('debit_amount');
        $sumHaben = DB::table('accounting_journal_lines')->whereIn('journal_entry_id', [$id, $stornoId])->sum('credit_amount');
        $this->assertEquals($sumSoll, $sumHaben);

        // Doppel-Storno wird abgewiesen.
        $this->expectException(RuntimeException::class);
        $this->engine->storno($id, 'pruefer', 'nochmal');
    }
}
