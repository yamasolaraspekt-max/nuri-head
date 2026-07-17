<?php

namespace Tests\Unit\Geometrie;

use App\Models\HeizlastRaum;
use App\Models\RaumGeometrie;
use App\Services\Geometrie\GeometrieUngueltigException;
use App\Services\Geometrie\SzeneProjektionService;
use App\Services\Heizlast\GeometrieAbleitungService;
use Tests\TestCase;

/**
 * AP/P2-1a — Tests für die reine, unverdrahtete Projektion Szene → gebaeude_geometrie.
 * Grundlage: docs/planner-spec-szene-projektion.md. Kein DB-Schreiben; Tests\TestCase (bootet App
 * für Eloquent-Casts beim Round-Trip), ohne RefreshDatabase — kein DB-Zugriff.
 */
class SzeneProjektionServiceTest extends TestCase
{
    private function service(): SzeneProjektionService
    {
        return new SzeneProjektionService();
    }

    /** @return array<string,mixed> */
    private function wall(string $id, int $sx, int $sy, int $ex, int $ey): array
    {
        return [
            'id' => $id, 'type' => 'wall', 'levelId' => 'level-eg',
            'start' => ['x' => $sx, 'y' => $sy], 'end' => ['x' => $ex, 'y' => $ey],
            'thickness' => 200, 'height' => 2500, 'visible' => true, 'locked' => false, 'tags' => [],
        ];
    }

    /**
     * Rechteck 5000 × 5000 mm, ein Geschoss. @param array<int,array<string,mixed>> $extra
     * @return array<string,mixed>
     */
    private function rechteckSzene(array $extra = []): array
    {
        return [
            'schemaVersion' => 1, 'units' => 'mm',
            'levels' => [['id' => 'level-eg', 'name' => 'Erdgeschoss', 'sortOrder' => 0, 'defaultWallHeight' => 2500]],
            'nodes' => array_merge([
                $this->wall('w1', 0, 0, 5000, 0),
                $this->wall('w2', 5000, 0, 5000, 5000),
                $this->wall('w3', 5000, 5000, 0, 5000),
                $this->wall('w4', 0, 5000, 0, 0),
            ], $extra),
        ];
    }

    public function test_ein_raum_rechteck_vier_aussenwaende_korrekte_azimute(): void
    {
        $raeume = $this->service()->projiziere($this->rechteckSzene());

        $this->assertCount(1, $raeume);
        $raum = $raeume[0];
        $this->assertCount(4, $raum['polygon']);
        $this->assertCount(4, $raum['wand_segmente']);
        $this->assertSame(2500, $raum['hoehe_mm']);
        $this->assertSame(0, $raum['geschoss']);
        $this->assertNull($raum['decke']);
        $this->assertNull($raum['boden']);

        foreach ($raum['wand_segmente'] as $seg) {
            $this->assertSame('aussen', $seg['grenzflaeche']);
            $this->assertSame('wand', $seg['bauteil_typ']);
            $this->assertSame([], $seg['oeffnungen']);
        }

        $azimute = array_map(fn ($s) => $s['azimut_grad'], $raum['wand_segmente']);
        sort($azimute);
        $this->assertSame([0, 90, 180, 270], $azimute); // N / O / S / W (Nord = +y)
    }

    public function test_oeffnung_wird_der_wirtswand_zugeordnet(): void
    {
        $fenster = [
            'id' => 'o1', 'type' => 'window', 'levelId' => 'level-eg', 'hostWallId' => 'w1',
            'offsetFromWallStart' => 1000, 'width' => 1200, 'height' => 1400, 'sillHeight' => 900,
            'visible' => true, 'locked' => false, 'tags' => [],
        ];

        $raeume = $this->service()->projiziere($this->rechteckSzene([$fenster]));
        $alle = array_merge(...array_map(fn ($s) => $s['oeffnungen'], $raeume[0]['wand_segmente']));

        $this->assertCount(1, $alle);
        $this->assertSame('fenster', $alle[0]['typ']);
        $this->assertSame(1200, $alle[0]['breite_mm']);
        $this->assertSame(1400, $alle[0]['hoehe_mm']);
        $this->assertSame(900, $alle[0]['bruestung_mm']);
    }

    public function test_ausgabe_fuettert_ausGeometrie_ohne_formatbruch(): void
    {
        $raum = $this->service()->projiziere($this->rechteckSzene())[0];

        $geo = new RaumGeometrie([
            'polygon' => $raum['polygon'],
            'hoehe_mm' => $raum['hoehe_mm'],
            'wand_segmente' => $raum['wand_segmente'],
        ]);
        $geo->setRelation('heizlastRaum', new HeizlastRaum());

        $abgeleitet = (new GeometrieAbleitungService())->ausGeometrie($geo);

        $this->assertCount(4, $abgeleitet['bauteile']);
        foreach ($abgeleitet['bauteile'] as $b) {
            $this->assertSame('wand', $b['typ']);
            $this->assertGreaterThan(0, $b['flaeche_m2']);
        }
        $this->assertEqualsWithDelta(12.5, $abgeleitet['bauteile'][0]['flaeche_m2'], 0.001); // 5000 × 2500 mm
    }

    public function test_schmetterling_umlauf_wird_abgelehnt(): void
    {
        $szene = [
            'levels' => [['id' => 'level-eg', 'sortOrder' => 0, 'defaultWallHeight' => 2500]],
            'nodes' => [
                $this->wall('w1', 0, 0, 5000, 5000),
                $this->wall('w2', 5000, 5000, 5000, 0),
                $this->wall('w3', 5000, 0, 0, 5000),
                $this->wall('w4', 0, 5000, 0, 0),
            ],
        ];

        $this->expectException(GeometrieUngueltigException::class);
        $this->service()->projiziere($szene);
    }
}
