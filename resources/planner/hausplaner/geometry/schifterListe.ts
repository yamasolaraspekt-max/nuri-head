/**
 * Eingabeaufforderung 28: REINE, testbare Klassifikation + Stückliste der SCHIFTSPARREN (Schifter,
 * engl. jack rafters) im 3D-Dachplaner. Hintergrund: Die Engine (DachplanerProPage.buildRoofFace)
 * zeichnet die Gemeinsparren bereits auf das Flächenpolygon GECLIPPT — an Kehlen (innenliegende
 * V-Kerbe der L/T/U-Hauptfläche) und Graten (Walm-Trapez/-Dreieck) entstehen dadurch automatisch
 * VERKÜRZTE Sparren. Diese lagen bisher pauschal als „Sparren" in der Holzliste (siehe
 * holzBauteile.ts → OFFENE_HOLZBAUTEILE: „Schiftersparren als eigene Klasse … nicht eindeutig
 * benannt"). EA28 schließt genau diese Lücke: benennen + als „davon"-Breakdown ausweisen.
 *
 * FACHREGELN (Zimmermann-/Statik-Agent + Dachdecker-Meister-Agent, verbindlich):
 *  - Schifter = Gemeinsparren, der NICHT mit rechtwinkligem Traufe-/First-Schnitt durchläuft,
 *    sondern an einer SCHRÄGEN Tragkante (Grat-/Kehlsparren bzw. bündige Anbaukante) angeschnitten
 *    („geschiftet") ist. In v-Flächenkoordinaten (v=0 Traufe, v=vMax First): voll ⇔ vStart≈0 ∧ vEnd≈vMax.
 *  - Gratschifter: oben am Grat angeschnitten (vEnd<vMax). Kehlschifter: unten an der Kehle (vStart>0).
 *    Der Grat/Kehle-SPLIT hier ist eine Clip-Ende-HEURISTIK (oben→Grat, unten→Kehle); die robuste,
 *    fachlich gesicherte Größe ist die GESAMTzahl + Σ lfm. (Bündige Flügel-/Giebelkappung wird als
 *    Schifter mitgezählt — auch sie braucht einen Schiftschnitt.)
 *  - Ein nur durch eine Dachöffnung (Fenster/Kamin) geteilter Sparren ist KEIN Schifter (das ist ein
 *    „ausgewechselter Sparren" am Wechsel). In der Engine laufen solche Stücke über den teile-Zweig,
 *    NICHT über den hier gespiegelten Polygon-Clip → sie tauchen hier gar nicht auf.
 *  - BOM: KEIN neuer lfm-Hauptposten (Doppelzählung!). Schifter sind Teilmenge der Sparren-lfm →
 *    nur „davon Schiftsparren: n Stück, min–max, Σ lfm" als mengenneutraler Breakdown.
 *
 * Rein (KEIN three/React-Import). KEINE Statik/Schneelast/Eindeckung/Querschnittsbemessung — nur
 * Lage/Länge/Anzahl. Spiegelt getLineIntersections + die Sparren-Schleife aus buildRoofFace bit-genau.
 */

export interface Punkt2D { x: number; y: number; }

export type SchifterArt = 'voll' | 'kehle' | 'grat' | 'beidseitig';

export interface SchifterSparren {
  u: number;        // Position entlang der Traufe (Flächen-u)
  vStart: number;   // unteres Ende (Traufe=0)
  vEnd: number;     // oberes Ende (First=vMax)
  laenge3D: number; // echte Sparrenlänge = vEnd − vStart (v ist bereits 3D-Schräglänge)
  art: SchifterArt;
}

export interface SchifterMengen {
  anzahl: number;
  laengeGesamt: number;   // Σ lfm der Schifter (Teilmenge der Sparren-lfm — NICHT addieren!)
  laengeMin: number;
  laengeMax: number;
  gratAnzahl: number;
  gratLaenge: number;
  kehleAnzahl: number;
  kehleLaenge: number;
}

function endlich(n: unknown): number { return typeof n === 'number' && Number.isFinite(n) ? n : NaN; }

/**
 * Klassifiziert EINEN gezeichneten Sparrenabschnitt [vStart..vEnd] auf einer Fläche der Höhe vMax.
 * tol = Toleranz (m), ab der ein Ende als „nicht an Traufe/First" (also angeschnitten) gilt.
 * Ungültige Zahlen → 'voll' (sicher: wird NICHT als Schifter gebucht, nie NaN).
 */
export function klassifiziereSchifter(vStart: number, vEnd: number, vMax: number, tol = 0.05): SchifterArt {
  const a = endlich(vStart), b = endlich(vEnd), m = endlich(vMax);
  if (!Number.isFinite(a) || !Number.isFinite(b) || !Number.isFinite(m) || m <= 0) return 'voll';
  if (b - a <= 0) return 'voll';
  const untenAngeschnitten = a > tol;          // erreicht die Traufe (v=0) nicht → Kehle
  const obenAngeschnitten = b < m - tol;        // erreicht den First (v=vMax) nicht → Grat
  if (untenAngeschnitten && obenAngeschnitten) return 'beidseitig';
  if (untenAngeschnitten) return 'kehle';
  if (obenAngeschnitten) return 'grat';
  return 'voll';
}

