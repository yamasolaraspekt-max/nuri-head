<?php

namespace App\Services\Auslegung;

use App\Models\LeadAlternativeAdd;

/**
 * Ketten-Adapter Gebäudeakte → WpAuslegungsEingabe (Welle A4 / V1.5 · 2026-07-16).
 *
 * Baut die Auslegungs-Eingabe aus dem kanonischen Objekt PLUS den vom Menschen bestätigten
 * Operanden (Vorschlag + Bestätigung — Operanden-Gate: der Adapter erfindet NICHTS):
 * - aus dem Objekt belegt: PLZ, lat/lon, Vorlauftemperatur, Jahres-Heizenergie (nur wenn kWh belegt)
 * - vom Menschen bestätigt: Heizlast phiHl, WP-Typ, Heizsystem, WW-Entscheidung, optional qWw
 * Fehlt ein Pflicht-Operand, entscheidet der WpAuslegungsketteService per Gate (gates_offen) —
 * der Adapter überspielt keine Lücke. Ergebnis bleibt Stufe 3a: informativ, nie verbindlich.
 */
class GebaeudeakteAuslegungsAdapter
{
    /** Vorbelegung aus dem Objekt — nur belegbare Werte, für die Anzeige im Formular. */
    public function vorbelegung(LeadAlternativeAdd $objekt): array
    {
        return [
            'plz' => $objekt->postcode ?: null,
            'lat' => is_numeric($objekt->lat) ? (float) $objekt->lat : null,
            'lon' => is_numeric($objekt->lon) ? (float) $objekt->lon : null,
            'vorlauf_c' => is_numeric($objekt->flow_temperature) ? (float) $objekt->flow_temperature : null,
            'q_heiz_kwh' => $this->jahresHeizenergieKwh($objekt),
            'heizsystem_hinweis' => $objekt->heat_distribution ?: null, // Anzeige; die Auswahl bestätigt der Mensch
        ];
    }

    /**
     * Eingabe aus Objekt + bestätigten Operanden. $bestaetigt-Schlüssel:
     * phi_hl_kw, wp_typ, heizsystem, ww_mit_wp (bool), q_ww_kwh?, q_heiz_kwh?, vorlauf_c?
     */
    public function eingabe(LeadAlternativeAdd $objekt, array $bestaetigt): WpAuslegungsEingabe
    {
        $vor = $this->vorbelegung($objekt);

        $qHeiz = $bestaetigt['q_heiz_kwh'] ?? null;
        $qHeiz = is_numeric($qHeiz) && (float) $qHeiz > 0 ? (float) $qHeiz : $vor['q_heiz_kwh'];

        $vorlauf = $bestaetigt['vorlauf_c'] ?? null;
        $vorlauf = is_numeric($vorlauf) && (float) $vorlauf > 0 ? (float) $vorlauf : $vor['vorlauf_c'];

        return new WpAuslegungsEingabe(
            phiHlKw: is_numeric($bestaetigt['phi_hl_kw'] ?? null) && (float) $bestaetigt['phi_hl_kw'] > 0
                ? (float) $bestaetigt['phi_hl_kw'] : null,
            qHeizKwh: $qHeiz,
            qWwKwh: is_numeric($bestaetigt['q_ww_kwh'] ?? null) ? (float) $bestaetigt['q_ww_kwh'] : 0.0,
            wwMitWp: array_key_exists('ww_mit_wp', $bestaetigt) ? (bool) $bestaetigt['ww_mit_wp'] : null,
            vorlaufC: $vorlauf,
            plz: $vor['plz'],
            wpTyp: ($bestaetigt['wp_typ'] ?? '') !== '' ? (string) $bestaetigt['wp_typ'] : null,
            heizsystem: ($bestaetigt['heizsystem'] ?? '') !== '' ? (string) $bestaetigt['heizsystem'] : null,
            belastbar: false, // Stufe 3a: immer informativ
            lat: $vor['lat'],
            lon: $vor['lon'],
        );
    }

    /** Jahres-Heizenergie NUR, wenn sie belegt in kWh vorliegt (Einheiten-Kante: nie umrechnen/raten). */
    private function jahresHeizenergieKwh(LeadAlternativeAdd $objekt): ?float
    {
        if (is_numeric($objekt->annual_heating_energy_consumption_kwh) && (float) $objekt->annual_heating_energy_consumption_kwh > 0) {
            return (float) $objekt->annual_heating_energy_consumption_kwh;
        }

        $einheit = strtolower(trim((string) $objekt->heating_energy_unit));
        if (in_array($einheit, ['kwh', 'kwh/a', 'kwh/jahr'], true)
            && is_numeric($objekt->annual_heating_energy_consumption)
            && (float) $objekt->annual_heating_energy_consumption > 0) {
            return (float) $objekt->annual_heating_energy_consumption;
        }

        return null; // Liter/m³ o. ä.: Umrechnung wäre eine Fach-Formel → nicht raten
    }
}
