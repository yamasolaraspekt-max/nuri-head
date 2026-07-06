<?php

namespace App\Services\Form;

/**
 * Plausibilitätsprüfung für die Formular-Berechnungs-Engine (FS-03).
 *
 * Faithful-Port aus playground (backend-laravel app/Services/Form/PlausibilityService.php),
 * ENTKOPPELT von den Eloquent-Models: statt eines FormField-Objekts nimmt der
 * Dienst eine reine Feld-Konfiguration (Array) entgegen. KEIN DB-Zugriff, kein
 * eval(), keine Persistenz.
 *
 * Liefert AUSSCHLIESSLICH Warnungen — niemals eine Wertänderung und niemals
 * eine Blockade. Der Aufrufer hängt die Befunde nur informativ an, damit
 * Sachbearbeitung und Prüfung sie sehen.
 *
 * Quellen der Regeln:
 *  - Allgemeine Heuristiken auf Operanden (negative Fläche/Länge, Stückzahl 0,
 *    Einheiten-Mischung über Dimensionen).
 *  - Optionale feldspezifische Regeln aus der Feld-Konfiguration unter dem
 *    Schlüssel `plausibilitaet` (Liste von Regeln {feld?, min?, max?, text?,
 *    schwere?}). Greift nur, wenn der Konfigurierende das hinterlegt hat.
 *
 * Bewusst KEINE verbindliche Fachberechnung (Heizlast/PV/WP) — nur generische,
 * fachneutrale Auffälligkeiten (z. B. Raumhöhe < 1,5 / > 4 als Standardregel,
 * wenn ein Feld als Raumhöhe gekennzeichnet ist).
 */
class PlausibilityService
{
    public function __construct(private readonly UnitConversionService $units)
    {
    }

    /**
     * Prüft eine Berechnung gegen Plausibilitätsregeln.
     *
     * @param array<string, mixed>            $field    Feld-Konfiguration. Genutzte Schlüssel:
     *        `slug` (?string), `label` (?string), `plausibilitaet` (array<int, array>)
     *        — alternativ `plausibility`.
     * @param array<int, array<string,mixed>> $operanden Operanden-Snapshots
     *        je Eintrag: {slug, value (float|null), unit (?string), is_assumption}
     * @param float|null                      $ergebnis  berechnetes Ergebnis (oder null)
     *
     * @return array<int, array{regel: string, schwere: string, text: string}>
     */
    public function pruefe(array $field, array $operanden, ?float $ergebnis): array
    {
        $befunde = [];

        // 1) Generische Operanden-Heuristiken.
        $dimensionen = [];
        foreach ($operanden as $operand) {
            $slug = (string) ($operand['slug'] ?? '');
            $wert = $operand['value'] ?? null;
            $unit = $operand['unit'] ?? null;

            if ($wert === null) {
                continue;
            }
            $wert = (float) $wert;

            $dim = $this->units->dimension($unit);
            if ($dim !== null) {
                $dimensionen[$dim] = true;
            }

            // Negative Maße (Fläche/Länge/Volumen) sind physikalisch unplausibel.
            if ($wert < 0 && in_array($dim, ['flaeche', 'laenge', 'volumen'], true)) {
                $befunde[] = $this->befund(
                    'negatives_mass',
                    'hoch',
                    "Feld {$slug}: negativer Wert ({$wert}) für ein Maß ist unplausibel."
                );
            }

            // Stückzahl 0 (Modulanzahl=0 o. Ä.).
            if ($wert == 0.0 && $dim === 'stueck') {
                $befunde[] = $this->befund(
                    'stueckzahl_null',
                    'mittel',
                    "Feld {$slug}: Stückzahl 0 — vermutlich noch nicht erfasst."
                );
            }

            // Raumhöhe-Heuristik (fachneutral, an Slug/Label erkannt).
            if ($this->istRaumhoehe($slug, $field) && $dim === 'laenge') {
                $hoeheM = $this->units->convert($wert, $unit, 'm');
                if ($hoeheM !== null && ($hoeheM < 1.5 || $hoeheM > 4.0)) {
                    $befunde[] = $this->befund(
                        'raumhoehe_aussergewoehnlich',
                        'mittel',
                        "Feld {$slug}: Raumhöhe {$hoeheM} m liegt außerhalb des üblichen Bereichs (1,5–4 m)."
                    );
                }
            }
        }

        // Einheiten-Mischung über mehrere Dimensionen (z. B. m + kg).
        if (count($dimensionen) > 1) {
            $liste = implode(', ', array_keys($dimensionen));
            $befunde[] = $this->befund(
                'einheiten_mischung',
                'mittel',
                "Es werden Einheiten verschiedener Dimensionen gemischt ({$liste}) — bitte prüfen."
            );
        }

        // 2) Feldspezifische Regeln aus der Feld-Konfiguration.
        foreach ($this->regelnAusFeld($field) as $regel) {
            $befund = $this->werteFeldregelAus($regel, $operanden, $ergebnis);
            if ($befund !== null) {
                $befunde[] = $befund;
            }
        }

        return array_values($befunde);
    }

