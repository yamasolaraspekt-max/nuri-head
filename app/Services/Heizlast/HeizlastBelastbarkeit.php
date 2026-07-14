<?php

namespace App\Services\Heizlast;

/**
 * AP-1 / Paket 5b — Heizlast-Belastbarkeits-Klassifizierer (READ-ONLY, REIN).
 *
 * Leitet aus den VORHANDENEN Feldern eines Heizlast-Werts (`datenlage`, `quelle`, `erfassungsweg`)
 * die fachliche Belastbarkeit ab — OHNE Persistenz, OHNE neues Feld, OHNE zweite Heizlast-Wahrheit.
 * Reine Funktion; erzeugt nur eine Einstufung + Reifegrad + Verbindlichkeits-Flag.
 *
 * Regelmodell (Slice 1, Heuristik akzeptiert; belegt an AnforderungsprofilHeizlastAdapter):
 *  - BELASTBAR      : raumweise DIN-Heizlast — datenlage='berechnet' aus dem HeizlastRechner,
 *                     ohne geschätzte/ungeprüfte Eingaben.                      → Reifegrad 1.0, verbindlich.
 *  - EINGESCHRAENKT : Verbrauchsauswertung (datenlage='gemessen') ODER berechnet mit
 *                     geschätzten/ungeprüften Eingaben ODER berechnet aus nicht-Rechner-Quelle
 *                     (Typologie/Bauteil mittlerer Qualität).                    → Reifegrad 0.6, unverbindlich.
 *  - VORLAEUFIG     : Schätzung / unvollständig (datenlage='geschaetzt').        → Reifegrad 0.3, unverbindlich.
 *  - UNZUREICHEND   : kein Wert oder nicht plausibel (wert_num<=0).              → Reifegrad 0.0, unverbindlich.
 *
 * WICHTIG: „fachlich freigegeben" hat heute KEINEN Marker (Freigabe-Workflow = Kapitel 13) und ist
 * daher NICHT als eigener Belastbar-Pfad abgebildet — bewusst spätere Abhängigkeit.
 */
final class HeizlastBelastbarkeit
{
    public const BELASTBAR = 'belastbar';
    public const EINGESCHRAENKT = 'eingeschraenkt';
    public const VORLAEUFIG = 'vorlaeufig';
    public const UNZUREICHEND = 'unzureichend';

    private const REIFEGRAD = [
        self::BELASTBAR => 1.0,
        self::EINGESCHRAENKT => 0.6,
        self::VORLAEUFIG => 0.3,
        self::UNZUREICHEND => 0.0,
    ];

    private const LABEL = [
        self::BELASTBAR => 'belastbar',
        self::EINGESCHRAENKT => 'eingeschränkt',
        self::VORLAEUFIG => 'vorläufig',
        self::UNZUREICHEND => 'unzureichend',
    ];

    /**
     * @param  ?string  $datenlage  'gemessen' | 'berechnet' | 'geschaetzt' | null (=kein Wert)
     * @param  ?string  $quelle  z. B. 'HeizlastRechner' | 'klima_plz 01067' | 'Kundenangabe'
     * @param  ?string  $erfassungsweg  'manuell' | 'import' | 'berechnet' | 'default'
     * @param  ?float  $wertNum  numerischer Heizlast-Wert (Plausibilität); null = kein Wert
     * @param  bool  $eingabenUnsicher  true, wenn die Rechnung geschätzte/ungeprüfte Eingaben enthielt
     *                                   (z. B. Anforderungsprofil-`ergebnis_hinweis` gesetzt)
     * @return array{stufe:string,label:string,reifegrad:float,verbindlich:bool,hinweis:?string}
     */
    public static function beurteile(
        ?string $datenlage,
        ?string $quelle = null,
        ?string $erfassungsweg = null,
        ?float $wertNum = null,
        bool $eingabenUnsicher = false,
    ): array {
        // Kein Wert oder nicht plausibel → unzureichend.
        if ($datenlage === null || ($wertNum !== null && $wertNum <= 0)) {
            return self::ergebnis(self::UNZUREICHEND, 'Keine belastbare Heizlast hinterlegt.');
        }

        $stufe = match ($datenlage) {
            'geschaetzt' => self::VORLAEUFIG,
            'gemessen' => self::EINGESCHRAENKT,          // Verbrauchsauswertung
            'berechnet' => self::istRaumweiseRechner($quelle)
                ? self::BELASTBAR
                : self::EINGESCHRAENKT,                    // Typologie/Bauteil/andere Quelle
            default => self::VORLAEUFIG,                   // unbekannte Datenlage → konservativ
        };

        // Raumweise DIN-Rechnung auf geschätzten/ungeprüften Eingaben ist NICHT voll belastbar.
        if ($stufe === self::BELASTBAR && $eingabenUnsicher) {
            $stufe = self::EINGESCHRAENKT;
        }

        return self::ergebnis($stufe, self::standardHinweis($stufe, $eingabenUnsicher));
    }

    /** Convenience: wenn gar kein Heizlast-Wert existiert. */
    public static function fehlend(): array
    {
        return self::ergebnis(self::UNZUREICHEND, 'Keine belastbare Heizlast hinterlegt.');
    }

    public static function reifegradFuer(string $stufe): float
    {
        return self::REIFEGRAD[$stufe] ?? 0.0;
    }

    private static function istRaumweiseRechner(?string $quelle): bool
    {
        if ($quelle === null || $quelle === '') {
            return false;
        }

        return str_contains($quelle, 'HeizlastRechner');
    }

    private static function standardHinweis(string $stufe, bool $eingabenUnsicher): ?string
    {
        return match ($stufe) {
            self::BELASTBAR => null,
            self::EINGESCHRAENKT => $eingabenUnsicher
                ? 'Heizlast berechnet, aber mit geschätzten/ungeprüften Eingaben — nur eingeschränkt belastbar.'
                : 'Heizlast aus Verbrauch/Typologie — eingeschränkt belastbar, kein verbindliches Ranking.',
            self::VORLAEUFIG => 'Heizlast nur geschätzt/vorläufig — unverbindlich, keine technische Freigabe.',
            default => 'Keine belastbare Heizlast hinterlegt.',
        };
    }

    /** @return array{stufe:string,label:string,reifegrad:float,verbindlich:bool,hinweis:?string} */
    private static function ergebnis(string $stufe, ?string $hinweis): array
    {
        return [
            'stufe' => $stufe,
            'label' => self::LABEL[$stufe],
            'reifegrad' => self::REIFEGRAD[$stufe],
            'verbindlich' => $stufe === self::BELASTBAR,
            'hinweis' => $hinweis,
        ];
    }
}
