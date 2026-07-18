/**
 * Hausplaner P1 — CSG-freie Wand-Segmentierung (Spec p1-spec-3d.md ▲P1-K3).
 *
 * Wand + Öffnungen → deterministische Quader-Liste in WAND-LOKALEN Koordinaten
 * (u = mm entlang der Wandachse ab start, Höhe = mm ab Wand-Unterkante). Je Öffnung
 * entstehen Brüstungs-Quader (unter der Öffnung), Sturz-Quader (darüber) und
 * Zwischen-Quader (seitlich). Keine CSG-Bibliothek, mm-exakt, unit-testbar ohne Browser.
 *
 * Kanten (Spec §3):
 * - Kante 1: Öffnung exakt am Wandende ⇒ Zwischen-Quader Breite 0 ENTFÄLLT (kein 0-Volumen-Mesh).
 * - Kante 2: sillHeight + height > wall.height ⇒ P0 validiert das heute nicht (Rückgabe-Befund);
 *   hier wird GEKLEMMT gerendert (Sturz entfällt) und die Öffnung als geklemmt gemeldet —
 *   nie stillschweigend „passend gerechnet".
 * - Öffnungen, die über den Wand-Anfang/-Ende hinausragen, werden auf die Wand geklemmt
 *   (P0 lehnt width > Wandlänge bereits im Command ab — hier nur Render-Schutz).
 * - Überlappende Öffnungen: deterministisch in Offset-Reihenfolge; der Folgeausschnitt
 *   beginnt frühestens am Ende des vorigen (Bereinigung ist Sache des 2D-Editors, Kante 3-Geist).
 *
 * Reine Funktion: keine three.js-Abhängigkeit — P1b baut aus den Quadern die Meshes.
 */

import type { OpeningNode, WallNode } from '../../domain/scene.types';
import { wandLaenge } from '../../geometry/wallGeometry';

/** Ein axis-aligned Quader in Wand-lokalen Koordinaten (Dicke = Wanddicke, implizit). */
export interface WandQuader {
  /** 'voll' = Zwischen-/Randstück volle Höhe, 'bruestung' = unter, 'sturz' = über einer Öffnung. */
  art: 'voll' | 'bruestung' | 'sturz';
  vonMm: number;   // entlang der Wandachse, ab wall.start
  bisMm: number;
  untenMm: number; // ab Wand-Unterkante
  obenMm: number;
  /** Bei bruestung/sturz: die verursachende Öffnung. */
  oeffnungId?: string;
  /** Kante 2: Markierung statt stiller Korrektur — P1b färbt das Segment sichtbar ein. */
  geklemmt?: boolean;
}

export interface SegmentierteWand {
  laengeMm: number;
  hoeheMm: number;
  dickeMm: number;
  quader: WandQuader[];
  /** IDs der Öffnungen, die über die Wandhöhe hinausragen (Kante 2) — für UI-Markierung. */
  geklemmteOeffnungen: string[];
}

/** Volumen eines Quaders in mm³ (Dicke der Wand eingerechnet). */
export function quaderVolumenMm3(q: WandQuader, dickeMm: number): number {
  return (q.bisMm - q.vonMm) * (q.obenMm - q.untenMm) * dickeMm;
}

/**
 * Segmentiert eine Wand mit ihren Öffnungen in Quader. `oeffnungen` sind die Öffnungen
 * DIESER Wirtswand (hostWallId wird nicht erneut geprüft — der Aufrufer filtert, die
 * Funktion bleibt rein). Reihenfolge der Eingabe ist egal (wird nach Offset sortiert).
 */
export function segmentiereWand(wand: WallNode, oeffnungen: OpeningNode[]): SegmentierteWand {
  const laenge = wandLaenge(wand.start, wand.end);
  const hoehe = wand.height;

  const quader: WandQuader[] = [];
  const geklemmteOeffnungen: string[] = [];

  const sortiert = [...oeffnungen].sort(
    (a, b) => a.offsetFromWallStart - b.offsetFromWallStart,
  );

  let cursor = 0;
  for (const o of sortiert) {
    // Auf die Wand klemmen (Render-Schutz; P0 lehnt Überbreite im Command ab).
    let von = Math.max(0, Math.min(o.offsetFromWallStart, laenge));
    let bis = Math.max(0, Math.min(o.offsetFromWallStart + o.width, laenge));
    von = Math.max(von, cursor); // Überlappung: deterministisch, kein negativer Quader
    if (bis <= von) {
      continue; // degeneriert/außerhalb/komplett überlappt ⇒ kein Ausschnitt
    }

    // Zwischen-Quader links der Öffnung (Kante 1: Breite 0 entfällt).
    if (von > cursor) {
      quader.push({ art: 'voll', vonMm: cursor, bisMm: von, untenMm: 0, obenMm: hoehe });
    }

    const sill = Math.max(0, o.sillHeight);
    const oben = sill + o.height;
    const geklemmt = oben > hoehe; // Kante 2 — ehrlich markieren, nie still korrigieren
    if (geklemmt) {
      geklemmteOeffnungen.push(o.id);
    }
    const effSill = Math.min(sill, hoehe);
    const effOben = Math.min(oben, hoehe);

    // Brüstungs-Quader unter der Öffnung (Tür: sill 0 ⇒ entfällt).
    if (effSill > 0) {
      quader.push({
        art: 'bruestung', vonMm: von, bisMm: bis, untenMm: 0, obenMm: effSill,
        oeffnungId: o.id, ...(geklemmt ? { geklemmt: true } : {}),
      });
    }

    // Sturz-Quader über der Öffnung (geklemmt ⇒ entfällt per Definition effOben = hoehe).
    if (effOben < hoehe) {
      quader.push({
        art: 'sturz', vonMm: von, bisMm: bis, untenMm: effOben, obenMm: hoehe,
        oeffnungId: o.id,
      });
    }

    cursor = bis;
  }

  // Rest-Quader rechts der letzten Öffnung (Kante 1: Breite 0 entfällt).
  if (laenge > cursor) {
    quader.push({ art: 'voll', vonMm: cursor, bisMm: laenge, untenMm: 0, obenMm: hoehe });
  }

  return {
    laengeMm: laenge,
    hoeheMm: hoehe,
    dickeMm: wand.thickness,
    quader,
    geklemmteOeffnungen,
  };
}