    /**
     * Extrahiert die optionalen Plausibilitätsregeln aus der Feld-Konfiguration.
     *
     * @param array<string, mixed> $field
     *
     * @return array<int, array<string, mixed>>
     */
    private function regelnAusFeld(array $field): array
    {
        $regeln = $field['plausibilitaet'] ?? $field['plausibility'] ?? [];

        return is_array($regeln) ? array_values(array_filter($regeln, 'is_array')) : [];
    }

    /**
     * Wertet eine einzelne feldspezifische Regel aus (min/max gegen den Wert
     * eines benannten Operanden oder gegen das Ergebnis).
     *
     * @param array<string, mixed>            $regel
     * @param array<int, array<string,mixed>> $operanden
     *
     * @return array{regel: string, schwere: string, text: string}|null
     */
    private function werteFeldregelAus(array $regel, array $operanden, ?float $ergebnis): ?array
    {
        $zielSlug = isset($regel['feld']) ? (string) $regel['feld'] : null;
        $wert = null;

        if ($zielSlug === null || $zielSlug === '') {
            $wert = $ergebnis;
        } else {
            foreach ($operanden as $operand) {
                if ((string) ($operand['slug'] ?? '') === $zielSlug && ($operand['value'] ?? null) !== null) {
                    $wert = (float) $operand['value'];
                    break;
                }
            }
        }

        if ($wert === null) {
            return null;
        }

        $schwere = isset($regel['schwere']) ? (string) $regel['schwere'] : 'mittel';
        $bezeichner = $zielSlug ?? 'ergebnis';

        if (array_key_exists('min', $regel) && is_numeric($regel['min']) && $wert < (float) $regel['min']) {
            return $this->befund(
                'regel_min_unterschritten',
                $schwere,
                (string) ($regel['text'] ?? "Wert von {$bezeichner} ({$wert}) unterschreitet das Minimum {$regel['min']}.")
            );
        }

        if (array_key_exists('max', $regel) && is_numeric($regel['max']) && $wert > (float) $regel['max']) {
            return $this->befund(
                'regel_max_ueberschritten',
                $schwere,
                (string) ($regel['text'] ?? "Wert von {$bezeichner} ({$wert}) überschreitet das Maximum {$regel['max']}.")
            );
        }

        return null;
    }

    /**
     * @param array<string, mixed> $field
     */
    private function istRaumhoehe(string $slug, array $field): bool
    {
        $label = (string) ($field['label'] ?? '');
        $heu = mb_strtolower($slug . ' ' . $label);

        return str_contains($heu, 'raumhoehe')
            || str_contains($heu, 'raumhöhe')
            || str_contains($heu, 'deckenhoehe')
            || str_contains($heu, 'deckenhöhe');
    }

    /**
     * @return array{regel: string, schwere: string, text: string}
     */
    private function befund(string $regel, string $schwere, string $text): array
    {
        return ['regel' => $regel, 'schwere' => $schwere, 'text' => $text];
    }
}
