<?php

namespace App\Services\Form;

/**
 * Einheiten-Umrechnung für die Formular-Berechnungs-Engine (FS-03).
 *
 * Faithful-Port aus playground (backend-laravel app/Services/Form/UnitConversionService.php).
 * Bereits framework-neutral: reine Hilfsklasse, KEIN Datenbankzugriff, KEIN eval(),
 * KEINE Eloquent-Kopplung, keine Seiteneffekte — daher 1:1 übernommen.
 *
 * Jede unterstützte Einheit gehört zu genau EINER Dimension (Länge, Fläche,
 * Volumen, Stück, Masse, Volumen-Flüssig, Leistung, Energie, Winkel, Anteil,
 * Zeit). Innerhalb einer Dimension wird über einen Faktor relativ zur
 * Basis-Einheit umgerechnet (z. B. Länge-Basis = Meter).
 *
 * WICHTIG (Sicherheit/Fachlogik):
 *  - Es findet KEINE automatische dimensionsfremde Umrechnung statt. Wird in
 *    einer Berechnung z. B. Länge (m) mit Masse (kg) gemischt, liefert
 *    sindKompatibel()=false und der aufrufende Dienst erzeugt eine
 *    Plausibilitäts-WARNUNG — niemals eine stillschweigende Auto-Änderung.
 */
class UnitConversionService
{
    /**
     * Einheiten-Katalog: einheit => [dimension, faktor-zur-basis].
     *
     * Der Faktor bildet "1 Einheit = faktor Basiseinheiten" ab. Beispiel:
     * 1 cm = 0.01 m → ['laenge', 0.01]. Die Basiseinheit je Dimension hat
     * Faktor 1.0.
     *
     * @var array<string, array{0: string, 1: float}>
     */
    private const KATALOG = [
        // Länge — Basis: m
        'mm'   => ['laenge', 0.001],
        'cm'   => ['laenge', 0.01],
        'dm'   => ['laenge', 0.1],
        'm'    => ['laenge', 1.0],
        'km'   => ['laenge', 1000.0],

        // Fläche — Basis: m²
        'mm2'  => ['flaeche', 0.000001],
        'cm2'  => ['flaeche', 0.0001],
        'dm2'  => ['flaeche', 0.01],
        'm2'   => ['flaeche', 1.0],
        'm²'   => ['flaeche', 1.0],

        // Volumen (Raum) — Basis: m³
        'cm3'  => ['volumen', 0.000001],
        'dm3'  => ['volumen', 0.001],
        'm3'   => ['volumen', 1.0],
        'm³'   => ['volumen', 1.0],

        // Stück / Anzahl — Basis: Stk
        'stk'  => ['stueck', 1.0],
        'stück'=> ['stueck', 1.0],
        'stueck' => ['stueck', 1.0],
        'st'   => ['stueck', 1.0],

        // Masse — Basis: kg
        'g'    => ['masse', 0.001],
        'kg'   => ['masse', 1.0],
        't'    => ['masse', 1000.0],

        // Flüssigvolumen — Basis: l
        'ml'   => ['fluessig', 0.001],
        'l'    => ['fluessig', 1.0],

        // Leistung — Basis: kW
        'w'    => ['leistung', 0.001],
        'kw'   => ['leistung', 1.0],
        'kwp'  => ['leistung', 1.0],

        // Energie — Basis: kWh
        'wh'   => ['energie', 0.001],
        'kwh'  => ['energie', 1.0],
        'mwh'  => ['energie', 1000.0],

        // Winkel — Basis: Grad
        'grad' => ['winkel', 1.0],
        '°'    => ['winkel', 1.0],

        // Anteil — Basis: % (1.0 == ein Prozentpunkt; Bruchteil 0..1 = nicht hier)
        '%'    => ['anteil', 1.0],
        'prozent' => ['anteil', 1.0],

        // Zeit — Basis: h
        'min'  => ['zeit', 1.0 / 60.0],
        'h'    => ['zeit', 1.0],
        'std'  => ['zeit', 1.0],
    ];

    /**
     * Normalisiert eine Einheit (trim, Kleinschreibung) für den Katalog-Lookup.
     */
    public function normalize(?string $einheit): ?string
    {
        if ($einheit === null) {
            return null;
        }

        $norm = mb_strtolower(trim($einheit));

        return $norm === '' ? null : $norm;
    }

    /** Ist die Einheit im Katalog bekannt? */
    public function kennt(?string $einheit): bool
    {
        $norm = $this->normalize($einheit);

        return $norm !== null && array_key_exists($norm, self::KATALOG);
    }

    /**
     * Dimension einer Einheit (z. B. 'laenge') oder null bei unbekannter Einheit.
     */
    public function dimension(?string $einheit): ?string
    {
        $norm = $this->normalize($einheit);

        if ($norm === null || ! array_key_exists($norm, self::KATALOG)) {
            return null;
        }

        return self::KATALOG[$norm][0];
    }

    /**
     * Gehören beide Einheiten zur selben Dimension? Zwei unbekannte/einheitenlose
     * Werte gelten als kompatibel (reine Zahlen ohne Maß).
     */
    public function sindKompatibel(?string $von, ?string $nach): bool
    {
        $dimVon = $this->dimension($von);
        $dimNach = $this->dimension($nach);

        // Einheitenlose Zahlen sind untereinander immer kompatibel.
        if ($dimVon === null && $dimNach === null) {
            return true;
        }

        // Eine Seite einheitenlos, die andere mit Maß → nicht eindeutig konvertierbar.
        if ($dimVon === null || $dimNach === null) {
            return false;
        }

        return $dimVon === $dimNach;
    }

    /**
     * Rechnet einen Wert von der Quell- in die Ziel-Einheit um.
     *
     * Liefert null, wenn die Einheiten dimensionsfremd sind (m → kg) oder eine
     * Einheit unbekannt ist und nicht beide einheitenlos sind. Der Aufrufer
     * behandelt null als „nicht umrechenbar" (Plausibilitäts-Warnung), niemals
     * als 0.
     *
     * Sind beide Einheiten einheitenlos/leer, wird der Wert unverändert
     * zurückgegeben (reine Zahl).
     */
    public function convert(float $wert, ?string $von, ?string $nach): ?float
    {
        $normVon = $this->normalize($von);
        $normNach = $this->normalize($nach);

        // Beide einheitenlos → Wert unverändert.
        if ($normVon === null && $normNach === null) {
            return $wert;
        }

        // Gleiche (normalisierte) Einheit → keine Umrechnung nötig.
        if ($normVon !== null && $normVon === $normNach) {
            return $wert;
        }

        if (! $this->sindKompatibel($normVon, $normNach)) {
            return null;
        }

        // Ab hier sind beide bekannt und gleicher Dimension.
        $faktorVon = self::KATALOG[$normVon][1];
        $faktorNach = self::KATALOG[$normNach][1];

        if ($faktorNach == 0.0) {
            return null;
        }

        // Wert → Basiseinheit → Zieleinheit.
        return ($wert * $faktorVon) / $faktorNach;
    }
}
