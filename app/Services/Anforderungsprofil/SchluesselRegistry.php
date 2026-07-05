<?php

namespace App\Services\Anforderungsprofil;

use InvalidArgumentException;

/**
 * B2a-2 — Schlüssel-Registry: der VERTRAG für Anforderungsprofil-Werte.
 *
 * Definierte Liste erlaubter `schluessel` (Key => erwartete Einheit + Cast + ob wert_num Pflicht).
 * Gegen diese Registry wird beim Schreiben validiert (unbekannter Key = Fehler statt stiller Zeile);
 * der B2a-3-Adapter LIEST sie, damit kein Konsument Keys rät. Zähmt den EAV-Preis (Tippfehler-Keys,
 * keine Typ-Sicherheit) der anforderungsprofil_werte-Tabelle.
 *
 * Additiv erweiterbar (Auslegungs-Arten wachsen); Änderungen sind eine bewusste Vertrags-Änderung.
 */
final class SchluesselRegistry
{
    /** @var array<string, array{einheit: ?string, cast: string, wert_num_pflicht: bool}> */
    private const SCHLUESSEL = [
        // --- Ergebnis-Bedarf (i. d. R. berechnet) ---
        'phi_hl_kw'         => ['einheit' => 'kW',    'cast' => 'float',  'wert_num_pflicht' => true],
        'phi_ww_kw'         => ['einheit' => 'kW',    'cast' => 'float',  'wert_num_pflicht' => true],
        'vorlauf_c'         => ['einheit' => '°C',    'cast' => 'float',  'wert_num_pflicht' => true],
        'ruecklauf_c'       => ['einheit' => '°C',    'cast' => 'float',  'wert_num_pflicht' => false],
        'spreizung_k'       => ['einheit' => 'K',     'cast' => 'float',  'wert_num_pflicht' => false],
        // --- Rahmenbedingungen / Standort ---
        'standort_plz'      => ['einheit' => null,    'cast' => 'string', 'wert_num_pflicht' => false],
        'norm_aussentemp_c' => ['einheit' => '°C',    'cast' => 'float',  'wert_num_pflicht' => true],
        'gelaendehoehe_m'   => ['einheit' => 'm',     'cast' => 'float',  'wert_num_pflicht' => false],
        // --- Gebäude-Kontext ---
        'baujahr'           => ['einheit' => null,    'cast' => 'int',    'wert_num_pflicht' => false],
        'sanierungsstufe'   => ['einheit' => null,    'cast' => 'string', 'wert_num_pflicht' => false],
        'komfortzuschlag_k' => ['einheit' => 'K',     'cast' => 'float',  'wert_num_pflicht' => false],
        'intermittierend'   => ['einheit' => null,    'cast' => 'bool',   'wert_num_pflicht' => false],
        // --- Betrieb ---
        'sperrzeit_h'       => ['einheit' => 'h',     'cast' => 'float',  'wert_num_pflicht' => false],
    ];

    /** Erlaubte Datenlage-Stufen (die Kern-Ehrlichkeit je Wert). */
    public const DATENLAGE = ['gemessen', 'berechnet', 'geschaetzt'];

    public static function istErlaubt(string $schluessel): bool
    {
        return array_key_exists($schluessel, self::SCHLUESSEL);
    }

    /** @return array{einheit: ?string, cast: string, wert_num_pflicht: bool} */
    public static function definition(string $schluessel): array
    {
        if (! self::istErlaubt($schluessel)) {
            throw new InvalidArgumentException("Unbekannter Anforderungsprofil-Schlüssel: {$schluessel}");
        }

        return self::SCHLUESSEL[$schluessel];
    }

    public static function datenlageErlaubt(string $datenlage): bool
    {
        return in_array($datenlage, self::DATENLAGE, true);
    }

    /** @return list<string> */
    public static function alle(): array
    {
        return array_keys(self::SCHLUESSEL);
    }
}
