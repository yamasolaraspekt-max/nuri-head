/**
 * Hausplaner — Wand-Geometrie (P0). Reine Funktionen, mm-Integer-Welt.
 * Azimut-Konvention (Spec ▲K2, dokumentiert): Nord = +y; Azimut = Richtung der
 * Wand-NORMALEN, im Uhrzeigersinn von Nord, ganzzahlig 0–359.
 */

export interface Punkt {
  x: number;
  y: number;
}

/** Wandlänge in mm (Gleitkomma für Vergleiche; Persistenz bleibt ganzzahlig). */
export function wandLaenge(start: Punkt, end: Punkt): number {
  return Math.hypot(end.x - start.x, end.y - start.y);
}

/** Punkt auf der Wandachse bei offset (mm) ab start — ganzzahlig gerundet. */
export function punktAufWand(start: Punkt, end: Punkt, offset: number): Punkt {
  const laenge = wandLaenge(start, end);
  if (laenge === 0) {
    return { x: start.x, y: start.y };
  }
  const t = offset / laenge;

  return {
    x: Math.round(start.x + (end.x - start.x) * t),
    y: Math.round(start.y + (end.y - start.y) * t),
  };
}

/**
 * Azimut der Wand-Normalen (Grad, 0–359, ganzzahlig). `seite` wählt die Normale:
 * 'links' = 90° gegen den Uhrzeigersinn zur Laufrichtung start→end, 'rechts' = im Uhrzeigersinn.
 * Beispiele (seite='links'): Wand West→Ost (+x) ⇒ Normale +y ⇒ 0° (Nord);
 * Wand Süd→Nord (+y) ⇒ Normale −x ⇒ 270° (West).
 */
export function azimutDerNormalen(start: Punkt, end: Punkt, seite: 'links' | 'rechts'): number {
  const dx = end.x - start.x;
  const dy = end.y - start.y;
  // Normale: links = (-dy, dx) gedreht… in Bildschirm-Mathematik mit y nach OBEN (Nord=+y):
  // Laufrichtung (dx,dy); linke Normale = (-dy, dx); rechte = (dy, -dx).
  const nx = seite === 'links' ? -dy : dy;
  const ny = seite === 'links' ? dx : -dx;

  // Azimut im Uhrzeigersinn von Nord (+y): atan2(ost-Anteil, nord-Anteil).
  const rad = Math.atan2(nx, ny);
  const grad = Math.round((rad * 180) / Math.PI);

  return ((grad % 360) + 360) % 360;
}

/** Prüft die mm-Invariante eines Punkts (ganzzahlig). */
export function istGanzzahlig(p: Punkt): boolean {
  return Number.isInteger(p.x) && Number.isInteger(p.y);
}

// ---------------------------------------------------------------------------
// Wandbänder mit Gehrung (P2b-2)
//
// Jede Wand ist ein gefülltes Rechteck (Band) der Breite = thickness um ihre
// Achse. Wo GENAU ZWEI Wände einen Endpunkt teilen, treffen sich die Bänder auf
// Gehrung (mitered): die beiden Bandkanten werden bis zum Schnittpunkt verlängert,
// sodass die Ecke sauber schließt (keine stumpfe Überlappung, kein Loch).
// Freie Enden und T-/Kreuz-Stöße (≥3 Wände) bleiben bewusst stumpf (kein Rätselraten).
// Beide beteiligten Wände berechnen an einer Ecke DENSELBEN Gehrungspunkt — die
// Bänder teilen sich also exakt dieselbe Diagonale (eine Wahrheit).
// ---------------------------------------------------------------------------

/** Minimal-Kontrakt einer Wand für die Bandberechnung (nur Geometrie + Dicke). */
export interface WandEingabe {
  id: string;
  start: Punkt;
  end: Punkt;
  thickness: number;
}

/** Ein Wandband = geschlossenes Polygon (4 Ecken) zum Füllen im 2D-Renderer. */
export interface WandBand {
  id: string;
  /** Ecken in Reihenfolge startLinks → endLinks → endRechts → startRechts (geschlossen). */
  ecken: [Punkt, Punkt, Punkt, Punkt];
}

const EPS = 1e-6;

function einheit(vx: number, vy: number): { x: number; y: number } | null {
  const len = Math.hypot(vx, vy);
  if (len < EPS) {
    return null;
  }
  return { x: vx / len, y: vy / len };
}

/** Linke Normale (90° gegen den Uhrzeigersinn bei y-nach-oben): (x,y) → (-y, x). */
function perpLinks(v: { x: number; y: number }): { x: number; y: number } {
  return { x: -v.y, y: v.x };
}

function schluessel(p: Punkt): string {
  return `${Math.round(p.x)},${Math.round(p.y)}`;
}

