<?php

namespace Tests\Unit\Heizlast;

use App\Models\HeizlastRaum;
use App\Models\RaumGeometrie;
use App\Services\Heizlast\GeometrieAbleitungService;
use Tests\TestCase;

/**
 * U-a (Operanden-Gate) — opakeUQuelle markiert unbelegte Standard-C-Bauteile mit u_wert_datenlage='fehlt'.
 * Additiv/UNVERDRAHTET: die HeizlastRechner-Formel und der belegte Pfad bleiben byte-genau; der Adapter-
 * Zähler (der 'fehlt' auswertet) ist U-b. Grundlage: docs/planner-spec-heizlast-operanden-gate-uwert.md.
 * Kein DB-Zugriff (nur Model-Konstruktion + reine Ableitung).
 */
class GeometrieAbleitungUWertDatenlageTest extends TestCase
{
    /** @param array<string,mixed> $segment */
    private function geo(array $segment): RaumGeometrie
    {
        $geo = new RaumGeometrie([
            'polygon' => [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 5000, 'y' => 5000], ['x' => 0, 'y' => 5000]],
            'hoehe_mm' => 2500,
            'wand_segmente' => [$segment],
            'decke' => null,
            'boden' => null,
        ]);
        $geo->setRelation('heizlastRaum', new HeizlastRaum(['name' => 'Raum', 'nutzung' => 'wohnen']));

        return $geo;
    }

    /** @param array<string,mixed> $extra @return array<string,mixed> */
    private function wand(array $extra = []): array
    {
        return array_merge([
            'von' => ['x' => 0, 'y' => 0], 'bis' => ['x' => 5000, 'y' => 0],
            'bauteil_typ' => 'wand', 'grenzflaeche' => 'aussen', 'azimut_grad' => 180, 'oeffnungen' => [],
        ], $extra);
    }

    public function test_unbelegte_wand_wird_als_fehlt_markiert_ohne_erfundenen_u_wert(): void
    {
        $bauteil = (new GeometrieAbleitungService())->ausGeometrie($this->geo($this->wand()))['bauteile'][0];

        $this->assertSame('C', $bauteil['u_strategie']);
        $this->assertSame('fehlt', $bauteil['u_wert_datenlage']);
        $this->assertArrayNotHasKey('u_wert', $bauteil); // kein stiller Ersatzwert
        $this->assertGreaterThan(0, $bauteil['flaeche_m2']);
    }

    public function test_belegte_wand_bleibt_A_ohne_fehlt_marker(): void
    {
        $bauteil = (new GeometrieAbleitungService())->ausGeometrie($this->geo($this->wand(['u_wert' => 0.24])))['bauteile'][0];

        $this->assertSame('A', $bauteil['u_strategie']);
        $this->assertEqualsWithDelta(0.24, $bauteil['u_wert'], 1e-9);
        $this->assertArrayNotHasKey('u_wert_datenlage', $bauteil); // belegter Pfad unberührt
    }
}
