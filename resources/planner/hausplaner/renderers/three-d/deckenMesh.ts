/**
 * Decke (Feature A) — REINE, testbare Geometrie-Helfer (kein three/WebGL). Der three-Aufsatz (szene.ts)
 * baut aus dem Decken-Polygon minus `oeffnungen` eine Shape-mit-Löchern auf der Wand-Oberkante. Hier nur
 * die belastbaren Kennwerte. Die Etagen-Stapel-Ableitung liegt seit Z1-E0-1 in geometry/hoehenkette.ts.
 */
import type { CeilingNode } from '../../domain/scene.types';
import { polygonFlaecheM2 } from '../../geometry/polygonFlaeche';

// Z1-E0-1: `deckenOberkanteMm` und `naechsteEtageElevationMm` wohnen jetzt in
// `geometry/hoehenkette.ts` — die Hoehenkette ist Fachgeometrie und gehoert keinem
// Darstellungsweg. Diese Datei rechnet sie nicht mehr; wer sie braucht, liest sie dort.

/** mm² pro m² — polygonFlaecheM2 rechnet KEINE Einheit um (Input in mm ⇒ Ergebnis mm²). */
const MM2_PRO_M2 = 1_000_000;

/** Netto-Deckenfläche (m²) = Umriss minus Durchbrüche (Treppenauge etc.). mm-Polygone → m². Nie negativ. */
export function deckenNettoFlaecheM2(ceiling: CeilingNode): number {
  const brutto = ceiling.polygon.length >= 3 ? Math.max(0, polygonFlaecheM2(ceiling.polygon)) : 0;
  const loch = (ceiling.oeffnungen ?? []).reduce(
    (s, o) => s + (o.polygon.length >= 3 ? Math.max(0, polygonFlaecheM2(o.polygon)) : 0),
    0,
  );
  return Math.max(0, brutto - loch) / MM2_PRO_M2;
}

