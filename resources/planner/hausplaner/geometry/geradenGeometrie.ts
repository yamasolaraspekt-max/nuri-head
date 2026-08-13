/**
 * A-32 — **reine Geradenmathematik.** Schnittpunkt zweier Geraden (F-004) und Parallelversatz
 * einer Achse (F-020, Normalform).
 *
 * ---
 *
 * **Warum es diese Datei gibt.** Dieselbe Rechnung sitzt heute **zweimal im Verbraucher eingebaut**
 * statt in `geometry/` zu liegen: die Normale in `commands/applyCommand.ts` (in
 * `treppenDurchbrueche`) und der Gehrungs-Schnittpunkt in `wallGeometry.ts:110` — *nicht
 * exportiert.* Beide Male fehlt sie dem nächsten Verbraucher. `wallGeometry.ts` ist
 * **wand**-spezifisch, `editierGeometrie.ts` sind **Bearbeitungs**-Operationen; eine Datei für
 * reine Geradenmathematik gab es nicht.
 *
 * **Rein:** keine React-, Konva- oder three-Abhängigkeit, kein Modellbefehl, kein Schema.
 *
 * ## Die Achsenkonvention, an der „links" hängt — gemessen, nicht angenommen
 *
 * Im **Modell** zeigt `y` nach **oben**. *Wörtlich aus `app/rahmen/Buehne.tsx:135-136`: „die Stage
 * dreht mit `scaleY={-zoom}` auf **CAD-Konvention Y-hoch**".* **Der Bildschirm dreht — das Modell
 * nicht.** *Ohne diesen Satz baut der nächste Verbraucher den Versatz spiegelverkehrt, und es fällt
 * erst an der gezeichneten Wand auf.*
 */
import type { Punkt } from './wallGeometry';

/**
 * **Schwelle für den Grenzfall aus F-004 — auf den NORMALISIERTEN Wert, nicht auf `m` selbst.**
 *
 * *`m` ist ein Kreuzprodukt zweier Differenzvektoren, seine Einheit ist **mm²**.* Eine absolute
 * Schwelle darauf wäre **längenabhängig**: zwei Achsen von je 10 m bei 1° Zwischenwinkel ergeben
 * `m ≈ 1.745.241`, zwei von je 100 mm bei **demselben** Winkel ≈ `175`. *Dieselbe Winkellage wäre
 * bei kurzen Achsen „parallel" und bei langen nicht.*
 *
 * Geteilt durch das Produkt der beiden Längen ist `m` der **Sinus des Zwischenwinkels** —
 * dimensionslos und längenunabhängig. **Der Wert ist nicht abgeschrieben, sondern übernommen mit
 * Grund:** `geometry/wallGeometry.ts:84` führt `EPS = 1e-6` und wendet es dort ebenfalls auf einen
 * **Sinus** an (`sinHalb` in der Gehrung). *F-001s ε = 0,5 mm passt nicht — das ist eine Länge.*
 */
const EPS_SINUS = 1e-6;

/** Kreuzprodukt zweier 2D-Vektoren (z-Komponente). */
function kreuz(ux: number, uy: number, vx: number, vy: number): number {
  return ux * vy - uy * vx;
}

/**
 * **F-004 · Schnittpunkt zweier Geraden.** Gerade 1 durch `a`,`b`, Gerade 2 durch `c`,`d`.
 *
 * Liefert den Schnittpunkt der **unbegrenzten Geraden** — nicht den der Strecken. *Genau das
 * braucht `trimmen`: dort berühren sich die Wände gerade nicht.*
 *
 * **Rückgabe `null`** bei parallel, deckungsgleich oder einer Achse der Länge 0. **Nie `NaN`, nie
 * `Infinity`, nie ein Wurf** — ein Aufrufer muss nicht selbst darauf prüfen.
 *
 * ---
 *
 * ## BEFUND: die Formelzeile in F-004 verfehlt ihre eigene Ausgabe (generator, 13.08.)
 *
 * `FORMELSAMMLUNG.md:75-87` schreibt den Zähler als
 *
 * ```text
 * n = (Ax−Cx)(Dy−Cy) − (Ay−Cy)(Dx−Cx)      also  (A−C) × s
 * ```
 *
 * **Richtig ist `(C−A) × s`** — das Vorzeichen ist vertauscht, und damit liegt der berechnete Punkt
 * **auf der falschen Seite von `A`**. *An zwei unabhängigen Mustern gemessen, bevor gebaut wurde:*
 *
 * ```text
 * woertlich gerechnet    A(0,0) B(10,0) x C(5,-5) D(5,5)  -> S=(-5,0)   statt (5,0)
 *                        A(0,0) B(4,0)  x C(1,-1) D(1,3)  -> S=(-1,0)   statt (1,0)
 *                        A(0,0) B(2,2)  x C(0,4)  D(4,0)  -> S=(-2,-2)  statt (2,2)
 * unabhaengig geloest    dasselbe System ueber Cramer, vier Faelle:
 *                        t_geloest + t_Sammlung == 0.000000000000 in JEDEM Fall
 * ```
 *
 * *Kein Rundungseffekt und keine andere Konvention:* **`S = A + t·(B−A)` legt die Bedeutung von `t`
 * eindeutig fest**, und F-004s eigener Zweck und ihre eigene Ausgabe sagen „Schnittpunkt S".
 *
 * **Hier steht der Zähler deshalb richtig.** *Die Formelsammlung ist NICHT angefasst — sie gehört
 * nicht zum Scope von A-32, und ihre Berichtigung ist ein eigener Vorgang.* **Beide Fassungen
 * stehen hier, damit die nächste Umsetzung die falsche nicht wieder abschreibt.**
 */
