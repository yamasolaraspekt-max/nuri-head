<?php

namespace Tests\Feature\Accounting;

use App\Services\Accounting\AuswertungsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * FiBu Stufe (v) — Auswertungen gegen ein Referenz-Geschäftsjahr mit von Hand bekannten Soll-Werten
 * (Zahlen-Wahrheit): zwei Ausgangsrechnungen (19 % / 7 %) + eine Eingangsrechnung mit Vorsteuer.
 *
 * Erwartung:
 *  UStVA: Netto19=1000 · USt19=190 · Netto7=100 · USt7=7 · Vorsteuer=95 · Zahllast=190+7−95=102
 *  BWA:   Erlöse=1100 · Wareneinsatz=500 · Rohertrag=600 · Ergebnis=600
 *  SuSa:  Debitor Saldo=1297 (1190+107) · Erlös19 Haben=1000
 */
class AuswertungenTest extends TestCase
{
    use DatabaseTransactions;

    private int $clientId;

    private array $acc = []; // key => account_id

    private AuswertungsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuswertungsService;
        $now = now();

        $chartId = DB::table('chart_of_accounts')->insertGetId([
            'code' => 'AWSKR', 'name' => 'Auswertungs-Testrahmen', 'country' => 'DE', 'version' => '2026',
            'is_default' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->clientId = DB::table('accounting_clients')->insertGetId([
            'name' => 'Auswertungs-Mandant', 'default_chart_of_account_id' => $chartId, 'default_currency' => 'EUR',
            'fiscal_year_start_month' => 1, 'is_active' => true, 'is_datev_enabled' => false,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        // Konten + Mappings.
        $defs = [
            'forderung_debitor' => ['1400', 'Forderungen', 'aktiv', 'forderungen', 'soll'],
            'verbindlichkeit_kreditor' => ['1600', 'Verbindlichkeiten', 'passiv', 'verbindlichkeiten', 'haben'],
            'erloes_19' => ['8400', 'Erlöse 19 %', 'erloes', 'umsatzerloese', 'haben'],
            'erloes_7' => ['8300', 'Erlöse 7 %', 'erloes', 'umsatzerloese', 'haben'],
            'ust_19' => ['1776', 'USt 19 %', 'passiv', 'umsatzsteuer', 'haben'],
            'ust_7' => ['1771', 'USt 7 %', 'passiv', 'umsatzsteuer', 'haben'],
            'vorsteuer_19' => ['1576', 'Vorsteuer 19 %', 'aktiv', 'vorsteuer', 'soll'],
            'wareneingang_19' => ['3400', 'Wareneingang', 'aufwand', 'wareneinsatz', 'soll'],
        ];
        foreach ($defs as $key => [$nr, $name, $typ, $kat, $saldo]) {
            $id = DB::table('accounts')->insertGetId([
                'chart_of_account_id' => $chartId, 'accounting_client_id' => $this->clientId,
                'account_number' => $nr, 'account_name' => $name, 'account_type' => $typ,
                'account_category' => $kat, 'normal_balance' => $saldo, 'is_active' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $this->acc[$key] = $id;
            DB::table('account_mappings')->insert([
                'accounting_client_id' => $this->clientId, 'chart_of_account_id' => $chartId,
                'mapping_key' => $key, 'mapping_name' => $key, 'applies_to' => '', 'account_id' => $id,
                'priority' => 100, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Referenz-Buchungen (festgeschrieben).
        $this->buchung('2026-02-10', [
            [$this->acc['forderung_debitor'], 1190.00, 0],
            [$this->acc['erloes_19'], 0, 1000.00],
            [$this->acc['ust_19'], 0, 190.00],
        ]);
        $this->buchung('2026-02-20', [
            [$this->acc['forderung_debitor'], 107.00, 0],
            [$this->acc['erloes_7'], 0, 100.00],
            [$this->acc['ust_7'], 0, 7.00],
        ]);
        $this->buchung('2026-03-05', [
            [$this->acc['wareneingang_19'], 500.00, 0],
            [$this->acc['vorsteuer_19'], 95.00, 0],
            [$this->acc['verbindlichkeit_kreditor'], 0, 595.00],
        ]);
    }

    /** @param array<int, array{0:int,1:float,2:float}> $lines [account_id, soll, haben] */
    private function buchung(string $date, array $lines): void
    {
        $now = now();
        $entryId = DB::table('accounting_journal_entries')->insertGetId([
            'accounting_client_id' => $this->clientId, 'booking_date' => $date, 'document_date' => $date,
            'booking_text' => 'Referenz', 'origin' => 'manuell', 'status' => 'festgeschrieben',
            'is_finalized' => true, 'is_booked' => true, 'is_locked' => true,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $rows = [];
        $ln = 1;
        foreach ($lines as [$accId, $soll, $haben]) {
            $rows[] = ['journal_entry_id' => $entryId, 'line_number' => $ln++, 'account_id' => $accId,
                'debit_amount' => $soll, 'credit_amount' => $haben, 'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('accounting_journal_lines')->insert($rows);
    }

    public function test_ustva_kennzahlen_stimmen_zifferngenau(): void
    {
        $u = $this->service->ustVoranmeldung($this->clientId, '2026-01-01', '2026-12-31');
        $this->assertEquals(1000.00, $u['kz81_netto_19']);
        $this->assertEquals(190.00, $u['ust_19']);
        $this->assertEquals(100.00, $u['kz86_netto_7']);
        $this->assertEquals(7.00, $u['ust_7']);
        $this->assertEquals(95.00, $u['kz66_vorsteuer']);
        $this->assertEquals(102.00, $u['kz83_zahllast']);
    }

    public function test_bwa_ergebnis_stimmt(): void
    {
        $b = $this->service->bwa($this->clientId, '2026-01-01', '2026-12-31');
        $this->assertEquals(1100.00, $b['erloese']);
        $this->assertEquals(500.00, $b['wareneinsatz']);
        $this->assertEquals(600.00, $b['rohertrag']);
        $this->assertEquals(600.00, $b['ergebnis']);
    }

    public function test_susa_salden_stimmen(): void
    {
        $rows = collect($this->service->summenSalden($this->clientId, '2026-01-01', '2026-12-31'))->keyBy('account_number');
        $this->assertEquals(1297.00, $rows['1400']['saldo']); // Debitor Soll-Saldo
        $this->assertEquals(1000.00, $rows['8400']['haben']); // Erlös 19 % Haben
        $this->assertEquals(95.00, $rows['1576']['soll']);    // Vorsteuer Soll
    }

    public function test_zeitraum_grenzt_korrekt_ab(): void
    {
        // Nur Februar: die März-Vorsteuer darf NICHT auftauchen.
        $u = $this->service->ustVoranmeldung($this->clientId, '2026-02-01', '2026-02-28');
        $this->assertEquals(0.00, $u['kz66_vorsteuer']);
        $this->assertEquals(197.00, $u['kz83_zahllast']); // 190+7, keine Vorsteuer
    }
}