/** Mirror von DachplanerProPage.getLineIntersections (isUAxis=true: senkrechte Sparren-Scanlinie bei u). */
function schnittpunkteU(u: number, poly: Punkt2D[]): number[] {
  const hits: number[] = [];
  for (let i = 0; i < poly.length; i++) {
    const p1 = poly[i], p2 = poly[(i + 1) % poly.length];
    const c1 = p1.x, c2 = p2.x, o1 = p1.y, o2 = p2.y;
    if (c1 === c2) continue;
    if ((c1 <= u && u <= c2) || (c2 <= u && u <= c1)) {
      const t = (u - c1) / (c2 - c1);
      hits.push(o1 + t * (o2 - o1));
    }
  }
  hits.sort((a, b) => a - b);
  const uniq: number[] = [];
  for (let i = 0; i < hits.length; i++) if (i === 0 || Math.abs(hits[i] - hits[i - 1]) > 0.001) uniq.push(hits[i]);
  return uniq;
}

/**
 * Spiegelt die Sparren-Schleife aus buildRoofFace EXAKT (numRafters/Schrittweite/Clip/0.05-Filter) und
 * liefert je gezeichnetem Sparren seine Klassifikation. Identische Reihenfolge/Positionen wie die Engine
 * → Regressions-/Abnahmebasis (der Geometrie-Prüfer vergleicht die Engine-Holzliste hiergegen).
 * Öffnungen werden hier NICHT betrachtet (sie ändern das Polygon nicht; ihre Teilstücke sind keine Schifter).
 */
export function schifterAusFlaeche(polygon: Punkt2D[], uMax: number, vMax: number, rafterDist: number, rafterW: number, tol = 0.05): SchifterSparren[] {
  const out: SchifterSparren[] = [];
  const uM = endlich(uMax), vM = endlich(vMax), rd = endlich(rafterDist), rw = endlich(rafterW);
  if (![uM, vM, rd, rw].every(Number.isFinite) || uM <= 0 || vM <= 0 || rd <= 0 || !Array.isArray(polygon) || polygon.length < 3) return out;
  const numRafters = Math.min(2000, Math.max(1, Math.floor(uM / rd)));
  for (let i = 0; i <= numRafters; i++) {
    const u = rw / 2 + i * ((uM - rw) / Math.max(1, numRafters));
    const hits = schnittpunkteU(u, polygon);
    for (let k = 0; k < hits.length; k += 2) {
      if (hits[k + 1] !== undefined && hits[k + 1] - hits[k] > 0.05) {
        const vStart = hits[k], vEnd = hits[k + 1];
        out.push({ u, vStart, vEnd, laenge3D: vEnd - vStart, art: klassifiziereSchifter(vStart, vEnd, vM, tol) });
      }
    }
  }
  return out;
}

/** Aggregiert eine Schifter-Liste (nur art≠'voll') zur Stückliste — mit Grat/Kehle-Split. */
export function schifterMengen(liste: ReadonlyArray<SchifterSparren> | null | undefined): SchifterMengen {
  const leer: SchifterMengen = { anzahl: 0, laengeGesamt: 0, laengeMin: 0, laengeMax: 0, gratAnzahl: 0, gratLaenge: 0, kehleAnzahl: 0, kehleLaenge: 0 };
  if (!Array.isArray(liste)) return leer;
  let min = Infinity, max = 0;
  const m = { ...leer };
  for (const s of liste) {
    if (!s || s.art === 'voll') continue;
    const L = endlich(s.laenge3D);
    if (!Number.isFinite(L) || L <= 0) continue;
    m.anzahl += 1; m.laengeGesamt += L;
    if (L < min) min = L; if (L > max) max = L;
    // Grat/Kehle-Split (Clip-Ende-Heuristik); 'beidseitig' zählt zum Grat (oben angeschnitten).
    if (s.art === 'kehle') { m.kehleAnzahl += 1; m.kehleLaenge += L; }
    else { m.gratAnzahl += 1; m.gratLaenge += L; }
  }
  m.laengeMin = m.anzahl > 0 ? min : 0;
  m.laengeMax = max;
  return m;
}

/** Eintrag der Engine-Holzliste, soweit für die Schifter-Auswertung relevant. */
export interface HolzStueckRef { type?: string; name?: string; laenge?: number; schifter?: string | null; istSchifter?: boolean; }

/**
 * Aggregiert die Schifter aus der ECHTEN Engine-Holzliste (Bauteile, die buildRoofFace mit
 * istSchifter=true / schifter='kehle'|'grat'|'beidseitig' abgelegt hat). Analog holzBauteileAusListe;
 * type bleibt 'sparren' (keine Doppelzählung in holzMengen) — hier nur der „davon"-Breakdown.
 */
export function schifterMengenAusListe(holzliste: ReadonlyArray<HolzStueckRef> | null | undefined): SchifterMengen {
  const liste: SchifterSparren[] = [];
  for (const h of Array.isArray(holzliste) ? holzliste : []) {
    if (!h || h.type !== 'sparren') continue;
    const art = (h.schifter as SchifterArt) || (h.istSchifter ? 'grat' : 'voll');
    if (art === 'voll' || !h.istSchifter) continue;
    const L = endlich(h.laenge);
    if (!Number.isFinite(L) || L <= 0) continue;
    liste.push({ u: 0, vStart: 0, vEnd: L, laenge3D: L, art });
  }
  return schifterMengen(liste);
}
