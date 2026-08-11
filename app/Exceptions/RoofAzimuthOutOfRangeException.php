<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A-13 — wird geworfen, wenn `pv_roofs.roof_azimuth` außerhalb des Vertrags gespeichert werden soll.
 *
 * Der Vertrag ist die Kompass-Konvention des Hauses: **0=N, 90=E, 180=S, 270=W**, gültig `0 ≤ x < 360`.
 * `360` ist derselbe Punkt wie `0` — wer beide zulässt, hat zwei Zahlen für eine Richtung.
 *
 * Der Wächter sitzt am Model und nicht im Controller, damit er auch auf dem Mass-Assignment-Pfad
 * (`PVRoof::create($array)`) greift. Keine stille Korrektur: ein Wert außerhalb des Bereichs wird
 * **abgewiesen**, nicht zurechtgebogen.
 */
final class RoofAzimuthOutOfRangeException extends RuntimeException
{
    public function __construct(public readonly mixed $wert)
    {
        parent::__construct(
            'roof_azimuth ausserhalb des Vertrags: '.var_export($wert, true)
            .' — gueltig ist 0 <= x < 360 (Kompass: 0=N, 90=E, 180=S, 270=W).'
        );
    }
}
