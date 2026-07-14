<?php

namespace Tests\Feature\Geometrie;

use App\Services\Geometrie\TopologieGate;
use Tests\TestCase;

/**
 * G0b / AP-4b — Bestandsdaten-Scan (E3, reproduzierbar).
 *
 * Lokale Dev-DB enthält 0 raum_geometrien-Zeilen (Read-only-Scan 2026-07-14) → E3-Fallback:
 * synthetische, repräsentative Fixture (Rechteck/L/T/U/Walm). Dieser Test beweist, dass das Gate
 * realistische, gültige Grundriss-Geometrien NICHT fälschlich ablehnt (0 Blocker). Der echte
 * Bestands-Scan gegen Hetzner-Daten ist ein Deploy-Tag-Checklistenpunkt (kein Testgegenstand).
 */
class BestandScanTest extends TestCase
{
    public function test_synthetische_bestandsfixture_wird_nicht_abgelehnt(): void
    {
        $pfad = base_path('tests/Fixtures/geometrie-bestand-2026-07-14.json');
        $this->assertFileExists($pfad);

        $daten = json_decode(file_get_contents($pfad), true);
        $this->assertSame('synthetisch', $daten['herkunft']); // Herkunft ehrlich deklariert
        $this->assertNotEmpty($daten['geometrien']);

        $gate = new TopologieGate;
        $blocked = [];
        foreach ($daten['geometrien'] as $g) {
            $erg = $gate->pruefePolygon($g['polygon']);
            if (! $erg->valid) {
                $blocked[] = $g['id'].':'.implode(',', $erg->ruleKeys());
            }
        }

        // Scan = 0 Treffer → repräsentative reale Formen passieren das Gate.
        $this->assertSame([], $blocked, 'Gültige Bestandsgeometrie würde vom Gate blockiert: '.implode(' | ', $blocked));
    }
}
