<?php

namespace Tests\Unit\Models;

use App\Exceptions\RoofAzimuthOutOfRangeException;
use App\Models\PVRoof;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
    /**
     * Nur fuer die zwei Schreibpfad-Zusagen unten. Sie schreiben wirklich — und rollen zurueck.
     * Die acht Vertragszusagen darueber brauchen die Datenbank nicht und bleiben unberuehrt.
     */
    use DatabaseTransactions;

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

    /**
     * A-13 · P2-NACHFORDERUNG des Release-Prüfers, wörtlich: *„die Nachforderung an den Generator
     * (eine Zusage, die SPEICHERT) bleibt offen und erlischt NICHT mit der Veröffentlichung: ohne
     * sie kann der Hook bei einem späteren Umbau still verschwinden."*
     *
     * Die acht Zusagen oben rufen `pruefeAzimut` DIREKT auf. Sie sind richtig — aber sie überleben
     * alle, wenn jemand `booted()` samt `saving`-Wächter entfernt. **Diese Zusage stirbt dann**,
     * und genau das ist ihr Zweck: sie geht über `save()`, nicht über die Vertragsfunktion.
     */
    public function test_a13_p2_waechter_greift_auf_dem_schreibpfad(): void
    {
        $roof = new PVRoof();
        $roof->roof_azimuth = 400;

        $this->expectException(RoofAzimuthOutOfRangeException::class);
        $roof->save();
    }

    /**
     * A-13 · die Gegenprobe zur vorigen Zusage — ohne sie bewiese ein Wächter, der ALLES abweist,
     * dasselbe. Ein gültiger Azimut kommt am Wächter VORBEI und läuft weiter bis zur Datenbank.
     *
     * Dort scheitert er an den drei Fremdschlüsseln (`customer_id`, `alternative_id`,
     * `roof_covering`) — und das ist hier kein Mangel, sondern **der Beleg**: die Ausnahme wechselt
     * von `RoofAzimuthOutOfRangeException` zu `QueryException`. *Der Wächter hat den Wert
     * durchgelassen.* Eine vollständige Bestandskette aufzubauen wäre ein zweiter Auftrag und
     * würde nichts belegen, was diese Unterscheidung nicht schon zeigt.
     */
    public function test_a13_p2_gueltiger_wert_kommt_am_waechter_vorbei(): void
    {
        $roof = new PVRoof();
        $roof->roof_azimuth = 180;

        try {
            $roof->save();
            $this->assertTrue(true, 'Gespeichert — der Waechter hat 180 durchgelassen.');
        } catch (RoofAzimuthOutOfRangeException $e) {
            $this->fail('Der Waechter hat einen GUELTIGEN Azimut abgewiesen: '.$e->getMessage());
        } catch (QueryException $e) {
            $this->assertTrue(true, 'Der Waechter liess 180 durch; gescheitert ist erst die '
                .'Datenbank an den Fremdschluesseln — nicht der Vertrag.');
        }
    }
}
