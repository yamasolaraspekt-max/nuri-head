<?php

namespace Tests\Feature\Geometrie;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\HeizlastProjekt;
use App\Models\HeizlastRaum;
use App\Models\RaumGeometrie;
use App\Models\User;
use App\Services\Heizlast\GeometrieAbleitungService;
use App\Services\Heizlast\HeizlastProjektService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * AP-4b — Referenztest-Transplantation aus wberechnung (tests/Feature/GrundrissEditorTest.php +
 * tests/Unit/GeometrieAbleitungTest.php), test/spec-only: KEIN Produktivcode geändert.
 *
 * Kernaussagen der wb-Referenz, hier auf die ticket-Pfade (energie.grundriss.*) übertragen:
 * 1. Geometrie-Raum ≡ Masken-Raum — die Engine (HeizlastProjektService) unterscheidet die
 *    Eingangswege NICHT: identische Flächen/U-Werte/Grenzflächen ⇒ identische Kennzahlen.
 * 2. Vorschau eines 5×6-Rechtecks = 30,00 m² mit Φ_HL > 0 über den echten HTTP-Pfad.
 * 3. Fenster-U-Wert wirkt richtungsrichtig auf Φ_HL (Einfachglas > ohne Fenster > 3-fach).
 * 4. offset_mm einer Öffnung ist display-only ⇒ Φ_HL-invariant (ticket normalisiert ihn weg —
 *    dieser Test friert genau diesen Vertrag ein).
 * 5. Freihand-Polygon: Startecke egal ⇒ identisches Φ_HL.
 * 6. schreibeInProjekt persistiert die abgeleiteten Brutto-Bauteile und bleibt über den
 *    Engine-Pfad (fuerProjekt) rechenbar.
 */
class GrundrissReferenzTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->actingAs(User::factory()->create(['password' => 'password', 'name' => 'georeftest', 'is_admin' => true]));
    }

    // ------------------------------------------------------------------
    // Payload-Bausteine (wb-Referenzgeometrie: Rechteck 5×6 m, 2,5 m hoch)
    // ------------------------------------------------------------------

    /**
     * Gezeichnetes Rechteck 5×6 m, 4 Außenwände U 1,4, Dach U 0,3, Boden/Erdreich U 0,4.
     *
     * @return array<string, mixed>
     */
    private function rechteckPayload(): array
    {
        $ecken = [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 5000, 'y' => 6000], ['x' => 0, 'y' => 6000]];

        return array_merge(
            ['name' => 'Wohnen', 'nutzung' => 'wohnen', 'hoehe_mm' => 2500, 'geschoss' => 0],
            $this->polygonMitSegmenten($ecken),
            [
                'decke' => ['bauteil_typ' => 'dach', 'grenzflaeche' => 'aussen', 'u_wert' => 0.3],
                'boden' => ['bauteil_typ' => 'boden', 'grenzflaeche' => 'erdreich', 'u_wert' => 0.4],
            ],
        );
    }

    /**
     * @param  array<int, array{x: int, y: int}>  $ecken
     * @return array<string, mixed>
     */
    private function polygonMitSegmenten(array $ecken): array
    {
        $n = count($ecken);
        $segmente = [];
        foreach ($ecken as $i => $von) {
            $segmente[] = [
                'von' => $von, 'bis' => $ecken[($i + 1) % $n],
                'grenzflaeche' => 'aussen', 'azimut_grad' => 90 * $i, 'bauteil_typ' => 'wand', 'u_wert' => 1.4,
            ];
        }

        return ['polygon' => $ecken, 'wand_segmente' => $segmente];
    }

    /**
     * Rechteck mit einem Fenster (1200×1400) auf der ersten Wand.
     *
     * @return array<string, mixed>
     */
    private function rechteckMitFenster(float $uFenster, int $offset = 1500): array
    {
        $payload = $this->rechteckPayload();
        $payload['wand_segmente'][0]['oeffnungen'] = [[
            'offset_mm' => $offset, 'breite_mm' => 1200, 'hoehe_mm' => 1400, 'typ' => 'fenster', 'u_wert' => $uFenster,
        ]];

        return $payload;
    }

    /** Φ_HL (kW) über den echten HTTP-Pfad der Vorschau (ticket: flache JSON-Keys). */
    private function phiHl(array $payload): float
    {
        $res = $this->postJson(route('energie.grundriss.vorschau'), $payload);
        $res->assertOk();

        return (float) $res->json('auslegungsheizlast_kw');
    }

    // ------------------------------------------------------------------
    // 1 · Geometrie ≡ Maske (Engine-Äquivalenz, Kern der wb-Referenz)
    // ------------------------------------------------------------------

    /**
     * Die Engine unterscheidet Geometrie- und Masken-Eingang nicht: ein aus der Geometrie
     * abgeleiteter Raum und ein von Hand gebauter Maskenraum mit identischen Flächen/U-Werten/
     * Grenzflächen liefern DIESELBEN Kennzahlen (assertSame, kein Delta).
     */
    public function test_geometrie_raum_ist_aequivalent_zum_maskenraum(): void
    {
        // Geometrie-Raum: Rechteck 5×6 m, 2,5 m hoch, 4 Außenwände (U 1,4), 1 Südfenster (U 1,3),
        // Dach (U 0,3), Boden gegen Erdreich (U 0,4) — in-memory, ohne Persistenz.
        $g = new RaumGeometrie;
        $g->polygon = [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 5000, 'y' => 6000], ['x' => 0, 'y' => 6000]];
        $g->hoehe_mm = 2500;
        $g->wand_segmente = [
            ['von' => ['x' => 0, 'y' => 0], 'bis' => ['x' => 5000, 'y' => 0], 'grenzflaeche' => 'aussen', 'azimut_grad' => 180, 'u_wert' => 1.4,
                'oeffnungen' => [['breite_mm' => 1200, 'hoehe_mm' => 1400, 'typ' => 'fenster', 'u_wert' => 1.3]]],
            ['von' => ['x' => 5000, 'y' => 0], 'bis' => ['x' => 5000, 'y' => 6000], 'grenzflaeche' => 'aussen', 'azimut_grad' => 270, 'u_wert' => 1.4],
            ['von' => ['x' => 5000, 'y' => 6000], 'bis' => ['x' => 0, 'y' => 6000], 'grenzflaeche' => 'aussen', 'azimut_grad' => 0, 'u_wert' => 1.4],
            ['von' => ['x' => 0, 'y' => 6000], 'bis' => ['x' => 0, 'y' => 0], 'grenzflaeche' => 'aussen', 'azimut_grad' => 90, 'u_wert' => 1.4],
        ];
        $g->decke = ['bauteil_typ' => 'dach', 'grenzflaeche' => 'aussen', 'u_wert' => 0.3];
        $g->boden = ['bauteil_typ' => 'boden', 'grenzflaeche' => 'erdreich', 'u_wert' => 0.4];
        $raum = new HeizlastRaum;
        $raum->name = 'Raum';
        $raum->nutzung = 'wohnen';
        $g->setRelation('heizlastRaum', $raum);

        $geoRaum = app(GeometrieAbleitungService::class)->ausGeometrie($g);

        // Äquivalenter Maskenraum (identische Flächen/U-Werte/Grenzflächen, handgerechnet:
        // 2× 5,0·2,5 = 12,5 m², 2× 6,0·2,5 = 15,0 m², Fenster 1,2·1,4 = 1,68 m², Dach/Boden 30 m²).
        $maskRaum = [
            'name' => 'Raum', 'nutzung' => 'wohnen', 'grundflaeche_m2' => 30.0, 'hoehe_m' => 2.5,
            'bauteile' => [
                ['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 12.5, 'u_strategie' => 'A', 'u_wert' => 1.4],
                ['typ' => 'fenster', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 1.68, 'u_strategie' => 'A', 'u_wert' => 1.3],
                ['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 15.0, 'u_strategie' => 'A', 'u_wert' => 1.4],
                ['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 12.5, 'u_strategie' => 'A', 'u_wert' => 1.4],
                ['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 15.0, 'u_strategie' => 'A', 'u_wert' => 1.4],
                ['typ' => 'dach', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 30.0, 'u_strategie' => 'A', 'u_wert' => 0.3],
                ['typ' => 'boden', 'grenzflaeche' => 'erdreich', 'flaeche_m2' => 30.0, 'u_strategie' => 'A', 'u_wert' => 0.4],
            ],
        ];

        $svc = app(HeizlastProjektService::class);
        $eingabe = fn (array $r): array => [
            'norm_aussentemp_c' => -12.0, 'waermebruecken' => 'keine', 'komfortzuschlag_k' => 0, 'raeume' => [$r],
        ];
        $resGeo = $svc->berechne($eingabe($geoRaum));
        $resMask = $svc->berechne($eingabe($maskRaum));

        $this->assertSame($resMask['gebaeude']['auslegungsheizlast_kw'], $resGeo['gebaeude']['auslegungsheizlast_kw']);
        $this->assertSame($resMask['raeume'][0]['h_t_w_k'], $resGeo['raeume'][0]['h_t_w_k']);
        $this->assertSame($resMask['raeume'][0]['h_v_w_k'], $resGeo['raeume'][0]['h_v_w_k']);
    }

    // ------------------------------------------------------------------
    // 2 · HTTP-Referenzen über energie.grundriss.vorschau
    // ------------------------------------------------------------------

    public function test_vorschau_rechteck_ist_30_qm_mit_positiver_heizlast(): void
    {
        $res = $this->postJson(route('energie.grundriss.vorschau'), $this->rechteckPayload());

        $res->assertOk();
        $this->assertEqualsWithDelta(30.0, (float) $res->json('grundflaeche_m2'), 0.05);
        $this->assertGreaterThan(0, (float) $res->json('auslegungsheizlast_kw'));
        // 4 Wände + 1× Dach + 1× Boden = 6 abgeleitete Bauteile
        $this->assertSame(6, (int) $res->json('bauteil_anzahl'));
    }

    public function test_fenster_u_wert_wirkt_richtungsrichtig_auf_phi_hl(): void
    {
        $ohne = $this->phiHl($this->rechteckPayload());
        $hoch = $this->phiHl($this->rechteckMitFenster(5.0));    // Einfachglas verdrängt U-1,4-Wand → mehr Verlust
        $niedrig = $this->phiHl($this->rechteckMitFenster(0.8)); // 3-fach verdrängt U-1,4-Wand → weniger Verlust

        $this->assertGreaterThan($ohne, $hoch);
        $this->assertLessThan($ohne, $niedrig);
    }

    public function test_offset_einer_oeffnung_aendert_phi_hl_nicht(): void
    {
        // offset_mm ist display-only (ticket normalisiert ihn vor der Engine weg) → identisches Φ_HL.
        $this->assertSame($this->phiHl($this->rechteckMitFenster(1.3, 500)), $this->phiHl($this->rechteckMitFenster(1.3, 3000)));
    }

    public function test_freihand_polygon_startecke_egal_aequivalent(): void
    {
        // Freihand kann an jeder Ecke starten → gleiche Form, andere Reihenfolge → identisches Φ_HL.
        $a = [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 5000, 'y' => 6000], ['x' => 0, 'y' => 6000]];
        $b = [['x' => 5000, 'y' => 6000], ['x' => 0, 'y' => 6000], ['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0]];

        $basis = $this->rechteckPayload();
        $this->assertSame(
            $this->phiHl(array_merge($basis, $this->polygonMitSegmenten($a))),
            $this->phiHl(array_merge($basis, $this->polygonMitSegmenten($b))),
        );
    }

    // ------------------------------------------------------------------
    // 3 · Engine-Pfad-Persistenz (wb: test_schreibe_in_projekt_persistiert_ueber_engine_pfad)
    // ------------------------------------------------------------------

    public function test_schreibe_in_projekt_persistiert_ueber_engine_pfad(): void
    {
        $projekt = HeizlastProjekt::create(['name' => 'Geo-Referenz']);
        $raum = HeizlastRaum::create([
            'heizlast_projekt_id' => $projekt->getKey(),
            'name' => 'Raum', 'nutzung' => 'wohnen', 'grundflaeche_m2' => 1, 'hoehe_m' => 2.5,
        ]);
        $raum->geometrie()->create([
            'hoehe_mm' => 2500,
            'polygon' => [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 5000, 'y' => 6000], ['x' => 0, 'y' => 6000]],
            'wand_segmente' => [
                ['von' => ['x' => 0, 'y' => 0], 'bis' => ['x' => 5000, 'y' => 0], 'grenzflaeche' => 'aussen', 'u_wert' => 1.4],
                ['von' => ['x' => 5000, 'y' => 0], 'bis' => ['x' => 5000, 'y' => 6000], 'grenzflaeche' => 'aussen', 'u_wert' => 1.4],
            ],
        ]);

        app(GeometrieAbleitungService::class)->schreibeInProjekt($projekt->fresh());

        $raum = $projekt->fresh()->raeume()->first();
        $this->assertEqualsWithDelta(30.0, (float) $raum->grundflaeche_m2, 0.01);  // Grundfläche aus der Geometrie
        $this->assertSame(2, $raum->bauteile()->count());                          // 2 Wände, brutto
        $this->assertEqualsWithDelta(12.5, (float) $raum->bauteile()->first()->flaeche_m2, 0.01);

        // Über den Engine-Pfad rechenbar.
        $r = app(HeizlastProjektService::class)->fuerProjekt($projekt->fresh());
        $this->assertGreaterThan(0, (float) $r['gebaeude']['auslegungsheizlast_kw']);
    }
}
