/**
 * Hausplaner — automatische Raumerkennung (P0).
 *
 * Verfahren (klassisch, planar):
 * 1. Wandachsen eines Geschosses → Kanten-Graph; Wände werden an T-Punkten GETEILT
 *    (Endpunkt einer Wand liegt exakt auf der Achse einer anderen — mm-Integer-Welt,
 *    keine Toleranz-Magie).
 * 2. Je Knoten werden die ausgehenden Halbkanten nach Winkel sortiert; der Flächen-
 *    Umlauf nimmt an jedem Knoten die im Uhrzeigersinn nächste Halbkante nach der
 *    Gegenkante — jede Halbkante gehört zu genau einer Fläche ⇒ KEINE Endlosschleife,
 *    auch nicht bei offenen Wandzügen (deren Flächen entarten und fallen unten raus).
 * 3. Innenflächen = Umläufe mit positiver Shoelace-Fläche (y nach oben, CCW);
 *    der Außenumlauf ist negativ und wird verworfen. Fläche mm², Volumen mm³.
 *
 * BEWUSSTE P0-GRENZEN (dokumentiert, kein Raten):
 * - Polygone laufen über die WANDACHSEN (Mittellinien). Der „lichte" Abzug der halben
 *   Wanddicke ist eine spätere Verfeinerung der Projektion — die Referenzwerte der
 *   Tests sind auf Achsmaß handgerechnet.
 * - Echte X-Kreuzungen (zwei Wände schneiden sich abseits aller Endpunkte) werden
 *   NICHT geteilt (Snapping führt Wände auf Endpunkte/T). Ein Fall mit X-Kreuzung
 *   erzeugt schlicht keine zusätzlichen Räume — nie falsche.
 */
import type { WallNode } from '../domain/scene.types';
import type { Punkt } from './wallGeometry';

export interface RaumKante {
  von: Punkt;
  bis: Punkt;
  wallId: string;
  /** Offset-Fenster der Kante entlang der URSPRUNGS-Wand (mm ab wall.start). */
  offsetVon: number;
  offsetBis: number;
}

export interface ErkannterRaum {
  polygon: Punkt[];
  kanten: RaumKante[];
  flaecheMm2: number;
  volumenMm3: number;
}

interface Kante {
  a: string;
  b: string;
  wallId: string;
  offsetVon: number;
  offsetBis: number;
}

const key = (p: Punkt): string => `${p.x},${p.y}`;
const parse = (k: string): Punkt => {
  const [x, y] = k.split(',').map(Number);

  return { x, y };
};

/** Liegt p exakt (mm-Integer, kollinear) auf der offenen Strecke a–b? */
function liegtAufStrecke(p: Punkt, a: Punkt, b: Punkt): boolean {
  const kreuz = (b.x - a.x) * (p.y - a.y) - (b.y - a.y) * (p.x - a.x);
  if (kreuz !== 0) {
    return false;
  }
  const skalar = (p.x - a.x) * (b.x - a.x) + (p.y - a.y) * (b.y - a.y);
  const laengeQ = (b.x - a.x) ** 2 + (b.y - a.y) ** 2;

  return skalar > 0 && skalar < laengeQ;
}

/** Shoelace: signierte Fläche (mm², y nach oben ⇒ CCW positiv). */
export function signierteFlaeche(polygon: Punkt[]): number {
  let summe = 0;
  for (let i = 0; i < polygon.length; i++) {
    const p = polygon[i];
    const q = polygon[(i + 1) % polygon.length];
    summe += p.x * q.y - q.x * p.y;
  }

  return summe / 2;
}

