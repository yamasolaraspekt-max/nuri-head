<?php

namespace App\Services\Import;

/**
 * AUF-88-P1 / K-01 — die Magic-Byte-Erkennung, an EINER Stelle statt zweimal.
 *
 * **Der gemessene Mangel:** `PlanUploadController::store()` prüfte nur die Dateiendung; die
 * Magic-Byte-Prüfung lief erst in `PlanKlassifizieren`, NACHDEM die Datei bereits gespeichert war
 * (`PlanKlassifizieren::magicHinweis()`, dieselbe Logik, dupliziert). §3 des Master-Prompts
 * verbietet ausdrücklich, die Erkennung allein auf die Dateiendung zu stützen.
 *
 * **Diese Klasse ist die eine Wahrheit für beide Stellen** — der Controller ruft sie VOR
 * `store()`, der Job weiterhin danach (für die Meta-Anzeige). Reine Funktion, kein Storage-Zugriff:
 * sie bekommt Bytes, keinen Pfad — sonst bräuchte eine Vorab-Prüfung immer erst einen Schreibzugriff.
 */
class DateiSignatur
{
    /**
     * Erkennt den Dateityp an den ersten Bytes. Liefert `null`, wenn kein bekanntes Muster passt —
     * das ist eine Aussage („nicht erkannt"), kein Fehler.
     */
    public static function erkenne(string $kopf): ?string
    {
        return match (true) {
            str_starts_with($kopf, '%PDF') => 'pdf',
            str_starts_with($kopf, "\x89PNG") => 'png',
            str_starts_with($kopf, "\xFF\xD8\xFF") => 'jpg',
            str_starts_with($kopf, 'II*') || str_starts_with($kopf, "MM\x00*") => 'tiff',
            str_starts_with($kopf, 'AC10') => 'dwg',
            default => null,
        };
    }

    /**
     * Welche erkannten Signaturen zu einer behaupteten Endung passen. **Absichtlich großzügig
     * innerhalb einer Familie** (`jpg`/`jpeg` sind eine Signatur, `tif`/`tiff` sind eine Signatur) —
     * das ist keine zweite Wahrheit über erlaubte Endungen (die bleibt
     * `PlanUploadController::ENDUNGEN`), nur die Zuordnung Signatur↔Endung.
     *
     * **DWG hat keine feste Magic-Signatur, DXF ist Text ohne verlässliches Muster** — beide
     * werden hier NICHT geprüft (liefert `true`, keine Ablehnung): eine Prüfung, die sie ablehnen
     * würde, obwohl sie nichts Verlässliches prüft, wäre falsche Sicherheit.
     */
    public static function passtZuEndung(string $kopf, string $endung): bool
    {
        $endung = strtolower($endung);
        if (in_array($endung, ['dwg', 'dxf'], true)) {
            return true;
        }
        $erkannt = self::erkenne($kopf);
        if ($erkannt === null) {
            return false;
        }

        return match ($endung) {
            'pdf' => $erkannt === 'pdf',
            'png' => $erkannt === 'png',
            'jpg', 'jpeg' => $erkannt === 'jpg',
            'tif', 'tiff' => $erkannt === 'tiff',
            default => false,
        };
    }
}
