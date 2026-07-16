<?php

namespace Tests\Unit\BuildingModel;

use App\Services\BuildingModel\CanonicalBuildingModelValidator;
use App\Services\BuildingModel\CanonicalModelValidationResult;
use Tests\TestCase;

/**
 * G1b-1 / A-3 — Öffnungshöhen-Vertrag im semantischen Validator. Bootet Laravel (zentraler Toleranzvertrag).
 * Prüft Sturzkonsistenz (`lintel == sill + rough_height`), vertikale Wandgrenzen, Fertig ≤ Rohbau und die
 * ausdrückliche Nicht-Entscheidbarkeit bei `height_mode=profile` (Warncode statt stiller uniformer Annahme).
 */
class OpeningHeightContractTest extends TestCase
{
    /** @return array<string,mixed> */
    private function basisModell(): array
    {
        // Fixture 04 trägt eine interne Polylinienwand + Öffnung (Tür) auf Segment 1.
        $path = __DIR__.'/../../Fixtures/building-model/valid/04-polyline-wall-opening-segment1.json';

        return json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    private function wandIndex(array $model): int
    {
        $storey = $model['buildings'][0]['storeys'][0];
        $wallId = $storey['openings'][0]['wall_id'];
        foreach ($storey['walls'] as $i => $w) {
            if ($w['id'] === $wallId) {
                return $i;
            }
        }
        $this->fail('Wirtswand der Öffnung nicht gefunden.');
    }

    /**
     * @param  array<string,mixed>  $opening  Overrides für die Öffnung
     * @param  array<string,mixed>  $wall     Overrides für die Wirtswand
     */
    private function validate(array $opening = [], array $wall = []): CanonicalModelValidationResult
    {
        $model = $this->basisModell();
        $wi = $this->wandIndex($model);
        $model['buildings'][0]['storeys'][0]['openings'][0] = array_merge(
            $model['buildings'][0]['storeys'][0]['openings'][0],
            $opening
        );
        $model['buildings'][0]['storeys'][0]['walls'][$wi] = array_merge(
            $model['buildings'][0]['storeys'][0]['walls'][$wi],
            $wall
        );

        return (new CanonicalBuildingModelValidator)->validate($model);
    }

    public function test_gueltige_bruestung_und_rohbauhoehe_ist_valide(): void
    {
        $res = $this->validate(['sill_height_mm' => 900, 'rough_height_mm' => 1350, 'lintel_height_mm' => 2250]);
        $this->assertTrue($res->isValid(), 'Fehler: '.json_encode($res->errorCodes()));
    }

    public function test_lintel_stimmt_mit_rohbauoberkante_ueberein(): void
    {
        $res = $this->validate(['sill_height_mm' => 0, 'rough_height_mm' => 2000, 'lintel_height_mm' => 2000]);
        $this->assertFalse($res->hasErrorCode('opening.lintel_conflict'));
    }

    public function test_sturzkonflikt_wird_erkannt(): void
    {
        $res = $this->validate(['sill_height_mm' => 900, 'rough_height_mm' => 1350, 'lintel_height_mm' => 2300]);
        $this->assertTrue($res->hasErrorCode('opening.lintel_conflict'));
    }

    public function test_oeffnung_ueber_wandoberkante_wird_abgelehnt(): void
    {
        $res = $this->validate(
            ['sill_height_mm' => 1500, 'rough_height_mm' => 2000, 'lintel_height_mm' => null],
            ['height_mode' => 'uniform', 'height_mm' => 2500]
        );
        $this->assertTrue($res->hasErrorCode('opening.exceeds_wall_height'));
    }

    public function test_fertigmass_groesser_als_rohbaumass_wird_abgelehnt(): void
    {
        $res = $this->validate(['rough_height_mm' => 2000, 'finished_height_mm' => 2100]);
        $this->assertTrue($res->hasErrorCode('opening.finished_exceeds_rough'));
    }

    public function test_profilwand_wird_nicht_still_uniform_akzeptiert(): void
    {
        $res = $this->validate(
            ['sill_height_mm' => 1500, 'rough_height_mm' => 2000, 'lintel_height_mm' => null],
            ['height_mode' => 'profile', 'height_mm' => 2500]
        );
        // Keine uniforme Ablehnung, aber ausdrücklicher Warncode (nicht entscheidbar).
        $this->assertFalse($res->hasErrorCode('opening.exceeds_wall_height'));
        $this->assertContains('opening.profile_height_unresolved', array_column($res->issues(), 'code'));
    }
}
