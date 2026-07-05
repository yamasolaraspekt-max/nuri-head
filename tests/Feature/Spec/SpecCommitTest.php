<?php

namespace Tests\Feature\Spec;

use App\Services\Spec\SpecImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Baustufe 2 — spec:import --commit-Pfad + Migrationen M-A/M-B + Batch-Rückbau.
 */
class SpecCommitTest extends TestCase
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
            'identitaet' => ['hersteller' => 'TestHersteller', 'modell' => 'TW-1', 'serie' => 'S', 'kategorie' => 'waermepumpe'],
            'fachdaten' => [
                'heizleistung_am7_w35_kw' => 6.71, 'heizleistung_a2_w35_kw' => 2.87, 'heizleistung_a7_w35_kw' => 2.84,
                'cop_am7_w35' => 2.36, 'cop_a2_w35' => 4.06, 'cop_a7_w35' => 4.85, 'aussen_heizen_min_c' => -22,
                'bauart' => 'monoblock', 'kaeltemittel' => 'R290', 'scop_35' => 4.58,
            ],
            'semantik' => ['kurve_semantik' => 'en14511_nenn'],
            'herkunft' => ['verifikations_status' => 'datenblatt_verifiziert', 'datenblatt_referenz' => 'TEST-123', 'verifikations_datum' => '2026-07-05'],
        ], $override);
    }

    private function validePv(array $override = []): array
    {
        return array_replace_recursive([
            'geraetetyp' => 'pv_modul',
            'identitaet' => ['hersteller' => 'TestPV', 'modell' => 'TP-480', 'kategorie' => 'pv_modul'],
            'fachdaten' => ['pmpp_wp' => 480, 'voc_v' => 41.3, 'vmpp_v' => 34.86, 'isc_a' => 14.38, 'impp_a' => 13.78, 'tk_voc_pct_k' => -0.22, 'u_sys_max_v' => 1500],
            'herkunft' => ['verifikations_status' => 'importiert_ungeprueft', 'datenblatt_referenz' => 'PV-1'],
        ], $override);
    }

    public function test_commit_schreibt_volle_herkunftskette(): void
    {
        $r = $this->svc()->commit('waermepumpe', [$this->valideWp()], ['imported_from' => 'test-quelle']);

        $this->assertCount(1, $r['angelegt']);
        $p = DB::table('products')->where('model', 'TW-1')->first();
        $this->assertSame('test-quelle', $p->imported_from);
        $this->assertSame($r['batchId'], $p->import_batch_id);
        $this->assertSame('datenblatt_verifiziert', $p->verifikations_status);
        $this->assertSame('TEST-123', $p->datenblatt_referenz);
        $this->assertSame('Wärmepumpe', $p->category);

        $spec = DB::table('product_heat_pump_specs')->where('product_id', $p->id)->first();
        $this->assertSame($r['batchId'], $spec->import_batch_id);
        $this->assertEqualsWithDelta(6.71, (float) $spec->heizleistung_am7_w35_kw, 0.001);
        $this->assertSame('en14511_nenn', $spec->kurve_semantik);

        $b = DB::table('spec_import_batches')->where('id', $r['batchId'])->first();
        $this->assertSame('insert', $b->modus);
        $this->assertSame(1, (int) $b->anzahl_angelegt);
    }

    public function test_idempotenz_zweiter_commit_skippt(): void
    {
        $this->svc()->commit('waermepumpe', [$this->valideWp()], []);
        $r2 = $this->svc()->commit('waermepumpe', [$this->valideWp()], []);

        $this->assertCount(0, $r2['angelegt']);
        $this->assertCount(1, $r2['geskippt']);
        $this->assertSame(1, DB::table('products')->where('model', 'TW-1')->count());
    }

    public function test_update_felddiff(): void
    {
        $this->svc()->commit('waermepumpe', [$this->valideWp(['herkunft' => ['verifikations_status' => 'importiert_ungeprueft']])], []);
        $wp = $this->valideWp(['herkunft' => ['verifikations_status' => 'importiert_ungeprueft'], 'fachdaten' => ['cop_am7_w35' => 2.5]]);

        $r = $this->svc()->commit('waermepumpe', [$wp], ['update' => true]);

        $this->assertCount(1, $r['aktualisiert']);
        $diff = $r['diffs']['TestHersteller TW-1'];
        $this->assertArrayHasKey('spec.cop_am7_w35', $diff);
        $this->assertEqualsWithDelta(2.5, (float) $diff['spec.cop_am7_w35'][1], 0.001);
    }

    public function test_downgrade_schutz_ohne_flag_bestand_unberuehrt(): void
    {
        $this->svc()->commit('waermepumpe', [$this->valideWp()], []); // Bestand datenblatt_verifiziert
        $wp = $this->valideWp(['herkunft' => ['verifikations_status' => 'importiert_ungeprueft'], 'fachdaten' => ['cop_am7_w35' => 2.5]]);

        $r = $this->svc()->commit('waermepumpe', [$wp], ['update' => true]);

        $this->assertCount(1, $r['downgradeAbbruch']);
        $this->assertCount(0, $r['aktualisiert']);
        $p = DB::table('products')->where('model', 'TW-1')->first();
        $this->assertSame('datenblatt_verifiziert', $p->verifikations_status);
        $spec = DB::table('product_heat_pump_specs')->where('product_id', $p->id)->first();
        $this->assertEqualsWithDelta(2.36, (float) $spec->cop_am7_w35, 0.001); // nicht überschrieben
    }

    public function test_downgrade_mit_flag_statuswechsel(): void
    {
        $this->svc()->commit('waermepumpe', [$this->valideWp()], []);
        $wp = $this->valideWp(['herkunft' => ['verifikations_status' => 'importiert_ungeprueft'], 'fachdaten' => ['cop_am7_w35' => 2.5]]);

        $r = $this->svc()->commit('waermepumpe', [$wp], ['update' => true, 'allow_downgrade' => true]);

        $this->assertCount(1, $r['aktualisiert']);
        $p = DB::table('products')->where('model', 'TW-1')->first();
        $this->assertSame('importiert_ungeprueft', $p->verifikations_status);
    }

    public function test_batch_rueckbau_isoliert(): void
    {
        $rA = $this->svc()->commit('waermepumpe', [$this->valideWp()], []);
        $this->svc()->commit('pv_modul', [$this->validePv()], []);

        $res = $this->svc()->rollback($rA['batchId']);

        $this->assertSame('ok', $res['status']);
        $this->assertSame(0, DB::table('products')->where('model', 'TW-1')->count(), 'Batch A weg');
        $this->assertSame(1, DB::table('products')->where('model', 'TP-480')->count(), 'Batch B unberührt');
        $this->assertSame(0, DB::table('product_heat_pump_specs')->count());
        $this->assertSame(1, DB::table('product_pv_module_specs')->count());
    }

    public function test_rollback_lehnt_update_batch_ab(): void
    {
        $this->svc()->commit('waermepumpe', [$this->valideWp(['herkunft' => ['verifikations_status' => 'importiert_ungeprueft']])], []);
        $wp = $this->valideWp(['herkunft' => ['verifikations_status' => 'importiert_ungeprueft'], 'fachdaten' => ['cop_am7_w35' => 2.5]]);
        $rU = $this->svc()->commit('waermepumpe', [$wp], ['update' => true]);

        $res = $this->svc()->rollback($rU['batchId']);

        $this->assertSame('abgelehnt', $res['status']);
        $this->assertSame(1, DB::table('products')->where('model', 'TW-1')->count(), 'Bestand nicht gelöscht');
    }

    public function test_ablehnung_schreibt_nichts(): void
    {
        $wp = $this->valideWp();
        unset($wp['herkunft']['datenblatt_referenz']); // V7-Verstoß
        $before = DB::table('products')->count();

        $r = $this->svc()->commit('waermepumpe', [$wp], []);

        $this->assertNull($r['batchId']);
        $this->assertCount(1, $r['abgelehnt']);
        $this->assertSame($before, DB::table('products')->count());
    }

    public function test_command_commit_und_rollback_e2e(): void
    {
        $pfad = tempnam(sys_get_temp_dir(), 'spec').'.json';
        file_put_contents($pfad, json_encode(['geraete' => [$this->valideWp()]]));

        $code = Artisan::call('spec:import', ['datei' => $pfad, '--typ' => 'waermepumpe', '--commit' => true, '--quelle' => 'e2e']);
        $this->assertSame(0, $code);
        $p = DB::table('products')->where('model', 'TW-1')->first();
        $this->assertNotNull($p);
        $this->assertSame('e2e', $p->imported_from);

        $code2 = Artisan::call('spec:rollback', ['batch_id' => $p->import_batch_id]);
        $this->assertSame(0, $code2);
        $this->assertSame(0, DB::table('products')->where('model', 'TW-1')->count());
        @unlink($pfad);
    }
}
