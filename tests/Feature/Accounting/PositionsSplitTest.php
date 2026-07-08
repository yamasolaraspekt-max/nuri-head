<?php

namespace Tests\Feature\Accounting;

use App\Services\Accounting\BelegflussService;
use Database\Seeders\KontenrahmenSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * FiBu Stufe (vii) — Positions-Erlöskonten-Split: Erlöse je Rechnungsposition auf Erlöskonten nach
 * Kategorie/Steuersatz. Abnahme-Anker: Summe der Positions-Erlöse (+USt) = Kopf-Brutto (zifferngenau).
 */
class PositionsSplitTest extends TestCase
{
    use DatabaseTransactions;

    private BelegflussService $service;

    private int $clientId;

    private int $chartId;

    private int $customerId;

    protected function setUp(): void
    {
        parent::setUp();
        (new KontenrahmenSeeder)->run();
        $this->service = new BelegflussService;
        $client = DB::table('accounting_clients')->where('name', 'Demo-Mandant')->first();
        $this->clientId = $client->id;
        $this->chartId = $client->default_chart_of_account_id;
        $this->customerId = DB::table('new_leads')->insertGetId([
            'customer_type' => 'privat', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function rechnung(float $subtotal, float $tax, float $rate = 19.0): int
    {
        $now = now();

        return DB::table('invoices')->insertGetId([
            'customer_id' => $this->customerId, 'type' => 'invoice', 'issue_date' => '2026-04-01', 'due_date' => '2026-04-15',
            'subtotal' => $subtotal, 'tax_amount' => $tax, 'total_amount' => round($subtotal + $tax, 2),
            'tax_rate' => $rate, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    private function produkt(string $name, ?string $category): int
    {
        // Kategorie-Signal = Artikelgruppen-Name (invoice_items.product_id → article_groups).
        return DB::table('article_groups')->insertGetId([
            'article_group' => $category ?? $name, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function position(int $invoiceId, ?int $productId, float $lineTotal, float $rate): void
    {
        $now = now();
        DB::table('invoice_items')->insert([
            'invoice_id' => $invoiceId, 'product_id' => $productId, 'title' => 'Pos',
            'qty' => 1, 'unit_price' => $lineTotal, 'tax_rate' => $rate, 'line_total' => $lineTotal,
            'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    private function accId(string $key): int
    {
        return (int) DB::table('account_mappings')->where('accounting_client_id', $this->clientId)
            ->where('chart_of_account_id', $this->chartId)->where('mapping_key', $key)->value('account_id');
    }

    public function test_gemischte_saetze_splitten_auf_erloeskonten_summe_gleich_kopf(): void
    {
        // 1500 @19% (285 USt) + 200 @7% (14 USt) = Netto 1700, USt 299, Brutto 1999.
        $inv = $this->rechnung(1700.00, 299.00);
        $pv = $this->produkt('PV-Modul', 'pv');
        $wp = $this->produkt('Wärmepumpe', 'heatpump');
        $this->position($inv, $pv, 1000.00, 19.0);
        $this->position($inv, $pv, 500.00, 19.0);
        $this->position($inv, $wp, 200.00, 7.0);

        $r = $this->service->bucheRechnungMitPositionen($inv, 'system');
        $this->assertFalse($r['skipped']);
        $this->assertSame(2, $r['gruppen']);
        $this->assertEquals($r['soll'], $r['haben']);
        $this->assertEquals(1999.00, $r['soll']);

        $lines = DB::table('accounting_journal_lines')->where('journal_entry_id', $r['journal_entry_id'])->get();
        $byAcc = $lines->groupBy('account_id')->map(fn ($g) => (float) $g->sum('credit_amount'));
        $this->assertEquals(1500.00, $byAcc[$this->accId('erloes_19')]);
        $this->assertEquals(200.00, $byAcc[$this->accId('erloes_7')]);
        $this->assertEquals(285.00, $byAcc[$this->accId('ust_19')]);
        $this->assertEquals(14.00, $byAcc[$this->accId('ust_7')]);
        // Debitor Soll = Brutto.
        $this->assertEquals(1999.00, (float) $lines->firstWhere('account_id', $this->accId('forderung_debitor'))->debit_amount);
    }

    public function test_kategorie_mapping_routet_auf_eigenes_erloeskonto(): void
    {
        // Kategorie-spezifisches Erlöskonto pv @19 additiv anlegen.
        $now = now();
        $pvAcc = DB::table('accounts')->insertGetId([
            'chart_of_account_id' => $this->chartId, 'accounting_client_id' => $this->clientId,
            'account_number' => '8401', 'account_name' => 'Erlöse PV 19 %', 'account_type' => 'erloes',
            'account_category' => 'umsatzerloese', 'normal_balance' => 'haben', 'is_active' => true,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('account_mappings')->insert([
            'accounting_client_id' => $this->clientId, 'chart_of_account_id' => $this->chartId,
            'mapping_key' => 'erloes_pv_19', 'mapping_name' => 'erloes_pv_19', 'applies_to' => '',
            'account_id' => $pvAcc, 'priority' => 100, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $inv = $this->rechnung(1000.00, 190.00);
        $this->position($inv, $this->produkt('PV', 'pv'), 1000.00, 19.0);

        $r = $this->service->bucheRechnungMitPositionen($inv, 'system');
        $credit = (float) DB::table('accounting_journal_lines')
            ->where('journal_entry_id', $r['journal_entry_id'])->where('account_id', $pvAcc)->value('credit_amount');
        $this->assertEquals(1000.00, $credit); // auf das Kategorie-Konto geroutet
    }

    public function test_ohne_positionen_faellt_auf_kopfbuchung_zurueck(): void
    {
        $inv = $this->rechnung(1000.00, 190.00);
        $r = $this->service->bucheRechnungMitPositionen($inv, 'system');
        // Kopf-Buchung: 1 Debitor + 1 Erlös + 1 USt = 3 Zeilen, kein 'gruppen'.
        $this->assertSame(3, $r['lines']);
        $this->assertArrayNotHasKey('gruppen', $r);
        $this->assertEquals(1190.00, $r['soll']);
    }

    public function test_cent_differenz_wird_auf_groesste_gruppe_reconcilet(): void
    {
        // Kopf-USt bewusst 1 Cent über der naiven Positions-Summe (299,00 statt 298,99-Konstellation).
        $inv = $this->rechnung(1700.00, 299.01);
        $this->position($inv, $this->produkt('A', 'pv'), 1500.00, 19.0);      // naiv 285,00
        $this->position($inv, $this->produkt('B', 'heatpump'), 200.00, 7.0);  // naiv 14,00 → Summe 299,00
        $r = $this->service->bucheRechnungMitPositionen($inv, 'system');

        $ustSumme = (float) DB::table('accounting_journal_lines')
            ->where('journal_entry_id', $r['journal_entry_id'])
            ->whereIn('account_id', [$this->accId('ust_19'), $this->accId('ust_7')])->sum('credit_amount');
        $this->assertEquals(299.01, $ustSumme);                        // = Kopf-USt
        $this->assertEquals(285.01, (float) DB::table('accounting_journal_lines')
            ->where('journal_entry_id', $r['journal_entry_id'])
            ->where('account_id', $this->accId('ust_19'))->value('credit_amount')); // größte Gruppe trägt Cent
        $this->assertEquals($r['soll'], $r['haben']);
    }
}
