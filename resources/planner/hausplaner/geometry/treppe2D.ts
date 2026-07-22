/**
 * Hausplaner — Treppen-2D-Symbol (reine Geometrie), Grundlage fürs spätere Werkzeug/Rendering.
 *
 * Aus einer Lauflinie (start→end), Laufbreite und Geschosshöhe entsteht das Grundriss-Symbol:
 * Umriss-Rechteck + Trittstufen-Linien (quer zur Lauflinie) + Aufwärts-Pfeil. Die Stufenzahl
 * kommt aus dem getesteten `berechneTreppe` (DIN 18065). Keine Szene-Mutation, kein three/Konva.
 */

import { berechneTreppe, type TreppenBereich } from './treppenBerechnung';

export interface P {
  x: number;
  y: number;
}

export interface Treppe2DEingabe {
  /** Lauflinie (Mittellinie) im Grundriss, mm. */
  start: P;
  end: P;
  /** Laufbreite, mm. */
  laufbreite: number;
  /** Geschosshöhe OKFF→OKFF, mm. */
  geschosshoehe: number;
  gewuenschteSteigung?: number;
  bereich?: TreppenBereich;
}

export interface Treppe2DSymbol {
  /** Umriss-Rechteck (4 Ecken, im Uhrzeigersinn ab start-links). */
  umriss: [P, P, P, P];
  /** Trittstufen-Linien quer zur Lauflinie (je [links, rechts]). */
  stufen: Array<[P, P]>;
  /** Aufwärts-Pfeil: Linie start→end (Mitte) + Spitze. */
  pfeil: { von: P; bis: P };
  anzahlSteigungen: number;
  anzahlAuftritte: number;
  steigungshoehe: number;
  auftritt: number;
  bestanden: boolean;
}

const runde = (p: P): P => ({ x: Math.round(p.x), y: Math.round(p.y) });

export function treppe2DSymbol(e: Treppe2DEingabe): Treppe2DSymbol {
  const dx = e.end.x - e.start.x;
  const dy = e.end.y - e.start.y;
  const len = Math.hypot(dx, dy);
  const ux = len === 0 ? 1 : dx / len;
  const uy = len === 0 ? 0 : dy / len;
  const nx = -uy; // Linksnormale
  const ny = ux;
  const hw = e.laufbreite / 2;

  const t = berechneTreppe({
    geschosshoehe: e.geschosshoehe,
    verfuegbareLauflaenge: len > 0 ? len : undefined,
    gewuenschteSteigung: e.gewuenschteSteigung,
    laufbreite: e.laufbreite,
    bereich: e.bereich,
  });

  const links = (p: P): P => ({ x: p.x + nx * hw, y: p.y + ny * hw });
  const rechts = (p: P): P => ({ x: p.x - nx * hw, y: p.y - ny * hw });

  const umriss: [P, P, P, P] = [
    runde(links(e.start)),
    runde(links(e.end)),
    runde(rechts(e.end)),
    runde(rechts(e.start)),
  ];

  // Trittstufen: quer zur Lauflinie an jeder Auftritts-Grenze (k·auftritt entlang der Linie).
  const stufen: Array<[P, P]> = [];
  const auftritt = t.auftritt;
  const anzGrenzen = t.anzahlAuftritte; // Linien zwischen den Stufen (0 und Ende sind der Umriss)
  for (let k = 1; k < anzGrenzen; k++) {
    const s = k * auftritt;
    const px = e.start.x + ux * s;
    const py = e.start.y + uy * s;
    stufen.push([runde(links({ x: px, y: py })), runde(rechts({ x: px, y: py }))]);
  }

  return {
    umriss,
    stufen,
    pfeil: { von: runde(e.start), bis: runde(e.end) },
    anzahlSteigungen: t.anzahlSteigungen,
    anzahlAuftritte: t.anzahlAuftritte,
    steigungshoehe: t.steigungshoehe,
    auftritt: t.auftritt,
    bestanden: t.bestanden,
  };
}
