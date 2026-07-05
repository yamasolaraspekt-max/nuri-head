<?php

namespace Tests\Feature\Spec;

use App\Services\Spec\SpecImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Baustufe 1 — SpecSchema-Regelquelle (V1–V7) + spec:import --dry-run. Kein Write.
 */
class SpecImportTest extends TestCase
{
    use RefreshDatabase;

    private function svc(): SpecImportService
    {
        return app(SpecImportService::class);
    }

    private function valideWp(array $override = []): array
    {
        return array_replace_recursive([
            'geraetetyp' => 'waermepumpe',
            'identitaet' => ['hersteller' => 'TestHersteller', 'modell' => 'TW-1', 'kategorie' => 'waermepumpe'],
            'fachdaten' => [
                'heizleistung_am7_w35_kw' => 6.71, 'heizleistung_a2_w35_kw' => 2.87, 'heizleistung_a7_w35_kw' => 2.84,
                'cop_am7_w35' => 2.36, 'cop_a2_w35' => 4.06, 'cop_a7_w35' => 4.85, 'aussen_heizen_min_c' => -22,
            ],
            'semantik' => ['kurve_semantik' => 'en14511_nenn'],
            'herkunft' => ['verifikations_status' => 'datenblatt_verifiziert', 'datenblatt_referenz' => 'TEST-123'],
        ], $override);
    }

    private function validePv(array $override = []): array
    {
        return array_replace_recursive([
            'geraetetyp' => 'pv_modul',
            'identitaet' => ['hersteller' => 'TestPV', 'modell' => 'TP-480', 'kategorie' => 'pv_modul'],
            'fachdaten' => [
                'pmpp_wp' => 480, 'voc_v' => 41.3, 'vmpp_v' => 34.86, 'isc_a' => 14.38, 'impp_a' => 13.78,
                'tk_voc_pct_k' => -0.22, 'u_sys_max_v' => 1500,
            ],
            'herkunft' => ['verifikations_status' => 'importiert_ungeprueft', 'datenblatt_referenz' => 'PV-1'],
        ], $override);
    }

    public function test_valide_wp_keine_fehler(): void
    {
        $this->assertSame([], $this->svc()->validate('waermepumpe', $this->valideWp()));
    }

    public function test_valide_pv_keine_fehler(): void
    {
        $this->assertSame([], $this->svc()->validate('pv_modul', $this->validePv()));
    }

    public function test_v5_identitaet_modell_fehlt(): void
    {
        $wp = $this->valideWp();
        unset($wp['identitaet']['modell']);
        $this->assertStringContainsString('V5', implode(' ', $this->svc()->validate('waermepumpe', $wp)));
    }

    public function test_v2_heizleistung_in_watt_statt_kw_abgelehnt(): void
    {
        $wp = $this->valideWp(['fachdaten' => ['heizleistung_am7_w35_kw' => 6710]]); // W statt kW
        $this->assertStringContainsString('V2', implode(' ', $this->svc()->validate('waermepumpe', $wp)));
    }

    public function test_v6_unbekanntes_feld_abgelehnt(): void
    {
        $wp = $this->valideWp(['fachdaten' => ['foo_bar_x' => 1]]);
        $this->assertStringContainsString('V6', implode(' ', $this->svc()->validate('waermepumpe', $wp)));
    }

    public function test_v1_betriebspunkt_nur_paarweise(): void
    {
        // optionales W55-Paar: kW gesetzt, COP fehlt -> nur V1, ohne die Pflicht-Gruppe zu treffen
        $wp = $this->valideWp(['fachdaten' => ['heizleistung_am7_w55_kw' => 5.0]]);
        $this->assertStringContainsString('V1', implode(' ', $this->svc()->validate('waermepumpe', $wp)));
    }

    public function test_v4_kurve_semantik_pflicht_bei_spalten(): void
    {
        $wp = $this->valideWp();
        unset($wp['semantik']['kurve_semantik']);
        $this->assertStringContainsString('V4', implode(' ', $this->svc()->validate('waermepumpe', $wp)));
    }

    public function test_v7_datenblatt_referenz_pflicht(): void
    {
        $wp = $this->valideWp();
        unset($wp['herkunft']['datenblatt_referenz']);
        $this->assertStringContainsString('V7', implode(' ', $this->svc()->validate('waermepumpe', $wp)));
    }

    public function test_v7_verifikations_status_ungueltig(): void
    {
        $wp = $this->valideWp(['herkunft' => ['verifikations_status' => 'phantasie']]);
        $this->assertStringContainsString('V7', implode(' ', $this->svc()->validate('waermepumpe', $wp)));
    }

    public function test_pflicht_alternativ_ohne_stuetzpunkte_und_kurve(): void
    {
        $wp = $this->valideWp();
        $wp['fachdaten'] = ['aussen_heizen_min_c' => -22]; // weder 6 Punkte noch leistungskurve
        $this->assertNotEmpty($this->svc()->validate('waermepumpe', $wp));
    }

    public function test_dryrun_dedup_geskippt(): void
    {
        // Bestand anlegen -> derselbe (hersteller,modell) wird geskippt, nicht angelegt
        $status = DB::table('brands')->whereNotNull('status')->value('status') ?? 'active';
        $bid = DB::table('brands')->insertGetId(['name' => 'TestHersteller', 'status' => $status, 'created_at' => now(), 'updated_at' => now()]);
        $ag = DB::table('article_groups')->insertGetId(['article_group' => 'X', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('products')->insert(['brand_id' => $bid, 'article_group' => (string) $ag, 'product' => 'B', 'model' => 'TW-1', 'created_at' => now(), 'updated_at' => now()]);

        $report = $this->svc()->dryRun('waermepumpe', [$this->valideWp()]);
        $this->assertSame([], $report['angelegt']);
        $this->assertCount(1, $report['geskippt']);
    }

    public function test_command_dry_run_kein_write(): void
    {
        $before = DB::table('products')->count();
        $pfad = tempnam(sys_get_temp_dir(), 'spec').'.json';
        file_put_contents($pfad, json_encode(['geraete' => [$this->valideWp()]]));

        $code = Artisan::call('spec:import', ['datei' => $pfad, '--typ' => 'waermepumpe']);

        $this->assertSame(0, $code, 'valide WP -> exit 0');
        $this->assertSame($before, DB::table('products')->count(), 'Dry-Run schreibt nichts');
        @unlink($pfad);
    }

    public function test_command_abgelehnt_gibt_exit_1(): void
    {
        $pfad = tempnam(sys_get_temp_dir(), 'spec').'.json';
        $wp = $this->valideWp();
        unset($wp['herkunft']['datenblatt_referenz']); // V7-Verstoß
        file_put_contents($pfad, json_encode(['geraete' => [$wp]]));

        $code = Artisan::call('spec:import', ['datei' => $pfad, '--typ' => 'waermepumpe']);

        $this->assertSame(1, $code, 'Ablehnung -> exit 1');
        @unlink($pfad);
    }
}
