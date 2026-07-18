/**
 * Hausplaner P1 — Achsen-/Einheiten-Adapter (Spec p1-spec-3d.md ▲P1-K2).
 *
 * DIE eine Umrechnung Welt ↔ three.js — nirgendwo sonst wird umgerechnet:
 *   Welt (P0):  x/y horizontal, z = Höhe, Nord = +y, GANZE Millimeter.
 *   three (y-up): three.x = x/1000 · three.y = z/1000 · three.z = −y/1000 (Meter, Float).
 * Folge: Nord zeigt in three nach −z, Süden nach +z. Meter existieren NUR im Renderer;
 * die Persistenz bleibt mm-Integer (threeZuWelt rundet zurück auf ganze mm — Round-Trip
 * ist byte-gleich, eigener Test inkl. Extremwerten).
 *
 * Beide Funktionen sind LINEAR (reine Skalierung + Achsentausch, keine Translation) —
 * sie gelten daher unverändert auch für Richtungsvektoren (z. B. Wand-Normalen).
 */

/** Punkt/Vektor in der P0-Welt: ganze mm, z = Höhe, Nord = +y. */
export interface WeltPunkt {
  x: number;
  y: number;
  z: number;
}

/** Punkt/Vektor in three.js: Meter (Float), y-up, Nord = −z. */
export interface ThreePunkt {
  x: number;
  y: number;
  z: number;
}

export const MM_PRO_METER = 1000;

/** Welt (mm, z-up, Nord=+y) → three (m, y-up, Nord=−z). */
export function weltZuThree(p: WeltPunkt): ThreePunkt {
  return {
    x: p.x / MM_PRO_METER,
    y: p.z / MM_PRO_METER,
    z: -p.y / MM_PRO_METER,
  };
}

/**
 * three (m) → Welt (ganze mm). Math.round stellt die mm-Invariante wieder her:
 * für jeden ganzzahligen mm-Punkt gilt threeZuWelt(weltZuThree(p)) ≡ p byte-gleich
 * (Float-Restfehler von /1000·1000 liegt weit unter 0,5 mm).
 */
export function threeZuWelt(p: ThreePunkt): WeltPunkt {
  return {
    x: Math.round(p.x * MM_PRO_METER),
    y: Math.round(-p.z * MM_PRO_METER),
    z: Math.round(p.y * MM_PRO_METER),
  };
}

/**
 * RendererAdapter — die Naht zwischen App-Shell und Renderern (Spec §1).
 * Der 3D-Renderer (P1b) implementiert dieses Interface; er liest den Store per
 * subscribe und erzeugt KEINE Commands außer selectNodes (Klick-Auswahl).
 */
export interface RendererAdapter {
  focusNode(nodeId: string): void;
  fitToScene(): void;
  setActiveLevel(levelId: string): void;
  dispose(): void;
}