/** Wände eines Geschosses → Räume. hoeheMm = Geschoss-Standardhöhe (Volumen). */
export function erkenneRaeume(waende: WallNode[], hoeheMm: number): ErkannterRaum[] {
  // 1) Splitpunkte je Wand sammeln (fremde Endpunkte auf der Achse = T-Kreuzung).
  const endpunkte: Punkt[] = waende.flatMap((w) => [w.start, w.end]);
  const kanten: Kante[] = [];

  for (const wand of waende) {
    const laenge = Math.hypot(wand.end.x - wand.start.x, wand.end.y - wand.start.y);
    if (laenge === 0) {
      continue;
    }
    const offsets = new Map<number, Punkt>([[0, wand.start], [laenge, wand.end]]);
    for (const p of endpunkte) {
      if (liegtAufStrecke(p, wand.start, wand.end)) {
        offsets.set(Math.hypot(p.x - wand.start.x, p.y - wand.start.y), p);
      }
    }
    const sortiert = [...offsets.entries()].sort((l, r) => l[0] - r[0]);
    for (let i = 0; i < sortiert.length - 1; i++) {
      const [oa, pa] = sortiert[i];
      const [ob, pb] = sortiert[i + 1];
      if (key(pa) !== key(pb)) {
        kanten.push({ a: key(pa), b: key(pb), wallId: wand.id, offsetVon: oa, offsetBis: ob });
      }
    }
  }

  // 2) Halbkanten + Winkel-Rotation je Knoten.
  interface HalbKante {
    von: string;
    zu: string;
    kante: Kante;
    winkel: number;
    flaecheId: number; // -1 = unbesucht
  }
  const halbkanten: HalbKante[] = [];
  for (const k of kanten) {
    const pa = parse(k.a);
    const pb = parse(k.b);
    halbkanten.push({ von: k.a, zu: k.b, kante: k, winkel: Math.atan2(pb.y - pa.y, pb.x - pa.x), flaecheId: -1 });
    halbkanten.push({ von: k.b, zu: k.a, kante: k, winkel: Math.atan2(pa.y - pb.y, pa.x - pb.x), flaecheId: -1 });
  }
  const ausgehend = new Map<string, HalbKante[]>();
  for (const h of halbkanten) {
    if (!ausgehend.has(h.von)) {
      ausgehend.set(h.von, []);
    }
    ausgehend.get(h.von)!.push(h);
  }
  for (const liste of ausgehend.values()) {
    liste.sort((l, r) => l.winkel - r.winkel);
  }

  /** Nächste Halbkante des Umlaufs: an `h.zu` die im Uhrzeigersinn nächste NACH der Gegenkante. */
  function naechste(h: HalbKante): HalbKante {
    const liste = ausgehend.get(h.zu)!;
    const rueckWinkel = Math.atan2(parse(h.von).y - parse(h.zu).y, parse(h.von).x - parse(h.zu).x);
    // Position der Gegenkante in der Rotation suchen, dann die davor (im Uhrzeigersinn).
    let idx = liste.findIndex((k) => k.zu === h.von && k.kante === h.kante && k.winkel === rueckWinkel);
    if (idx === -1) {
      idx = liste.findIndex((k) => k.zu === h.von);
    }
    const naechsteIdx = (idx - 1 + liste.length) % liste.length;

    return liste[naechsteIdx];
  }

  // 3) Flächen-Umläufe einsammeln (jede Halbkante genau einmal ⇒ terminiert immer).
  const raeume: ErkannterRaum[] = [];
  let flaechenZaehler = 0;

  for (const start of halbkanten) {
    if (start.flaecheId !== -1) {
      continue;
    }
    const umlauf: HalbKante[] = [];
    let aktuell = start;
    while (aktuell.flaecheId === -1) {
      aktuell.flaecheId = flaechenZaehler;
      umlauf.push(aktuell);
      aktuell = naechste(aktuell);
    }
    flaechenZaehler++;

    // Umlauf → Polygon; entartete Umläufe (Stichwände: hin+zurück) haben Fläche 0.
    const polygon = umlauf.map((h) => parse(h.von));
    if (polygon.length < 3) {
      continue;
    }
    const flaeche = signierteFlaeche(polygon);
    if (flaeche <= 0) {
      continue; // Außenumlauf (negativ) oder entartet (0) — kein Raum.
    }

    raeume.push({
      polygon,
      kanten: umlauf.map((h) => ({
        von: parse(h.von),
        bis: parse(h.zu),
        wallId: h.kante.wallId,
        offsetVon: h.kante.offsetVon,
        offsetBis: h.kante.offsetBis,
      })),
      flaecheMm2: flaeche,
      volumenMm3: flaeche * hoeheMm,
    });
  }

  return raeume;
}
