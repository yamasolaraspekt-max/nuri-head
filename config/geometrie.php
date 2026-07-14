<?php

/*
 * G0b / AP-4b — Toleranzvertrag (EINE zentrale Quelle für alle Geometrie-Toleranzen/Rundungen).
 *
 * Keine „ungefähr gleich"-Werte irgendwo sonst im Code. Jede Änderung ist eine Config-Änderung
 * (kein Code). Zugriff ausschließlich über App\Support\GeometrieToleranz.
 *
 * [IST] Einheiten-/Rundungsweg der vorhandenen Geometrie-Engine (in G0a dokumentiert):
 *   Polygon/Segmente als mm-Integer → Shoelace |Σ|/2 → /1_000_000 → m². Intern wird NICHT auf cm
 *   gerundet; Rundung findet ausschließlich an den Anzeige-/Exportgrenzen statt.
 */

return [
    // Punkte näher als dieser Abstand gelten als identisch (Deduplizierung).
    'punktgleichheit_mm' => 1,

    // Kanten kürzer als dies sind Degenerat-Verdacht → Blocker.
    'min_segment_mm' => 10,

    // Polygone kleiner als dies (Rohfläche in mm²) werden abgelehnt (= 0,01 m²).
    'min_polygon_flaeche_mm2' => 10000,

    // Vorgriff-Konstanten (in G0b NUR definiert, NICHT verwendet — kein Anschluss-/Winkel-Code hier).
    'max_wandanschluss_spalt_mm' => 5,
    'winkel_toleranz_grad' => 0.5,

    // Rundung ausschließlich an Ausgabegrenzen.
    'anzeige_rundung_m2' => 2,

    // Zulässige relative numerische Abweichung bei Vergleichen.
    'recalc_abweichung_rel' => 1e-9,
];
