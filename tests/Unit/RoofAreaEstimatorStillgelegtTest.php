<?php

namespace Tests\Unit;

use App\Services\RoofAreaEstimator;
use LogicException;
use Tests\TestCase;

/**
 * AP-4 Gate d — Wächter der Stilllegung: der RoofAreaEstimator (Web-Mercator-Flächenfehler,
 * misst Grundriss statt geneigter Dachfläche) darf NIE wieder still ein Maß liefern.
 * Wird dieser Test rot, hat jemand die Stilllegung rückgängig gemacht — das ist nur mit
 * bewusstem Entscheid erlaubt (kanonische Quelle: anforderungsprofile.gebaeude_geometrie).
 */
class RoofAreaEstimatorStillgelegtTest extends TestCase
{
    public function test_estimate_wirft_immer(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/stillgelegt/i');

        // Selbst der frühere „sichere" DB-Pfad (polygon_area vorhanden) liefert kein Maß mehr.
        (new RoofAreaEstimator)->estimate(50.1, 8.6, 'Frankfurt', ['polygon_area' => 120.0]);
    }

    public function test_estimate_wirft_auch_ohne_daten(): void
    {
        $this->expectException(LogicException::class);

        (new RoofAreaEstimator)->estimate(null, null, null, []);
    }
}
