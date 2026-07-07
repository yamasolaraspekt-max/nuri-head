<?php

namespace App\Services\Heizlast;

use App\Models\HeizlastProjekt;
use App\Models\Konstruktion;
use App\Models\RaumGeometrie;

/**
 * Leitet aus einer Raum-Geometrie (Polygon + Wandsegmente) genau die Räume/Bauteile ab,
 * die die Engine (HeizlastRechner/HeizlastProjektService) erwartet — Brutto-Wandflächen plus
 * separate Fenster/Tür-Bauteile (RaumHuelleService zieht die Öffnungen pro Raum ab).
 *
 * Die Engine bleibt unangetastet; dieser Service erzeugt nur ihre Eingabe.
 *
 * Ticket-Naht (Diff=0 bis auf die eine Stelle): der wberechnung-Zweig
 * `fenster_artikel_id → Artikel::with('fensterSpec')->u_w` entfällt — ticket hat keine
 * Fenster-Artikel-Domäne. Fallback-Kette bleibt: direkter U (A) > fenster_typ.
 */
class GeometrieAbleitungService
{
    /**
     * Raum-Array exakt im Engine-Format aus einer Geometrie.
     *
     * @return array<string, mixed>
     */
    public function ausGeometrie(RaumGeometrie $geometrie): array
    {
        $raum = $geometrie->heizlastRaum;
        $grundflaeche = round($this->polygonFlaecheM2($geometrie->polygon ?? []), 2);
        $hoeheM = $geometrie->hoehe_mm !== null ? $geometrie->hoehe_mm / 1000.0 : (float) ($raum->hoehe_m ?? 2.5);

        $bauteile = [];
        foreach ($geometrie->wand_segmente ?? [] as $seg) {
            $laengeMm = $this->segmentLaengeMm($seg['von'] ?? [], $seg['bis'] ?? []);
            $hoeheMm = $hoeheM * 1000.0;
            // Brutto-Wandfläche – Öffnungen werden NICHT abgezogen (RaumHuelleService tut das).
            $bauteile[] = array_merge([
                'typ' => $seg['bauteil_typ'] ?? 'wand',
                'grenzflaeche' => $seg['grenzflaeche'] ?? 'aussen',
                'azimut_grad' => $seg['azimut_grad'] ?? null,
                'flaeche_m2' => round($laengeMm * $hoeheMm / 1_000_000.0, 2),
            ], $this->opakeUQuelle($seg));

            foreach ($seg['oeffnungen'] ?? [] as $oeffnung) {
                $flaeche = round(((float) ($oeffnung['breite_mm'] ?? 0)) * ((float) ($oeffnung['hoehe_mm'] ?? 0)) / 1_000_000.0, 2);
                $bauteile[] = array_merge([
                    'typ' => $oeffnung['typ'] ?? 'fenster',
                    'grenzflaeche' => $seg['grenzflaeche'] ?? 'aussen', // gleiche Grenzfläche wie die Wand
                    'azimut_grad' => $seg['azimut_grad'] ?? null,
                    'flaeche_m2' => $flaeche,
                ], $this->fensterUQuelle($oeffnung));
            }
        }

        foreach (['decke', 'boden'] as $flaeche) {
            $def = $geometrie->{$flaeche};
            if (! empty($def)) {
                $bauteile[] = array_merge([
                    'typ' => $def['bauteil_typ'] ?? $flaeche,
                    'grenzflaeche' => $def['grenzflaeche'] ?? ($flaeche === 'boden' ? 'erdreich' : 'aussen'),
                    'flaeche_m2' => $grundflaeche,
                ], $this->opakeUQuelle($def));
            }
        }

        return [
            'name' => $raum->name ?? 'Raum',
            'nutzung' => $raum->nutzung ?? 'wohnen',
            'theta_int_c' => $raum->theta_int_c ?? null,
            'grundflaeche_m2' => $grundflaeche,
            'hoehe_m' => round($hoeheM, 2),
            'luftwechsel_1h' => $raum->luftwechsel_1h ?? null,
            'heizkoerper' => $raum->heizkoerper ?? null,
            'fussbodenheizung' => $raum->fussbodenheizung ?? null,
            'bauteile' => $bauteile,
        ];
    }

