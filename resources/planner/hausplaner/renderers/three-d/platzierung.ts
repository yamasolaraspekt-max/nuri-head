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
import { weltZuThree, type ThreePunkt } from './adapter';

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
