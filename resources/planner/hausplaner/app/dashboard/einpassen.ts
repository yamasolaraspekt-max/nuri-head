/**
 * AUF-62 — **„Ansicht einpassen"**: die eine Rechnung, die noch fehlte.
 *
 * **Der Befund des Planners:** Alles, was es braucht, lag schon da — der Maßstab als Zustand
 * (`zoom`, px pro mm), der Verschub als Zustand (`pan`, seit AUF-51), die Bounding-Box einer
 * Punktmenge (`geometry/editierGeometrie.ts`) und der Knopf selbst. Es fehlte **die Rechnung**.
 *
 * **Die Ansicht ist Anzeige, kein Modellzustand.** Diese Datei schreibt nichts ins Dokument, löst
 * keinen Befehl aus und kennt den Store nicht. Sie bekommt Punkte und eine Bühnengröße und liefert
 * Maßstab und Lage — rein, ohne DOM, damit sie im Testlauf prüfbar ist und nicht erst im Browser.
 *
 * **Die Abbildung, gegen die hier gerechnet wird** (aus der Bühne, nicht erfunden):
 * ```
 * schirm.x = pan.x + welt.x * zoom
 * schirm.y = pan.y − welt.y * zoom      ← scaleY = −zoom: die Welt wächst nach OBEN
 * ```
 * Das Minus in der zweiten Zeile ist die Stelle, an der ein Einpassen still falsch wird: bei
 * quadratischen Grundrissen sieht ein vertauschtes Vorzeichen richtig aus und bei länglichen
 * falsch. Deshalb prüft der Test zwei Seitenverhältnisse.
 */
import { bbox, type Punkt } from '../../geometry/editierGeometrie';
import { standardPan, type Pan } from './pan';
import type { SceneNode } from '../../domain/scene.types';

/** Rand zwischen Grundriss und Bühnenkante, px. Ein benannter Wert — der Plan klebt nicht an der Kante. */
export const EINPASS_RAND = 40;
/** Die Maßstabsgrenzen der Bühne (Mausrad, `:1203-1204`). Sie gelten hier **unverändert** weiter. */
export const ZOOM_MIN = 0.02;
export const ZOOM_MAX = 1;
/** Der Standardmaßstab beim Laden — die Antwort auf ein leeres Geschoss. */
export const ZOOM_STANDARD = 0.12;

export interface Einpassung {
  zoom: number;
  pan: Pan;
}

/**
 * Die Punkte, die ein Knoten zur Ausdehnung beiträgt.
 *
 * **Was hier NICHT steht, ist Absicht:** Öffnungen (`window`/`door`/`opening`) liegen als Versatz
 * **auf** einer Wand und tragen keine eigenen Weltkoordinaten — ihre Lage ergibt sich aus der Wand,
 * die ohnehin gezählt wird. Sie zu raten hieße, eine zweite Platzierungsrechnung neben der
 * vorhandenen aufzumachen. Ein Knotentyp, dessen Punkte diese Funktion nicht kennt, wird
 * **übersprungen**, nicht geschätzt.
 */
export function knotenPunkte(nodes: ReadonlyArray<SceneNode>): Punkt[] {
  const punkte: Punkt[] = [];
  for (const n of nodes) {
    if (n.type === 'wall') {
      punkte.push({ x: n.start.x, y: n.start.y }, { x: n.end.x, y: n.end.y });
    } else if (n.type === 'zone') {
      for (const p of n.polygon) punkte.push({ x: p.x, y: p.y });
    } else if (n.type === 'route') {
      for (const p of n.points) punkte.push({ x: p.x, y: p.y });
    } else if (n.type === 'object') {
      punkte.push({ x: n.transform.position.x, y: n.transform.position.y });
    }
  }
  return punkte;
}

const begrenze = (wert: number, min: number, max: number): number => Math.min(max, Math.max(min, wert));

/**
 * Maßstab und Lage, die den ganzen Grundriss ins Bild rücken.
 *
 * @param punkte  Punkte des **aktiven Geschosses** (leer ⇒ Standardansicht)
 * @param breite  die Fläche, die 2D **wirklich** hat — in der Split-Ansicht die halbe Fensterbreite
 * @param hoehe   Bühnenhöhe in px
 * @param rand    Rand in px
 *
 * **Kanten, die hier bewusst beantwortet sind:**
 * - **Leer** ⇒ Standardmaßstab und Standardlage. Kein Sprung, kein Fehler, keine Division durch Null.
 * - **Nullfläche** (alle Punkte auf einer Linie oder ein einzelner Punkt) ⇒ die Achse ohne
 *   Ausdehnung stellt **keine** Forderung; bleibt gar keine Forderung übrig, gilt der Standardmaßstab.
 *   Nirgends entsteht `Infinity` oder `NaN`.
 * - **Die Grenzen gewinnen.** Passt es in `0,02 … 1` nicht ganz hinein, steht der Maßstab **auf**
 *   der Grenze. Die Grenze weicht nicht auf, damit ein Bild vollständig wird.
 */
export function einpassen(
  punkte: ReadonlyArray<Punkt>,
  breite: number,
  hoehe: number,
  rand: number = EINPASS_RAND,
): Einpassung {
  const b = bbox(punkte);
  if (!b) {
    return { zoom: ZOOM_STANDARD, pan: standardPan(hoehe) };
  }

  // Nutzfläche: mindestens 1 px, damit eine absurd kleine Bühne keinen Maßstab 0 erzwingt.
  const nutzBreite = Math.max(1, breite - 2 * rand);
  const nutzHoehe = Math.max(1, hoehe - 2 * rand);

  const bBreite = b.maxX - b.minX;
  const bHoehe = b.maxY - b.minY;

  // Eine Achse ohne Ausdehnung stellt keine Forderung — sie darf den Maßstab nicht auf Unendlich
  // treiben und ihn auch nicht auf 0 drücken.
  const forderungen: number[] = [];
  if (bBreite > 0) forderungen.push(nutzBreite / bBreite);
  if (bHoehe > 0) forderungen.push(nutzHoehe / bHoehe);
  const roh = forderungen.length > 0 ? Math.min(...forderungen) : ZOOM_STANDARD;

  const zoom = begrenze(roh, ZOOM_MIN, ZOOM_MAX);

  // Mitte der Box auf die Mitte der Bühne legen — mit dem Vorzeichen der Bühne, nicht gegen es.
  const mitteX = (b.minX + b.maxX) / 2;
  const mitteY = (b.minY + b.maxY) / 2;
  return {
    zoom,
    pan: { x: breite / 2 - mitteX * zoom, y: hoehe / 2 + mitteY * zoom },
  };
}

/**
 * Wo ein Weltpunkt bei gegebener Einpassung auf dem Schirm liegt.
 *
 * Steht hier, damit der Test **nachrechnen** kann, statt der Einpassung zu glauben: „jeder Knoten
 * liegt im sichtbaren Bereich" ist eine Aussage über Schirmkoordinaten, und ohne diese Umrechnung
 * wäre sie nur behauptet.
 */
export function aufSchirm(p: Punkt, e: Einpassung): Punkt {
  return { x: e.pan.x + p.x * e.zoom, y: e.pan.y - p.y * e.zoom };
}
