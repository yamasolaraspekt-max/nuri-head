/**
 * Hausplaner P1b — Platzierungs-Mathematik (rein, ohne three.js-Import, mm → three-Meter).
 *
 * Verwandelt Wand-lokale Quader (segmentiereWand) und Raum-Polygone in fertige
 * three-Platzierungen: Zentrum (three-Koordinaten), Rotation um die three-Y-Achse,
 * Abmessungen in Metern. Die EINE Achsen-Umrechnung bleibt weltZuThree (▲P1-K2) —
 * hier wird nur damit gerechnet, nie neu umgerechnet.
 *
 * Rotations-Herleitung (dokumentiert, testbar): three.rotation.y = θ bildet die lokale
 * +x-Achse auf (cos θ, 0, −sin θ) ab. Die Wandachse (dx, dy) in Welt wird zu (dx, −dy)
 * in three(x,z); aus cos θ = dx/L und −sin θ = −dy/L folgt θ = atan2(dy, dx) —
 * der Welt-Winkel der Wand direkt (Beispiele: W→O ⇒ 0; S→N ⇒ +π/2, lokale +x zeigt −z = Nord).
 */

import type { WallNode } from '../../domain/scene.types';
import type { WandQuader } from './segmentierung';
import type { WandBand, Punkt } from '../../geometry/wallGeometry';
import { weltZuThree, type ThreePunkt } from './adapter';

/**
 * Gehrung (Auftrag 3D-Wandecken, Ansatz A): Grundriss-Viereck EINES Wandsegments in Welt-mm, das die
 * bereits gehrten `wandBaender`-Ecken an den Wand-ENDEN WIEDERVERWENDET (eine Wahrheit — kein zweiter
 * Miter im 3D) und an Öffnungs-/Innensegment-Grenzen rechtwinklig zur Wandachse bleibt (die Gehrung
 * betrifft nur die Enden). Reihenfolge [leftVon, leftBis, rightBis, rightVon]. Rein/testbar (kein three).
 * Offenes Wandende ohne Nachbar ⇒ `wandBaender` liefert dort die quadratische Ecke ⇒ automatisch rechtwinklig.
 */
export function wandSegmentGrundriss(
  band: WandBand,
  wand: WallNode,
  quader: Pick<WandQuader, 'vonMm' | 'bisMm'>,
): [Punkt, Punkt, Punkt, Punkt] {
  const dx = wand.end.x - wand.start.x, dy = wand.end.y - wand.start.y;
  const laenge = Math.hypot(dx, dy) || 1;
  const dirx = dx / laenge, diry = dy / laenge;
  const ulx = -diry, uly = dirx;                 // Links-Normale (perpLinks), konstant über die Wand
  const h = wand.thickness / 2;
  const [startLinks, endLinks, endRechts, startRechts] = band.ecken;
  const amStart = quader.vonMm <= 0.5;           // Segment am Wand-Anfang → Band-Gehrung übernehmen
  const amEnde = quader.bisMm >= laenge - 0.5;   // Segment am Wand-Ende → Band-Gehrung übernehmen
  const kante = (offset: number, seite: 1 | -1): Punkt => ({
    x: wand.start.x + dirx * offset + seite * ulx * h,
    y: wand.start.y + diry * offset + seite * uly * h,
  });
  return [
    amStart ? startLinks : kante(quader.vonMm, 1),
    amEnde ? endLinks : kante(quader.bisMm, 1),
    amEnde ? endRechts : kante(quader.bisMm, -1),
    amStart ? startRechts : kante(quader.vonMm, -1),
  ];
}

/**
 * Die 8 three-Eckpunkte (Meter, y-up) des Wand-Segment-Prismas: 4 unten, 4 oben (Reihenfolge des
 * Grundriss-Vierecks). `weltZuThree` bleibt die EINE Achsen-Umrechnung (kein zweiter Rechenweg).
 */
export function wandSegmentPrismaThree(
  grundriss: readonly [Punkt, Punkt, Punkt, Punkt],
  untenMm: number,
  obenMm: number,
  elevationMm: number,
): ThreePunkt[] {
  const zUnten = elevationMm + untenMm, zOben = elevationMm + obenMm;
  return [
    ...grundriss.map((p) => weltZuThree({ x: p.x, y: p.y, z: zUnten })),
    ...grundriss.map((p) => weltZuThree({ x: p.x, y: p.y, z: zOben })),
  ];
}

