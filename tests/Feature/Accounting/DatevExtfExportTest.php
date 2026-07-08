<?php

namespace Tests\Feature\Accounting;

use App\Services\Accounting\DatevExtfExportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * FiBu Stufe (vi) — DATEV-EXTF-Testpaket (Pflicht-Gate 2.3: kein Export vor grünem Testpaket +
 * grünem Konformitäts-Prüfer). Prüft Kopf, Stern-Zerlegung (Ausgangs- + Eingangsrechnung),
 * Betrags-/S-H-Format und den integrierten Konformitäts-Prüfer.
 */
class DatevExtfExportTest extends TestCase
{
    use DatabaseTransactions;

    private int $clientId;

    private array $acc = [];

    private DatevExtfExportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DatevExtfExportService;
        $now = now();

        $chartId = DB::table('chart_of_accounts')->insertGetId([
            'code' => 'DXSKR', 'name' => 'DATEV-Testrahmen', 'country' => 'DE', 'version' => '2026',
            'is_default' => false, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->clientId = DB::table('accounting_clients')->insertGetId([
            'name' => 'DATEV-Mandant', 'default_chart_of_account_id' => $chartId, 'default_currency' => 'EUR',
            'fiscal_year_start_month' => 1, 'is_active' => true, 'is_datev_enabled' => true,
            'datev_berater_nr' => '12345', 'datev_mandant_nr' => '67890',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        foreach ([
            'debitor' => '1400', 'kreditor' => '1600', 'erloes19' => '8400',
            'ust19' => '1776', 'vst19' => '1576', 'wareneingang' => '3400',
        ] as $k => $nr) {
            $this->acc[$k] = DB::table('accounts')->insertGetId([
                'chart_of_account_id' => $chartId, 'accounting_client_id' => $this->clientId,
                'account_number' => $nr, 'account_name' => $k, 'account_type' => 'x',
                'account_category' => 'x', 'normal_balance' => 'soll', 'is_active' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Ausgangsrechnung: Debitor 1190 an Erlös 1000 + USt 190.
        $this->buchung('2026-02-10', 'RE-1001', [
            [$this->acc['debitor'], 1190.00, 0],
            [$this->acc['erloes19'], 0, 1000.00],
            [$this->acc['ust19'], 0, 190.00],
        ]);
        // Eingangsrechnung: Wareneingang 500 + Vorsteuer 95 an Kreditor 595.
        $this->buchung('2026-03-05', 'ER-2001', [
            [$this->acc['wareneingang'], 500.00, 0],
            [$this->acc['vst19'], 95.00, 0],
            [$this->acc['kreditor'], 0, 595.00],
        ]);
    }

    private function buchung(string $date, string $belegnr, array $lines): void
    {
        $now = now();
        $entryId = DB::table('accounting_journal_entries')->insertGetId([
            'accounting_client_id' => $this->clientId, 'booking_date' => $date, 'document_date' => $date,
            'document_number' => $belegnr, 'booking_text' => 'DATEV-Test '.$belegnr, 'origin' => 'manuell',
            'status' => 'festgeschrieben', 'is_finalized' => true, 'is_booked' => true, 'is_locked' => true,
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

    private function extf(): string
    {
        return mb_convert_encoding(
            $this->service->exportBuchungsstapel($this->clientId, '2026-01-01', '2026-12-31'),
            'UTF-8', 'CP1252'
        );
    }

    public function test_kopfzeile_ist_extf_buchungsstapel_v13(): void
    {
        $zeilen = explode("\r\n", $this->extf());
        $this->assertStringStartsWith('"EXTF";700;21;"Buchungsstapel";13;', $zeilen[0]);
        $this->assertStringContainsString(';12345;67890;', $zeilen[0]); // Berater/Mandant
    }

    public function test_ausgangsrechnung_wird_sternfoermig_zerlegt(): void
    {
        $inhalt = $this->extf();
        // Erlös-Zeile: 1000,00;"H";...;8400;1400
        $this->assertMatchesRegularExpression('/1000,00;"H";"EUR";;;;8400;1400;;1002;"RE-1001"/', $inhalt);
        // USt-Zeile: 190,00;"H";...;1776;1400
        $this->assertMatchesRegularExpression('/190,00;"H";"EUR";;;;1776;1400;;1002;"RE-1001"/', $inhalt);
    }

    public function test_eingangsrechnung_pivot_auf_kreditor(): void
    {
        $inhalt = $this->extf();
        // Wareneingang 500,00;S;...;3400;1600 und Vorsteuer 95,00;S;...;1576;1600
        $this->assertMatchesRegularExpression('/500,00;"S";"EUR";;;;3400;1600;;0503;"ER-2001"/', $inhalt);
        $this->assertMatchesRegularExpression('/95,00;"S";"EUR";;;;1576;1600;;0503;"ER-2001"/', $inhalt);
    }

    public function test_konformitaets_pruefer_ist_gruen(): void
    {
        $cp1252 = $this->service->exportBuchungsstapel($this->clientId, '2026-01-01', '2026-12-31');
        $fehler = $this->service->pruefeKonformitaet($cp1252);
        $this->assertSame([], $fehler, 'Konformitäts-Fehler: '.implode(' | ', $fehler));
    }

    public function test_konformitaets_pruefer_erkennt_kaputte_datei(): void
    {
        $kaputt = mb_convert_encoding("EXTF;700;99;\"Falsch\";1\r\nKopf\r\nabc;X;;;;;xx;yy;;99;\r\n", 'CP1252', 'UTF-8');
        $fehler = $this->service->pruefeKonformitaet($kaputt);
        $this->assertNotEmpty($fehler);
        $this->assertTrue(collect($fehler)->contains(fn ($f) => str_contains($f, 'Umsatz')));
    }
}