/**
 * Gehrungs-Eckpunkte an einem Scheitel V, an dem zwei Wände mit den (Einheits-)
 * Weg-Richtungen p und q (jeweils VOM Scheitel WEG zeigend) zusammentreffen.
 * Liefert die beiden Schnittpunkte der Bandkanten (Halbdicke h) oder null, wenn
 * die Gehrung entartet (Wände ~kollinear/entgegengesetzt) oder die Spitze zu lang
 * würde (Miter-Limit) — dann fällt der Aufrufer auf den stumpfen Abschluss zurück.
 */
function gehrungsEcken(
  V: Punkt,
  p: { x: number; y: number },
  q: { x: number; y: number },
  h: number,
): [Punkt, Punkt] | null {
  const t = einheit(p.x + q.x, p.y + q.y);
  if (!t) {
    return null; // Wände zeigen exakt entgegengesetzt (180°) — keine definierte Gehrung.
  }
  // Die Gehrungsnaht liegt AUF der Winkelhalbierenden t (nicht senkrecht dazu): sie verbindet
  // Innen- und Außeneck. So bleibt der Außenwinkel ein sauberer scharfer Eck (z. B. 90°),
  // statt abgeschrägt zu werden. innen = V + t·len, außen = V − t·len.
  const cosHalb = t.x * p.x + t.y * p.y; // cos(halber Öffnungswinkel), t·p == t·q
  const sinHalb = Math.sqrt(Math.max(0, 1 - cosHalb * cosHalb));
  if (sinHalb < EPS) {
    return null; // kollinear/entartet.
  }
  const len = h / sinHalb;
  if (len > h * 8) {
    return null; // Miter-Limit: bei sehr spitzem Winkel keine überlange Spitze.
  }
  return [
    { x: V.x + t.x * len, y: V.y + t.y * len },
    { x: V.x - t.x * len, y: V.y - t.y * len },
  ];
}

function runde(p: { x: number; y: number }): Punkt {
  return { x: Math.round(p.x), y: Math.round(p.y) };
}

function abstandQ(a: { x: number; y: number }, b: { x: number; y: number }): number {
  const dx = a.x - b.x;
  const dy = a.y - b.y;
  return dx * dx + dy * dy;
}

/**
 * Wandbänder (gefüllte Polygone mit Gehrung) für einen Satz Wände.
 * Reihenfolge des Ergebnisses = Reihenfolge der Eingabe. Entartete Wände
 * (Länge 0) werden übersprungen.
 */
export function wandBaender(waende: ReadonlyArray<WandEingabe>): WandBand[] {
  // Endpunkt-Index: Schlüssel → Liste der dort andockenden Wand-Enden mit Weg-Richtung.
  const anEndpunkt = new Map<string, Array<{ id: string; weg: { x: number; y: number } }>>();

  for (const w of waende) {
    const dir = einheit(w.end.x - w.start.x, w.end.y - w.start.y);
    if (!dir) {
      continue; // Länge 0 — kein Band.
    }
    // Am Start zeigt die Wand mit +dir weg, am Ende mit −dir.
    for (const [pkt, weg] of [
      [w.start, dir],
      [w.end, { x: -dir.x, y: -dir.y }],
    ] as const) {
      const key = schluessel(pkt);
      const liste = anEndpunkt.get(key) ?? [];
      liste.push({ id: w.id, weg });
      anEndpunkt.set(key, liste);
    }
  }

  const baender: WandBand[] = [];

  for (const w of waende) {
    const dir = einheit(w.end.x - w.start.x, w.end.y - w.start.y);
    if (!dir) {
      continue;
    }
    const h = w.thickness / 2;
    const uL = perpLinks(dir); // Einheits-Linksnormale der Wand (konstant über die Wand).

    // Stumpfe (quadratische) Default-Ecken.
    const startLinks = { x: w.start.x + h * uL.x, y: w.start.y + h * uL.y };
    const startRechts = { x: w.start.x - h * uL.x, y: w.start.y - h * uL.y };
    const endLinks = { x: w.end.x + h * uL.x, y: w.end.y + h * uL.y };
    const endRechts = { x: w.end.x - h * uL.x, y: w.end.y - h * uL.y };

    const ecken = {
      startLinks: startLinks as { x: number; y: number },
      startRechts: startRechts as { x: number; y: number },
      endLinks: endLinks as { x: number; y: number },
      endRechts: endRechts as { x: number; y: number },
    };

    // Für jedes Wand-Ende prüfen, ob genau EINE Nachbarwand andockt → Gehrung.
    const enden: Array<{
      V: Punkt;
      weg: { x: number; y: number };
      linksSquare: { x: number; y: number };
      rechtsSquare: { x: number; y: number };
      setzeLinks: (p: Punkt) => void;
      setzeRechts: (p: Punkt) => void;
    }> = [
      {
        V: w.start,
        weg: dir,
        linksSquare: startLinks,
        rechtsSquare: startRechts,
        setzeLinks: (p) => (ecken.startLinks = p),
        setzeRechts: (p) => (ecken.startRechts = p),
      },
      {
        V: w.end,
        weg: { x: -dir.x, y: -dir.y },
        linksSquare: endLinks,
        rechtsSquare: endRechts,
        setzeLinks: (p) => (ecken.endLinks = p),
        setzeRechts: (p) => (ecken.endRechts = p),
      },
    ];

    for (const ende of enden) {
      const key = schluessel(ende.V);
      const andere = (anEndpunkt.get(key) ?? []).filter((e) => e.id !== w.id);
      if (andere.length !== 1) {
        continue; // Freies Ende oder T-/Kreuz-Stoß → stumpf lassen.
      }
      const miter = gehrungsEcken(ende.V, ende.weg, andere[0].weg, h);
      if (!miter) {
        continue; // Entartet → stumpf.
      }
      const [M1, M2] = miter;
      // Jeden der beiden Miter-Punkte der näheren stumpfen Ecke zuordnen.
      if (abstandQ(M1, ende.linksSquare) <= abstandQ(M2, ende.linksSquare)) {
        ende.setzeLinks(M1);
        ende.setzeRechts(M2);
      } else {
        ende.setzeLinks(M2);
        ende.setzeRechts(M1);
      }
    }

    baender.push({
      id: w.id,
      ecken: [runde(ecken.startLinks), runde(ecken.endLinks), runde(ecken.endRechts), runde(ecken.startRechts)],
    });
  }

  return baender;
}

