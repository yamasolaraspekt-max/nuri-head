<?php

namespace Tests\Feature\Accounting;

use Database\Seeders\KontenrahmenSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * FiBu Stufe (ii) — Eigenschafts-Test des Kontenrahmen-Seeds (Kontenrahmen/USt/mapping_keys).
 *
 * Misst die Abnahmekriterien der Planner-Spec „stufe-ii-kontenrahmen-debitor-ust":
 * Stammkonten/USt/Mappings vorhanden, Sammeldebitor 1400 über forderung_debitor, Idempotenz
 * (Gegen-Beweis: zweiter Lauf ändert nichts), alle mapping_keys verweisen auf existierende Konten,
 * Marker-basierter Rückbau restlos, und die Belegkette (invoices/deals/customers) bleibt unberührt.
 *
 * Läuft gegen ticket_testing (phpunit.xml), DatabaseTransactions -> Rollback nach jedem Test.
 */
class KontenrahmenSeederTest extends TestCase
{
    use DatabaseTransactions;

    private const MARKER = 'skr_seed';

    protected function setUp(): void
    {
        parent::setUp();
        (new KontenrahmenSeeder)->run();
    }

    /** Kriterium 2 + 4 + 5: Kontenrahmen, Standardkonten, USt-Codes und Sammeldebitor-Mapping vorhanden. */
    public function test_seedt_kontenrahmen_konten_steuern_und_sammeldebitor_mapping(): void
    {
        // SKR03 = Default, SKR04 vorhanden.
        $skr03 = DB::table('chart_of_accounts')->where('code', 'SKR03')->first();
        $this->assertNotNull($skr03, 'SKR03 fehlt');
        $this->assertEquals(1, (int) $skr03->is_default, 'SKR03 ist nicht Default');
        $this->assertNotNull(DB::table('chart_of_accounts')->where('code', 'SKR04')->first(), 'SKR04 fehlt');

        // Repräsentative SKR03-Konten mit Marker.
        foreach (['1400', '1776', '1771', '1576', '8400', '8300'] as $nr) {
            $this->assertNotNull(
                DB::table('accounts')->where('chart_of_account_id', $skr03->id)
                    ->where('account_number', $nr)->where('imported_from', self::MARKER)->first(),
                "SKR03-Konto $nr fehlt (oder ohne Marker)",
            );
        }

        // USt-Codes.
        $client = DB::table('accounting_clients')->where('name', 'Demo-Mandant')->first();
        foreach (['UST19', 'UST7', 'UST0', 'VST19', 'VST7'] as $code) {
            $this->assertNotNull(
                DB::table('tax_codes')->where('accounting_client_id', $client->id)->where('code', $code)->first(),
                "Steuercode $code fehlt",
            );
        }

        // Sammeldebitor: forderung_debitor -> Konto 1400.
        $forderung = DB::table('account_mappings')
            ->where('chart_of_account_id', $skr03->id)->where('mapping_key', 'forderung_debitor')->first();
        $this->assertNotNull($forderung, 'mapping forderung_debitor fehlt');
        $acc = DB::table('accounts')->where('id', $forderung->account_id)->first();
        $this->assertSame('1400', $acc->account_number, 'Sammeldebitor zeigt nicht auf 1400');
    }

    /** Kriterium 3 (Kern-Gegenbeweis): zweiter Seed-Lauf erzeugt KEINE Duplikate. */
    public function test_ist_idempotent_kein_duplikat_bei_zweitem_lauf(): void
    {
        $zaehle = fn (string $t) => DB::table($t)->where('imported_from', self::MARKER)->count();
        $vorher = array_map($zaehle, ['chart_of_accounts', 'accounts', 'tax_codes', 'account_mappings']);

        (new KontenrahmenSeeder)->run(); // zweiter Lauf

        $nachher = array_map($zaehle, ['chart_of_accounts', 'accounts', 'tax_codes', 'account_mappings']);
        $this->assertSame($vorher, $nachher, 'Zweiter Seed-Lauf hat Zeilen verändert (nicht idempotent)');
    }

    /** Kriterium 4: jeder mapping_key verweist auf ein existierendes Konto (FK-Integrität/Vollständigkeit). */
    public function test_alle_mapping_keys_verweisen_auf_existierende_konten(): void
    {
        $verwaist = DB::table('account_mappings as m')
            ->leftJoin('accounts as a', 'a.id', '=', 'm.account_id')
            ->whereNull('a.id')->count();
        $this->assertSame(0, $verwaist, "$verwaist Mapping(s) verweisen auf ein nicht existierendes Konto");
    }

    /** Kriterium 7: Marker-basierter Rückbau räumt die Seed-Zeilen restlos (FK-sichere Reihenfolge). */
    public function test_marker_teardown_raeumt_restlos(): void
    {
        foreach (['account_mappings', 'tax_codes', 'accounts', 'accounting_clients', 'chart_of_accounts'] as $t) {
            if (Schema::hasColumn($t, 'imported_from')) {
                DB::table($t)->where('imported_from', self::MARKER)->delete();
            }
        }
        foreach (['chart_of_accounts', 'accounts', 'tax_codes', 'account_mappings', 'accounting_clients'] as $t) {
            $this->assertSame(0, DB::table($t)->where('imported_from', self::MARKER)->count(),
                "Marker-Rest in $t nach Teardown");
        }

        // Diskriminierend (Lehre Runde 2): die Identitäts-Zeilen müssen WIRKLICH weg sein — nicht nur
        // markerlos. Fehlt der Marker im Seeder, löscht der Teardown nichts und DIESE Assertions kippen.
        $this->assertSame(0, DB::table('accounting_clients')->where('name', 'Demo-Mandant')->count(),
            'Demo-Mandant existiert nach Rückbau noch (Seeder-Marker fehlt?)');
        $this->assertSame(0, DB::table('chart_of_accounts')->where('code', 'SKR03')->count(),
            'SKR03-Kontenrahmen existiert nach Rückbau noch');
    }

    /** Kriterium 6 (Gegen-Beweis stille Änderung): der Seed fasst die Belegkette nicht an. */
    public function test_belegkette_bleibt_unberuehrt(): void
    {
        $ketten = array_values(array_filter(['invoices', 'deals', 'customers'], fn ($t) => Schema::hasTable($t)));
        $vorher = [];
        foreach ($ketten as $t) {
            $vorher[$t] = DB::table($t)->count();
        }

        (new KontenrahmenSeeder)->run();

        foreach ($ketten as $t) {
            $this->assertSame($vorher[$t], DB::table($t)->count(),
                "Seed hat $t verändert (Kette muss unberührt bleiben)");
        }
    }
}
