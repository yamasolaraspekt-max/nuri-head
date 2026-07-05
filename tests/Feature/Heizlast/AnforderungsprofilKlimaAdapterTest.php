<?php

namespace Tests\Feature\Heizlast;

use App\Models\Anforderungsprofil;
use App\Services\Anforderungsprofil\AnforderungsprofilHeizlastAdapter;
use App\Services\Anforderungsprofil\AnforderungsprofilKlimaAdapter;
use App\Services\Anforderungsprofil\AnforderungsprofilService;
use App\Services\Heizlast\HeizlastRechner;
use App\Services\Heizlast\HoehenkorrekturService;
use App\Services\Heizlast\KlimaPlzService;
use App\Services\Heizlast\RaumHuelleService;
use App\Services\Heizlast\WarmwasserService;
use Database\Factories\AnforderungsprofilFactory;
use Database\Seeders\ReferenzKatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * B2b-C — Klima-/WW-Adapter: Byte-Beweis θe (KlimaPlz-CSV), Höhenkorrektur, Gate, WW,
 * Integration (schließt das B2a-3-Gate) + Auflage C1.1 (CSV == klima_plz-Tabelle).
 */
class AnforderungsprofilKlimaAdapterTest extends TestCase
{
    use RefreshDatabase;

    private function adapter(): AnforderungsprofilKlimaAdapter
    {
        return new AnforderungsprofilKlimaAdapter(new KlimaPlzService, new HoehenkorrekturService, new WarmwasserService);
    }

    /** @param  array<int, array<string, mixed>>  $werte */
    private function profil(array $werte): Anforderungsprofil
    {
        return (new AnforderungsprofilService)->anlegen(AnforderungsprofilFactory::objektAnker(), 'Bestand', $werte);
    }

    public function test_byte_beweis_norm_aussentemp_dresden(): void
    {
        $profil = $this->profil([
            ['schluessel' => 'standort_plz', 'wert' => '01067', 'datenlage' => 'gemessen', 'quelle' => 'Adresse'],
        ]);

        $r = $this->adapter()->berechneUndSchreibe($profil);

        $this->assertEqualsWithDelta(-8.5, $r['norm_aussentemp_c'], 0.01); // Dresden nat_c
        $wert = $profil->werte()->where('schluessel', 'norm_aussentemp_c')->first();
        $this->assertSame('berechnet', $wert->datenlage);
        $this->assertEqualsWithDelta(-8.5, (float) $wert->wert_num, 0.01);
    }

    public function test_byte_beweis_hoehenkorrektur(): void
    {
        // Dresden nat_c=-8,5 / CSV-Bezug 111 m; Standort 611 m (Δ500 ≥ 200) → θe = -8,5 - 0,01·500 = -13,5 °C
        $profil = $this->profil([
            ['schluessel' => 'standort_plz', 'wert' => '01067', 'datenlage' => 'gemessen'],
            ['schluessel' => 'gelaendehoehe_m', 'wert' => '611', 'wert_num' => 611, 'datenlage' => 'gemessen'],
        ]);

        $r = $this->adapter()->berechneUndSchreibe($profil);

        $this->assertEqualsWithDelta(-13.5, $r['norm_aussentemp_c'], 0.01); // exakter Höhen-Anker
    }

    public function test_gate_ohne_plz(): void
    {
        $profil = $this->profil([['schluessel' => 'komfortzuschlag_k', 'wert' => '2', 'wert_num' => 2, 'datenlage' => 'geschaetzt']]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/standort_plz/');
        $this->adapter()->berechneUndSchreibe($profil);
    }

    public function test_gate_unbekannte_plz(): void
    {
        $profil = $this->profil([['schluessel' => 'standort_plz', 'wert' => '99999', 'datenlage' => 'gemessen']]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/nicht in klima_plz/');
        $this->adapter()->berechneUndSchreibe($profil);
    }

    public function test_byte_beweis_warmwasser(): void
    {
        // WW_KW_PRO_PERSON=0,20 → 4 Personen × 0,20 = 0,80 kW (ww_mit_wp=true)
        $profil = $this->profil([
            ['schluessel' => 'standort_plz', 'wert' => '01067', 'datenlage' => 'gemessen'],
            ['schluessel' => 'personen_im_haushalt', 'wert' => '4', 'wert_num' => 4, 'datenlage' => 'gemessen'],
        ]);

        $r = $this->adapter()->berechneUndSchreibe($profil);

        $this->assertEqualsWithDelta(0.80, $r['phi_ww_kw'], 0.001); // exakter WW-Anker
        $this->assertSame('berechnet', $profil->werte()->where('schluessel', 'phi_ww_kw')->first()->datenlage);
    }

    public function test_klima_schliesst_das_heizlast_gate(): void
    {
        $profil = (new AnforderungsprofilService)->anlegen(AnforderungsprofilFactory::objektAnker(), 'x', [
            ['schluessel' => 'standort_plz', 'wert' => '01067', 'datenlage' => 'gemessen'],
        ]);
        $profil->gebaeude_geometrie = ['raeume' => [[
            'name' => 'WZ', 'nutzung' => 'wohnen', 'grundflaeche_m2' => 25, 'hoehe_m' => 2.5,
            'bauteile' => [['typ' => 'wand', 'flaeche_m2' => 30, 'u_wert' => 0.28, 'grenzflaeche' => 'aussen']],
        ]]];
        $profil->save();

        $heizlast = new AnforderungsprofilHeizlastAdapter(new HeizlastRechner(new RaumHuelleService));

        // vor Klima: Gate schließt (norm_aussentemp_c fehlt)
        try {
            $heizlast->berechneUndSchreibe($profil);
            $this->fail('Gate hätte schließen müssen');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('norm_aussentemp_c', $e->getMessage());
        }

        // Klima liefert θe → Heizlast rechnet jetzt
        $this->adapter()->berechneUndSchreibe($profil);
        $r = $heizlast->berechneUndSchreibe($profil->fresh());
        $this->assertArrayHasKey('gebaeude', $r);
    }

    public function test_identitaet_csv_gegen_tabelle_c1_1(): void
    {
        $this->seed(ReferenzKatalogSeeder::class);

        $this->assertSame(8168, DB::table('klima_plz')->count()); // Zeilenzahl CSV == Tabelle
        $tabelle = DB::table('klima_plz')->where('plz', '01067')->first();
        $csv = (new KlimaPlzService)->lookup('01067');
        $this->assertEqualsWithDelta((float) $tabelle->nat_c, $csv['nat_c'], 0.001); // Wert-Identität
        $this->assertEqualsWithDelta(-8.5, $csv['nat_c'], 0.01);
    }
}
