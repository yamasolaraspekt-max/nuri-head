<?php

namespace App\Support;

/**
 * G0b / AP-4b — typisierter Zugriff auf den Toleranzvertrag (config/geometrie.php).
 *
 * EINZIGE Zugriffsstelle für Geometrie-Toleranzen. Keine Komponente definiert eigene
 * „ungefähr gleich"-Werte; alles kommt aus der zentralen Config.
 */
final class GeometrieToleranz
{
    public static function punktgleichheitMm(): float
    {
        return (float) config('geometrie.punktgleichheit_mm', 1);
    }

    public static function minSegmentMm(): float
    {
        return (float) config('geometrie.min_segment_mm', 10);
    }

    public static function minPolygonFlaecheMm2(): float
    {
        return (float) config('geometrie.min_polygon_flaeche_mm2', 10000);
    }

    public static function anzeigeRundungM2(): int
    {
        return (int) config('geometrie.anzeige_rundung_m2', 2);
    }

    public static function recalcAbweichungRel(): float
    {
        return (float) config('geometrie.recalc_abweichung_rel', 1e-9);
    }
}
