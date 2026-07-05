<?php

namespace Tests\Feature\Heizlast;

use App\Models\Anforderungsprofil;
use App\Services\Anforderungsprofil\AnforderungsprofilHeizlastAdapter;
use App\Services\Anforderungsprofil\AnforderungsprofilService;
use App\Services\Heizlast\HeizlastRechner;
use App\Services\Heizlast\RaumHuelleService;
use Database\Factories\AnforderungsprofilFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * B2a-3 — Heizlast-Adapter: Byte-Beweis gegen den wb-Referenzfall (der Adapter verfälscht nichts),
 * Operanden-Gate, Datenlage-Durchreichung, Rückschreibung auf die aktive Version, Geometrie-Persistenz.
 */
class AnforderungsprofilHeizlastAdapterTest extends TestCase
{
    use RefreshDatabase;

    private function adapter(): AnforderungsprofilHeizlastAdapter
    {
        return new AnforderungsprofilHeizlastAdapter(new HeizlastRechner(new RaumHuelleService));
    }

    /** Referenz-Geometrie aus wb HeizlastRechnerTest: WZ 25 m², Wand 30/U0,28 · Fenster 6/U1,1 · Boden 25/U0,35. */
    private function wohnzimmerGeometrie(): array
    {
        return ['raeume' => [[
            'name' => 'Wohnzimmer', 'nutzung' => 'wohnen', 'grundflaeche_m2' => 25, 'hoehe_m' => 2.5,
            'bauteile' => [
                ['typ' => 'wand', 'flaeche_m2' => 30, 'u_wert' => 0.28, 'grenzflaeche' => 'aussen'],
                ['typ' => 'fenster', 'flaeche_m2' => 6, 'u_wert' => 1.1, 'grenzflaeche' => 'aussen'],
                ['typ' => 'boden', 'flaeche_m2' => 25, 'u_wert' => 0.35, 'grenzflaeche' => 'erdreich'],
            ],
        ]]];
    }

    /** @param  array<int, array<string, mixed>>  $werte */
    private function profilMitGeometrie(array $werte = [], ?array $geo = null): Anforderungsprofil
    {
        $default = [
            ['schluessel' => 'norm_aussentemp_c', 'wert' => '-12', 'wert_num' => -12, 'datenlage' => 'berechnet', 'quelle' => 'klima_plz'],
            ['schluessel' => 'komfortzuschlag_k', 'wert' => '2', 'wert_num' => 2, 'datenlage' => 'geschaetzt', 'quelle' => 'Annahme'],
        ];
        $profil = (new AnforderungsprofilService)->anlegen(AnforderungsprofilFactory::objektAnker(), 'Bestand', $werte ?: $default);
        $profil->gebaeude_geometrie = $geo ?? $this->wohnzimmerGeometrie();
        $profil->save();

        return $profil;
    }

    public function test_byte_beweis_wohnzimmer_wortgetreu(): void
    {
        $ergebnis = $this->adapter()->berechneUndSchreibe($this->profilMitGeometrie(), ['waermebruecken' => 'keine']);

        $raum = $ergebnis['raeume'][0];
        $this->assertEqualsWithDelta(17.26, $raum['h_t_w_k'], 0.05);          // H_T Anker
        $this->assertEqualsWithDelta(892, $raum['standardheizlast_w'], 2);    // Standardheizlast
        $this->assertEqualsWithDelta(0.95, $ergebnis['gebaeude']['auslegungsheizlast_kw'], 0.02);
        $this->assertEqualsWithDelta(37.9, $ergebnis['gebaeude']['spezifische_heizlast_w_m2'], 0.5);
    }

    public function test_mapping_phi_hl_ist_kw_nicht_w(): void
    {
        $profil = $this->profilMitGeometrie();
        $this->adapter()->berechneUndSchreibe($profil, ['waermebruecken' => 'keine']);

        $phi = $profil->werte()->where('schluessel', 'phi_hl_kw')->first();
        $this->assertSame('kW', $phi->einheit);
        $this->assertLessThan(10, (float) $phi->wert_num); // kW (~0,95), NICHT W (sonst ~950)
    }

    public function test_rueckschreibung_datenlage_und_parameter(): void
    {
        $profil = $this->profilMitGeometrie();
        $this->adapter()->berechneUndSchreibe($profil, ['waermebruecken' => 'keine']);

        $phi = $profil->werte()->where('schluessel', 'phi_hl_kw')->first();
        $this->assertSame('berechnet', $phi->datenlage);
        $this->assertSame('HeizlastRechner', $phi->quelle);
        $this->assertNotNull($profil->werte()->where('schluessel', 'standardheizlast_kw')->first());
        $this->assertNotNull($profil->werte()->where('schluessel', 'spezifische_heizlast_w_m2')->first());

        $wb = $profil->werte()->where('schluessel', 'waermebruecken')->first();
        $this->assertSame('geschaetzt', $wb->datenlage);
        $this->assertSame('Default', $wb->quelle);
    }

    public function test_rueckschreibung_erzeugt_keine_neue_version(): void
    {
        $profil = $this->profilMitGeometrie();
        $this->adapter()->berechneUndSchreibe($profil);

        $this->assertSame(1, Anforderungsprofil::where('verankerbar_id', $profil->verankerbar_id)->count());
        $this->assertSame(1, $profil->refresh()->version);
    }

    public function test_gate_verweigert_ohne_geometrie(): void
    {
        $profil = (new AnforderungsprofilService)->anlegen(AnforderungsprofilFactory::objektAnker(), 'x', [
            ['schluessel' => 'norm_aussentemp_c', 'wert' => '-12', 'wert_num' => -12, 'datenlage' => 'berechnet'],
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/raeume/');
        $this->adapter()->berechneUndSchreibe($profil);
    }

    public function test_gate_verweigert_ohne_norm_aussentemp(): void
    {
        $profil = (new AnforderungsprofilService)->anlegen(AnforderungsprofilFactory::objektAnker(), 'x', [
            ['schluessel' => 'komfortzuschlag_k', 'wert' => '2', 'wert_num' => 2, 'datenlage' => 'geschaetzt'],
        ]);
        $profil->gebaeude_geometrie = $this->wohnzimmerGeometrie();
        $profil->save();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/norm_aussentemp_c/');
        $this->adapter()->berechneUndSchreibe($profil);
    }

    public function test_ergebnis_hinweis_bei_ungepruefter_u_wert_datenlage(): void
    {
        $geo = $this->wohnzimmerGeometrie();
        $geo['raeume'][0]['bauteile'][0]['u_wert_datenlage'] = 'importiert_ungeprueft';
        $geo['raeume'][0]['bauteile'][1]['u_wert_datenlage'] = 'geschaetzt';

        $profil = $this->profilMitGeometrie(geo: $geo);
        $this->adapter()->berechneUndSchreibe($profil);

        $hinweis = $profil->werte()->where('schluessel', 'ergebnis_hinweis')->first();
        $this->assertNotNull($hinweis);
        $this->assertStringContainsString('ungeprueft', $hinweis->wert);
    }

    public function test_geometrie_wird_bei_neuer_version_mitkopiert(): void
    {
        $v2 = (new AnforderungsprofilService)->neueVersion($this->profilMitGeometrie());

        $this->assertNotNull($v2->gebaeude_geometrie);
        $this->assertSame('Wohnzimmer', $v2->gebaeude_geometrie['raeume'][0]['name']);
    }
}