export function geradenSchnitt(a: Punkt, b: Punkt, c: Punkt, d: Punkt): Punkt | null {
  const rx = b.x - a.x;
  const ry = b.y - a.y;
  const sx = d.x - c.x;
  const sy = d.y - c.y;

  const laengeR = Math.hypot(rx, ry);
  const laengeS = Math.hypot(sx, sy);
  // Eine Achse ohne Länge hat keine Richtung — es gibt keine Gerade, die sie beschreibt.
  if (laengeR < EPS_SINUS || laengeS < EPS_SINUS) {
    return null;
  }

  const m = kreuz(rx, ry, sx, sy);
  // DER GRENZFALL AUS F-004, normalisiert: |m| / (|r|·|s|) IST der Sinus des Zwischenwinkels.
  if (Math.abs(m) / (laengeR * laengeS) < EPS_SINUS) {
    return null; // parallel oder deckungsgleich
  }

  // BERICHTIGT gegenüber FORMELSAMMLUNG.md:80 — dort steht (A−C) × s, siehe Befund oben.
  const n = kreuz(c.x - a.x, c.y - a.y, sx, sy);
  const t = n / m;

  return { x: a.x + t * rx, y: a.y + t * ry };
}

/**
 * Auf welche Seite versetzt wird — **benannt und nicht als Vorzeichen**, damit der nächste
 * Verbraucher nicht spiegelverkehrt baut.
 *
 * *Bezug ist die **Laufrichtung** `start → ende`, nicht eine Weltachse:* `links` liegt links,
 * wenn man von `start` nach `ende` blickt. **Im Modell mit `y` nach oben** (`Buehne.tsx:135-136`,
 * CAD-Konvention) ist das die Drehung der Richtung um **+90°**, also die Normale `(−dy, dx)`.
 *
 * ```text
 * Achse waagrecht nach RECHTS   (dx>0, dy=0)   links = nach OBEN   (+y)
 * Achse waagrecht nach LINKS    (dx<0, dy=0)   links = nach UNTEN  (−y)
 * Achse senkrecht nach OBEN     (dx=0, dy>0)   links = nach LINKS  (−x)
 * ```
 */
export type Versatzseite = 'links' | 'rechts';

/** Die versetzte Achse — dieselbe Richtung, um `abstandMm` zur Seite geschoben. */
export interface VersetzteAchse {
  start: Punkt;
  ende: Punkt;
}

/**
 * **Parallelversatz einer Achse — F-020s Normalform** (`FORMELSAMMLUNG.md:141-143`).
 *
 * ```text
 * Kante als Gerade:  a·x + b·y + c = 0    mit a² + b² = 1
 * Versetzte Kante:   a·x + b·y + c − t = 0
 * ```
 *
 * *Mit `a² + b² = 1` ist `a·x + b·y + c` der **vorzeichenbehaftete Abstand** eines Punktes von der
 * Geraden, gemessen entlang der Normalen `(a, b)`.* **Die versetzte Gerade ist damit die
 * Punktmenge mit Abstand `t`** — also die um `t` entlang `(a, b)` geschobene Gerade. Gebaut ist
 * genau das: jeder Endpunkt wandert um `abstandMm` entlang der Einheitsnormalen der gewählten
 * Seite.
 *
 * **KEINE neue F-Nummer.** *`FORMELSAMMLUNG.md:4` sagt „Eine Formel steht genau einmal", und diese
 * steht in F-020. F-020 versetzt **alle** Kanten eines Polygons nach innen, hier wird **eine**
 * Gerade auf eine benannte Seite versetzt — dieselbe Mathematik, anderer Zweck.*
 *
 * **Kein Widerspruch zu melden:** *beim Rechnen ist es dieselbe Formel geblieben. Die Normalform
 * mit `a²+b²=1` ist genau die Einheitsnormale, und `−t` ist genau die Verschiebung um `t` entlang
 * ihr — die zwei laufen an keiner Stelle auseinander.*
 *
 * Rückgabe `null` bei einer Achse der Länge 0: **ohne Richtung gibt es keine Seite.** Ein negativer
 * `abstandMm` ist zulässig und versetzt zur Gegenseite — die benannte Seite bleibt der Bezug.
 */
export function parallelVersatz(
  start: Punkt,
  ende: Punkt,
  abstandMm: number,
  seite: Versatzseite,
): VersetzteAchse | null {
  const dx = ende.x - start.x;
  const dy = ende.y - start.y;
  const laenge = Math.hypot(dx, dy);
  if (laenge < EPS_SINUS || !Number.isFinite(abstandMm)) {
    return null;
  }

  // Einheitsnormale (a, b) mit a² + b² = 1 — die +90°-Drehung der Laufrichtung ist LINKS.
  const a = -dy / laenge;
  const b = dx / laenge;
  const t = seite === 'links' ? abstandMm : -abstandMm;

  return {
    start: { x: start.x + a * t, y: start.y + b * t },
    ende: { x: ende.x + a * t, y: ende.y + b * t },
  };
}
