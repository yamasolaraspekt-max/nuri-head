<?php

namespace Tests\Feature\Heizlast;

use App\Services\Anforderungsprofil\AnforderungsprofilService;
use App\Services\Anforderungsprofil\AnforderungsprofilUWertAdapter;
use App\Services\Heizlast\UWertService;
use Database\Factories\AnforderungsprofilFactory;
use Database\Seeders\ReferenzKatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * B2b-A — UWert: 6 harte Anker über UWertService (byte-genau) + Adapter-Integration (baujahr→u_wert
 * in die Geometrie, Strategie C). ausSchichten/fenster sind pure; ausBaualter nutzt baualtersklassen (B2a-1).
 */
class AnforderungsprofilUWertAdapterTest extends TestCase
{
    use RefreshDatabase;

    private function uwert(): UWertService
    {
        return new UWertService;
    }

    public function test_byte_beweis_ausschichten_ziegel(): void
    {
        // Vollziegel 32 cm + Innenputz 1,5 + Außenputz 2 → U ≈ 1,45
        $r = $this->uwert()->ausSchichten([
            ['lambda' => 0.68, 'dicke_mm' => 320],
            ['lambda' => 0.51, 'dicke_mm' => 15],
            ['lambda' => 1.00, 'dicke_mm' => 20],
        ], 'wand');
        $this->assertEqualsWithDelta(1.45, $r['u_wert'], 0.02);
        $this->assertSame('B', $r['eingabe_strategie']);
    }

    public function test_byte_beweis_ausschichten_gedaemmt(): void
    {
        // + 10 cm EPS → U ≈ 0,28
        $r = $this->uwert()->ausSchichten([
            ['lambda' => 0.68, 'dicke_mm' => 320],
            ['lambda' => 0.51, 'dicke_mm' => 15],
            ['lambda' => 0.035, 'dicke_mm' => 100],
        ], 'wand');
        $this->assertEqualsWithDelta(0.28, $r['u_wert'], 0.015);
    }

    public function test_byte_beweis_fenster_konstante(): void
    {
        $this->assertSame(0.8, $this->uwert()->fenster('3fach')['u_wert']);   // U_3fach-Anker
        $this->assertSame(5.0, $this->uwert()->fenster('einfachglas')['u_wert']);
    }

    public function test_byte_beweis_ausbaualter_fenster(): void
    {
        $this->seed(ReferenzKatalogSeeder::class); // baualtersklassen
        $this->assertEqualsWithDelta(1.10, $this->uwert()->ausBaualter(2020, 'fenster')['u_wert'], 0.001);
    }

    public function test_adapter_ergaenzt_u_wert_in_geometrie(): void
    {
        $this->seed(ReferenzKatalogSeeder::class);
        $profil = (new AnforderungsprofilService)->anlegen(AnforderungsprofilFactory::objektAnker(), 'Bestand', [
            ['schluessel' => 'baujahr', 'wert' => '1980', 'wert_num' => 1980, 'datenlage' => 'gemessen'],
            ['schluessel' => 'sanierungsstufe', 'wert' => 'unsaniert', 'datenlage' => 'gemessen'],
        ]);
        $profil->gebaeude_geometrie = ['raeume' => [[
            'name' => 'WZ', 'grundflaeche_m2' => 25,
            'bauteile' => [['typ' => 'wand', 'flaeche_m2' => 30, 'grenzflaeche' => 'aussen']], // KEIN u_wert
        ]]];
        $profil->save();

        $r = (new AnforderungsprofilUWertAdapter($this->uwert()))->berechneUndSchreibe($profil);

        $this->assertSame(1, $r['ergaenzt']);
        $bauteil = $profil->fresh()->gebaeude_geometrie['raeume'][0]['bauteile'][0];
        $this->assertArrayHasKey('u_wert', $bauteil);
        $this->assertGreaterThan(0, $bauteil['u_wert']);
        $this->assertSame('tabula_richtwert', $bauteil['u_wert_datenlage']); // Datenlage-Durchreichung W-B2a-4
    }

    public function test_adapter_ueberschreibt_vorhandene_u_werte_nicht(): void
    {
        $this->seed(ReferenzKatalogSeeder::class);
        $profil = (new AnforderungsprofilService)->anlegen(AnforderungsprofilFactory::objektAnker(), 'x', [
            ['schluessel' => 'baujahr', 'wert' => '1980', 'wert_num' => 1980, 'datenlage' => 'gemessen'],
        ]);
        $profil->gebaeude_geometrie = ['raeume' => [[
            'name' => 'WZ', 'bauteile' => [['typ' => 'wand', 'u_wert' => 0.24, 'grenzflaeche' => 'aussen']], // verifiziert
        ]]];
        $profil->save();

        $r = (new AnforderungsprofilUWertAdapter($this->uwert()))->berechneUndSchreibe($profil);

        $this->assertSame(0, $r['ergaenzt']); // nichts überschrieben
        $this->assertEqualsWithDelta(0.24, $profil->fresh()->gebaeude_geometrie['raeume'][0]['bauteile'][0]['u_wert'], 0.001);
    }

    public function test_gate_ohne_baujahr(): void
    {
        $profil = (new AnforderungsprofilService)->anlegen(AnforderungsprofilFactory::objektAnker(), 'x', [
            ['schluessel' => 'sanierungsstufe', 'wert' => 'unsaniert', 'datenlage' => 'gemessen'],
        ]);
        $profil->gebaeude_geometrie = ['raeume' => [['bauteile' => [['typ' => 'wand']]]]];
        $profil->save();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/baujahr/');
        (new AnforderungsprofilUWertAdapter($this->uwert()))->berechneUndSchreibe($profil);
    }

    /** Guard: kein aktiver UWert-Pfad berührt fenster_specs (Tabelle bewusst NICHT vorhanden). */
    public function test_guard_kein_pfad_beruehrt_fenster_specs(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasTable('fenster_specs'), 'fenster_specs bleibt bewusst ungebaut');
        // Der U_3fach-Anker kommt trotzdem — aus der Konstante FENSTER_U, nicht aus FensterSpec:
        $this->assertSame(0.8, $this->uwert()->fenster('3fach')['u_wert']);
    }
}