    /**
     * Persistiert die abgeleiteten Räume in das Projekt: aktualisiert je Raum mit Geometrie
     * Grundfläche/Höhe und ersetzt dessen Bauteile. Geometrie bleibt erhalten (kein Raum-Delete).
     */
    public function schreibeInProjekt(HeizlastProjekt $projekt): void
    {
        $projekt->load('raeume.geometrie');
        foreach ($projekt->raeume as $raum) {
            if ($raum->geometrie === null) {
                continue;
            }
            $abgeleitet = $this->ausGeometrie($raum->geometrie);
            $raum->update([
                'grundflaeche_m2' => $abgeleitet['grundflaeche_m2'],
                'hoehe_m' => $abgeleitet['hoehe_m'],
            ]);
            $raum->bauteile()->delete();
            foreach (array_values($abgeleitet['bauteile']) as $ci => $b) {
                $raum->bauteile()->create([
                    'typ' => $b['typ'] ?? 'wand',
                    'grenzflaeche' => $b['grenzflaeche'] ?? 'aussen',
                    'azimut_grad' => $b['azimut_grad'] ?? null,
                    'flaeche_m2' => $b['flaeche_m2'] ?? 0,
                    'u_strategie' => $b['u_strategie'] ?? 'C',
                    'u_wert' => $b['u_wert'] ?? null,
                    'fenster_typ' => $b['fenster_typ'] ?? null,
                    'konstruktion_id' => $b['konstruktion_id'] ?? null,
                    'reihenfolge' => $ci,
                ]);
            }
        }
    }

    /**
     * Gauß'sche Trapezformel (Shoelace) über ein Polygon in mm → Fläche in m².
     *
     * @param  array<int, array{x: int|float, y: int|float}>  $polygon
     */
    public function polygonFlaecheM2(array $polygon): float
    {
        $n = count($polygon);
        if ($n < 3) {
            return 0.0;
        }
        $summe = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $summe += ((float) $polygon[$i]['x']) * ((float) $polygon[$j]['y'])
                - ((float) $polygon[$j]['x']) * ((float) $polygon[$i]['y']);
        }

        return abs($summe) / 2.0 / 1_000_000.0;
    }

    /**
     * @param  array{x?: int|float, y?: int|float}  $von
     * @param  array{x?: int|float, y?: int|float}  $bis
     */
    private function segmentLaengeMm(array $von, array $bis): float
    {
        $dx = (float) ($bis['x'] ?? 0) - (float) ($von['x'] ?? 0);
        $dy = (float) ($bis['y'] ?? 0) - (float) ($von['y'] ?? 0);

        return sqrt($dx * $dx + $dy * $dy);
    }

    /**
     * U-Quelle opaker Bauteile: direkter U (A) > konstruktion_id (u_wert_berechnet, A) > Baualter (C).
     *
     * @param  array<string, mixed>  $def
     * @return array<string, mixed>
     */
    private function opakeUQuelle(array $def): array
    {
        if (isset($def['u_wert']) && (float) $def['u_wert'] > 0) {
            return ['u_strategie' => 'A', 'u_wert' => (float) $def['u_wert']];
        }
        if (! empty($def['konstruktion_id'])) {
            $u = Konstruktion::find($def['konstruktion_id'])?->u_wert_berechnet;
            if ($u !== null) {
                return ['u_strategie' => 'A', 'u_wert' => (float) $u, 'konstruktion_id' => (int) $def['konstruktion_id']];
            }
        }

        return ['u_strategie' => $def['u_strategie'] ?? 'C'];
    }

    /**
     * U-Quelle Fenster/Tür: direkter U (A) > fenster_typ (Fallback).
     * (wberechnung-Zweig fenster_artikel_id/FensterSpec entfällt — ticket hat keine Fenster-Domäne.)
     *
     * @param  array<string, mixed>  $oeffnung
     * @return array<string, mixed>
     */
    private function fensterUQuelle(array $oeffnung): array
    {
        if (isset($oeffnung['u_wert']) && (float) $oeffnung['u_wert'] > 0) {
            return ['u_strategie' => 'A', 'u_wert' => (float) $oeffnung['u_wert']];
        }

        return ['fenster_typ' => $oeffnung['fenster_typ'] ?? '2fach'];
    }
}