/** Fertige Platzierung eines Quaders in der three-Szene (Meter, y-up). */
export interface QuaderPlatzierung {
  /** Mittelpunkt des Quaders in three-Koordinaten (Meter). */
  zentrum: ThreePunkt;
  /** Rotation um die three-Y-Achse (rad) — Welt-Winkel der Wandachse. */
  rotationY: number;
  /** Abmessungen in Metern: x = entlang der Wandachse, y = Höhe, z = Wanddicke. */
  masse: { x: number; y: number; z: number };
  art: WandQuader['art'];
  geklemmt: boolean;
  oeffnungId?: string;
}

/**
 * Platziert einen Wand-lokalen Quader in der Welt: Punkt auf der Wandachse bei der
 * Quader-Mitte, Höhenmitte über der Level-Elevation, Rotation = Wandwinkel.
 * Reine Funktion — testbar ohne Browser/WebGL (Abnahme-Baustein für Kriterium 1/2).
 */
export interface StufePlatzierung {
  zentrum: ThreePunkt;
  rotationY: number;
  masse: { x: number; y: number; z: number };
}

/**
 * Eine Treppen-Stufe (lokaler Quader aus treppe3DKoerper) in die three-Welt setzen — analog
 * platziereWandQuader: lokale x-Achse = Laufrichtung (start->end), y = Hoehe, z = Laufbreite.
 * Die EINE Achsen-Umrechnung bleibt weltZuThree; hier nur Verortung entlang der Lauflinie.
 */
export function platziereTreppenStufe(
  start: { x: number; y: number },
  end: { x: number; y: number },
  stufe: { mitte: [number, number, number]; groesse: [number, number, number] },
  levelElevationMm: number,
): StufePlatzierung {
  const dx = end.x - start.x;
  const dy = end.y - start.y;
  const len = Math.hypot(dx, dy);
  const ux = len === 0 ? 1 : dx / len;
  const uy = len === 0 ? 0 : dy / len;
  const nx = -uy;
  const ny = ux;
  const [lx, ly, lz] = stufe.mitte;
  const welt = {
    x: start.x + ux * lx + nx * ly,
    y: start.y + uy * lx + ny * ly,
    z: levelElevationMm + lz,
  };
  return {
    zentrum: weltZuThree(welt),
    rotationY: Math.atan2(dy, dx),
    masse: { x: stufe.groesse[0] / 1000, y: stufe.groesse[2] / 1000, z: stufe.groesse[1] / 1000 },
  };
}

export function platziereWandQuader(
  wand: WallNode,
  quader: WandQuader,
  levelElevationMm: number,
): QuaderPlatzierung {
  const dx = wand.end.x - wand.start.x;
  const dy = wand.end.y - wand.start.y;
  const laenge = Math.hypot(dx, dy);

  const mitteU = (quader.vonMm + quader.bisMm) / 2;
  const t = laenge === 0 ? 0 : mitteU / laenge;

  const weltZentrum = {
    x: wand.start.x + dx * t,
    y: wand.start.y + dy * t,
    z: levelElevationMm + (quader.untenMm + quader.obenMm) / 2,
  };

  return {
    zentrum: weltZuThree(weltZentrum),
    rotationY: Math.atan2(dy, dx),
    masse: {
      x: (quader.bisMm - quader.vonMm) / 1000,
      y: (quader.obenMm - quader.untenMm) / 1000,
      z: wand.thickness / 1000,
    },
    art: quader.art,
    geklemmt: quader.geklemmt === true,
    oeffnungId: quader.oeffnungId,
  };
}

/**
 * Raum-Polygon (Welt-mm, x/y) → three-Grundriss-Punkte in der XZ-Ebene (Meter) auf
 * Level-Elevation. three baut daraus eine flache Shape (P1b-Szene); die y-Koordinate
 * ist für alle Punkte identisch = elevation/1000.
 */
export function bodenPunkteThree(
  polygon: Array<{ x: number; y: number }>,
  levelElevationMm: number,
): { punkte: Array<{ x: number; z: number }>; y: number } {
  return {
    punkte: polygon.map((p) => {
      const t = weltZuThree({ x: p.x, y: p.y, z: levelElevationMm });
      return { x: t.x, z: t.z };
    }),
    y: levelElevationMm / 1000,
  };
}
