<?php

/*
 * WP Stufe 3 — Ranking-Config der Auslegungskette (EINE Quelle, E-WP4).
 *
 * Alle Ranking-Parameter liegen hier; Yama ändert sie per Config (kein Code).
 * Operanden-Gate: Kriterien ohne belegbare Datenquelle werden NICHT angewandt (nie erfundene Werte).
 */

return [
    // Label-Pflicht (E-WP4): jedes Ranking ist informativ, bis Yama-Geschäftsentscheidung UND
    // AP-1-Belastbarkeit (G5) erfüllt sind.
    'label_informativ' => 'informativ, nicht verbindlich',

    // Stufe A — hartes Eignungs-Gate (Muss, kein Gewicht).
    'gate_a' => [
        'min_deckung_ne_pct' => 90, // Heizlastdeckung am Bivalenzpunkt (mind.)
    ],

    // Stufe B — Gewichte der Geeigneten (Default; Config-änderbar).
    'gewichte' => [
        'jaz' => 0.50,
        'investitionskosten' => 0.30,
        'verfuegbarkeit' => 0.20,
    ],

    // Datenquellen-Status je Kriterium (E-WP5 / Preis-Frage, an Yama):
    // Ohne belegbare Quelle wird das Gewicht NICHT angewandt (Gewichte über die vorhandenen
    // Kriterien renormalisiert), und der Kandidat trägt einen quelle_fehlt-Marker.
    'quelle_vorhanden' => [
        'jaz' => true,
        'investitionskosten' => false, // WP-Preis via OMD/IDS (inert); kein per-Gerät-Feld in ticket
        'verfuegbarkeit' => false,      // kein Verfügbarkeitsfeld am WP-Gerät
    ],

    // Primärpartner: wird GEKENNZEICHNET (Feld), NICHT versteckt geboostet.
    'nibe_hersteller' => 'NIBE',
];
