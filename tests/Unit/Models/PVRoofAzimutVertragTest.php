<?php

namespace Tests\Unit\Models;

use App\Exceptions\RoofAzimuthOutOfRangeException;
use App\Models\PVRoof;
use Tests\TestCase;

/**
 * A-13 — Zusage fuer `pv_roofs.roof_azimuth`.
 *
 * Gleiche Form wie `test_azimut_vertrag_0_bis_360()` in
 * `tests/Unit/BuildingModel/BuildingModelSchemaContractTest.php`, damit beide Zusagen mit
 * EINEM `grep` auffindbar sind. Beide sichern dieselbe Grenze: `0 <= x < 360`.
 *
 * Ohne Datenbank: geprueft wird die oeffentliche Vertragsfunktion, die der `saving`-Waechter
 * selbst aufruft. Der Nachweis, dass der Waechter auch auf dem Mass-Assignment-Pfad greift,
 * ist eine Wegwerf-Probe gegen `ticket_testing` und steht im §11-Bericht.
 */
class PVRoofAzimutVertragTest extends TestCase
{
    public function test_azimut_vertrag_0_bis_360(): void
    {
        $this->assertSame(0, PVRoof::AZIMUT_MIN);
        $this->assertSame(360, PVRoof::AZIMUT_MAX_EXKLUSIV);
    }

    public function test_azimut_vertrag_0_ist_gueltig(): void
    {
        PVRoof::pruefeAzimut(0);
        $this->assertTrue(true, '0 ist gueltig — Nord.');
    }

    public function test_azimut_vertrag_359_ist_gueltig(): void
    {
        PVRoof::pruefeAzimut(359);
        $this->assertTrue(true, '359 ist der letzte gueltige Wert.');
    }

    public function test_azimut_vertrag_360_wird_abgewiesen(): void
    {
        $this->expectException(RoofAzimuthOutOfRangeException::class);
        PVRoof::pruefeAzimut(360);
    }

    public function test_azimut_vertrag_minus_1_wird_abgewiesen(): void
    {
        $this->expectException(RoofAzimuthOutOfRangeException::class);
        PVRoof::pruefeAzimut(-1);
    }

    public function test_azimut_vertrag_361_wird_abgewiesen(): void
    {
        $this->expectException(RoofAzimuthOutOfRangeException::class);
        PVRoof::pruefeAzimut(361);
    }

    public function test_azimut_vertrag_null_ist_gueltig(): void
    {
        PVRoof::pruefeAzimut(null);
        $this->assertTrue(true, 'Das Feld ist nullable — keine Angabe ist keine falsche Angabe.');
    }

    public function test_azimut_vertrag_text_wird_abgewiesen(): void
    {
        $this->expectException(RoofAzimuthOutOfRangeException::class);
        PVRoof::pruefeAzimut('Sued');
    }
}