// ---------------------------------------------------------------------------
// Türblatt & Öffnungsbogen (P2b-4)
//
// Reine Geometrie im LOKALEN Öffnungs-Koordinatensystem (Ursprung = Öffnungsmitte
// auf der Wandachse; +x entlang der Wand start→end; +y = linke Wandnormale). Der
// Renderer legt die Gruppe bereits an Position/Drehung der Öffnung — hier wird nur
// gerechnet, nichts gezeichnet und nichts geschrieben.
//
// `anschlag` wählt die Angel-Seite (Türangel an linkem oder rechtem Pfosten),
// `oeffnung` die Seite, zu der das Blatt aufschlägt (innen = +y, außen = −y).
// Das Blatt ist so lang wie die lichte Öffnung; der Bogen ist die 90°-Schwenkkurve.
// ---------------------------------------------------------------------------

export type TuerAnschlag = 'links' | 'rechts';
export type TuerOeffnung = 'innen' | 'aussen';

export interface TuerBlattGeometrie {
  /** Angelpunkt (Drehpunkt des Blatts), lokal. */
  angelpunkt: Punkt;
  /** Blattspitze bei 90° geöffnet, lokal. */
  blattEnde: Punkt;
  /** Öffnungsbogen als Polylinie (geschlossene Position → offene Position). */
  bogen: Punkt[];
}

/** Winkel auf (−π, π] normalisieren. */
function normWinkel(a: number): number {
  let x = a;
  while (x <= -Math.PI) x += 2 * Math.PI;
  while (x > Math.PI) x -= 2 * Math.PI;
  return x;
}

/**
 * Türblatt-Geometrie für eine Öffnung der lichten Breite `width` (mm).
 * Defaults: anschlag='links', oeffnung='innen'. `segmente` = Bogen-Auflösung.
 */
export function tuerBlattGeometrie(
  width: number,
  anschlag: TuerAnschlag = 'links',
  oeffnung: TuerOeffnung = 'innen',
  segmente = 12,
): TuerBlattGeometrie {
  const h = width / 2;
  const angelVorzeichen = anschlag === 'links' ? -1 : 1; // Angel am linken (−x) bzw. rechten (+x) Pfosten.
  const schwenkVorzeichen = oeffnung === 'innen' ? 1 : -1; // Aufschlag zu +y (innen) bzw. −y (außen).

  const angel = { x: angelVorzeichen * h, y: 0 };
  const geschlossen = { x: -angelVorzeichen * h, y: 0 }; // gegenüberliegender Pfosten.
  const blattEnde = { x: angel.x, y: schwenkVorzeichen * width };

  const startWinkel = Math.atan2(geschlossen.y - angel.y, geschlossen.x - angel.x);
  const endWinkel = Math.atan2(blattEnde.y - angel.y, blattEnde.x - angel.x);
  const delta = normWinkel(endWinkel - startWinkel); // ±90°, kurzer Weg.

  const bogen: Punkt[] = [];
  const stufen = Math.max(2, Math.round(segmente));
  for (let k = 0; k <= stufen; k++) {
    const w = startWinkel + (delta * k) / stufen;
    bogen.push(runde({ x: angel.x + width * Math.cos(w), y: angel.y + width * Math.sin(w) }));
  }

  return { angelpunkt: runde(angel), blattEnde: runde(blattEnde), bogen };
}
