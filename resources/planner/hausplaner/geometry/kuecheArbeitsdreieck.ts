/**
 * Hausplaner — Küchen-Arbeitsdreieck (Konfigurator Küche §8.6, autark).
 *
 * Reine Ergonomie-Prüfung nach DIN 18022 / gängiger Küchenergonomie: die drei Wege zwischen
 * Spüle, Kochstelle und Kühlgerät sollen je 1,2–2,7 m lang sein, die Summe 3,6–6,6 m. Keine
 * Szene-Mutation — nur Punkte → Wege → Bewertung. Für eine Küche genügt ein Raum (Autark-Prinzip).
 */

export interface Punkt { x: number; y: number } // mm

export interface Arbeitsdreieck {
  spuele: Punkt;
  kochen: Punkt;
  kuehlen: Punkt;
}

export type PruefSchwere = 'info' | 'warnung' | 'fehler';
export interface DreieckPruefung { id: string; schwere: PruefSchwere; meldung: string; bestanden: boolean; }

export interface DreieckErgebnis {
  wegSpKo: number; // Spüle↔Kochen, mm
  wegKoKu: number; // Kochen↔Kühlen, mm
  wegKuSp: number; // Kühlen↔Spüle, mm
  summe: number;   // mm
  pruefungen: DreieckPruefung[];
  bestanden: boolean;
}

const dist = (a: Punkt, b: Punkt): number => Math.round(Math.hypot(a.x - b.x, a.y - b.y));

const BEIN_MIN = 1200, BEIN_MAX = 2700, SUMME_MIN = 3600, SUMME_MAX = 6600;

export function bewerteArbeitsdreieck(d: Arbeitsdreieck): DreieckErgebnis {
  const wegSpKo = dist(d.spuele, d.kochen);
  const wegKoKu = dist(d.kochen, d.kuehlen);
  const wegKuSp = dist(d.kuehlen, d.spuele);
  const summe = wegSpKo + wegKoKu + wegKuSp;

  const p: DreieckPruefung[] = [];
  const beinPruef = (id: string, wert: number, name: string) =>
    p.push({ id, schwere: 'warnung', bestanden: wert >= BEIN_MIN && wert <= BEIN_MAX,
      meldung: `${name} ${wert} mm (Ziel ${BEIN_MIN}–${BEIN_MAX}).` });
  beinPruef('bein-sp-ko', wegSpKo, 'Weg Spüle–Kochen');
  beinPruef('bein-ko-ku', wegKoKu, 'Weg Kochen–Kühlen');
  beinPruef('bein-ku-sp', wegKuSp, 'Weg Kühlen–Spüle');
  p.push({ id: 'summe', schwere: 'fehler', bestanden: summe >= SUMME_MIN && summe <= SUMME_MAX,
    meldung: `Summe der Wege ${summe} mm (Ziel ${SUMME_MIN}–${SUMME_MAX}).` });

  return { wegSpKo, wegKoKu, wegKuSp, summe, pruefungen: p,
    bestanden: !p.some((x) => x.schwere === 'fehler' && !x.bestanden) };
}
