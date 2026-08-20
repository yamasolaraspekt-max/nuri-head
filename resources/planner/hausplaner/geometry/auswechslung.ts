/**
 * Reparatur 9: reine, testbare Geometrie für Auswechslungen/Wechselhölzer an
 * Dachöffnungen (Kamin, Dachfenster, Gaube, Lüfter) des 3D-Planers.
 *
 * Befund: Sparren werden in der Engine in lokalen Dachflächenkoordinaten platziert
 * (u = parallel Traufe, v = geneigt Traufe->First). Aufbauten (ObstacleData) liegen
 * mit relativer Position (x,y in 0..1 der Fläche) + Breite/Höhe in Metern + surfaceId
 * vor. Damit lässt sich rein geometrisch bestimmen, welche Sparren eine Öffnung
 * schneiden, und – im sicheren Fall – die Lage der Wechselhölzer ableiten.
 *
 * WICHTIG (Maßlogik): Öffnungsbreite = Maß in u-Richtung (parallel Traufe),
 * Öffnungshöhe = Maß in v-Richtung (geneigt). Die Aufbau-TIEFE (depth, Aufbauhöhe
 * über der Fläche) wird NICHT als Öffnungshöhe verwendet.
 *
 * Ehrlichkeit: Wechselhölzer werden NUR als echte Bauteile geführt, wenn die
 * angrenzenden tragenden Sparren eindeutig bestimmbar sind und die Öffnung nicht
 * in einer konstruktiven Sonderzone (First/Traufe/Ortgang) liegt. Sonst
 * 'pruefpflichtig' = true (Auswechslung statisch/geometrisch prüfen) und KEINE
 * erfundenen Mengen. Keine statische Bemessung.
 *
 * Rein (keine React-/THREE-Abhängigkeit), vollständig prüfbar.
 
 *
 * ---
 *
 * **HERKUNFTSVERMERK, nachgetragen 20.08. — dieser Kopf nennt einen Wirt, den es hier nicht gibt.**
 *
 * Die oben genannte „Engine" ist `class RoofEngine`, und sie steht **ausschliesslich** in
 * `docs/planner/pv-belegung-referenz/DachplanerProPage.tsx:369` — einer Referenzdatei unter `docs/`,
 * nicht im Produktivbaum. Gemessen: `buildFlat`, `ObstacleData` und `RoofEngine` haben in
 * `resources/` und `app/` **null Definitionen**, nur Kommentar-Nennungen.
 *
 * **Folge:** dieses Modul hat heute keinen Produktivverbraucher, und der Aufrufer, gegen den es
 * geschrieben ist, wurde nie mitgebracht. Es ist nicht „gebaut und noch nicht angeschlossen",
 * sondern **gegen ein anderes Haus gebaut**.
 *
 * **Dazu die Einheiten:** dieses Modul rechnet in **Metern** (27 Feldnamen auf `…M`/`…M2`, null auf
 * `…Mm`); die Domaene dieser Insel fuehrt **ganze Millimeter** (`domain/scene.types.ts:270`). Ein
 * Anschluss ist damit keine Verdrahtung, sondern eine Umrechnung mit Rundungsentscheidung.
 *
 * **Wer es anschliessen will, muss zuerst diesen Vermerk entkraeften** — sonst wird aus einem
 * fremden Vertrag stillschweigend ein eigener.
 */

export interface FlaecheMasse {
  /** Flächenbreite in u-Richtung (parallel Traufe), Meter. */
  breiteM: number;
  /** Flächenhöhe in v-Richtung (geneigt Traufe->First), Meter. */
  hoeheM: number;
}

export interface Oeffnung {
  /** relative u-Position des Mittelpunkts (0..1). */
  xRel: number;
  /** relative v-Position des Mittelpunkts (0..1). */
  yRel: number;
  /** Öffnungsbreite in u-Richtung (Meter) — NICHT die Aufbau-Tiefe. */
  breiteM: number;
  /** Öffnungshöhe in v-Richtung (Meter) — NICHT die Aufbau-Tiefe. */
  hoeheM: number;
}

export interface AuswechslungAnalyse {
  /** Anzahl der von der Öffnung (inkl. Sicherheitsrand) geschnittenen Sparren. */
  betroffeneSparren: number;
  /** true, wenn mehr als ein Sparrenfeld betroffen ist. */
  spanntMehrereFelder: boolean;
  /** true, wenn die Öffnung nahe First/Traufe/Ortgang liegt. */
  naheRandzone: boolean;
  randzonen: string[];
  /** true, wenn überhaupt ein Sparren geschnitten wird (Auswechslung nötig). */
  wechselErforderlich: boolean;
  /** true, wenn die Auswechslung NICHT sicher geometrisch ableitbar ist. */
  pruefpflichtig: boolean;
  /** Anzahl sicher ableitbarer Wechselhölzer (0 oder 2 = oben+unten). */
  wechselAnzahl: number;
  /** Länge je Wechselholz in u-Richtung (Meter, Spannweite zwischen tragenden Sparren). */
  wechselLaengeM: number;
  hinweise: string[];
}

function endlich(v: unknown, fallback = 0): number {
  return typeof v === "number" && Number.isFinite(v) ? v : fallback;
}

/**
 * Sparren-u-Positionen einer rechteckigen Flächenbreite — identisch zur Engine
 * (u = rafterW/2 + i*((breite - rafterW)/numRafters), i=0..numRafters).
 */
export function sparrenPositionenU(breiteM: number, rafterDistM: number, rafterWidthM = 0.08): number[] {
  const b = endlich(breiteM);
  const dist = Math.max(0.05, endlich(rafterDistM, 0.7));
  const rw = Math.max(0.02, endlich(rafterWidthM, 0.08));
  if (b <= 0) return [];
  const numRafters = Math.min(2000, Math.max(1, Math.floor(b / dist)));
  const positionen: number[] = [];
  for (let i = 0; i <= numRafters; i++) {
    positionen.push(rw / 2 + i * ((b - rw) / numRafters));
  }
  return positionen;
}

/**
 * Analysiert eine Öffnung gegen das Sparrenraster der Fläche.
 * Liefert betroffene Sparren, Randzonen-/Mehrfeld-Status und – nur im sicheren
 * Fall – die ableitbaren Wechselholz-Längen. Niemals NaN/Infinity/negativ.
 */
export function analysiereAuswechslung(
  flaeche: FlaecheMasse,
  oeffnung: Oeffnung,
  rafterDistM: number,
  opts?: { rafterWidthM?: number; sicherheitsrandM?: number; randThresholdM?: number },
): AuswechslungAnalyse {
  const breite = endlich(flaeche?.breiteM);
  const hoehe = endlich(flaeche?.hoeheM);
  const rand = Math.max(0, endlich(opts?.sicherheitsrandM, 0.05));
  const randThreshold = Math.max(0, endlich(opts?.randThresholdM, 0.3));
  const hinweise: string[] = [];
  const leer: AuswechslungAnalyse = {
    betroffeneSparren: 0, spanntMehrereFelder: false, naheRandzone: false, randzonen: [],
    wechselErforderlich: false, pruefpflichtig: false, wechselAnzahl: 0, wechselLaengeM: 0, hinweise,
  };

  if (breite <= 0 || hoehe <= 0) {
    return leer; // keine gültige Fläche -> nichts ableiten
  }

  const uMitte = endlich(oeffnung?.xRel) * breite;
  const vMitte = endlich(oeffnung?.yRel) * hoehe;
  const halbB = Math.max(0, endlich(oeffnung?.breiteM)) / 2;
  const halbH = Math.max(0, endlich(oeffnung?.hoeheM)) / 2;
  if (halbB <= 0 || halbH <= 0) return leer; // keine gültige Öffnung

  const uMin = uMitte - halbB - rand;
  const uMax = uMitte + halbB + rand;
  const vMin = vMitte - halbH - rand;
  const vMax = vMitte + halbH + rand;

  // Randzonen prüfen (First v~hoehe, Traufe v~0, Ortgang u~0/breite)
  const randzonen: string[] = [];
  if (vMin <= randThreshold) randzonen.push("Traufe");
  if (vMax >= hoehe - randThreshold) randzonen.push("First");
  if (uMin <= randThreshold) randzonen.push("Ortgang links");
  if (uMax >= breite - randThreshold) randzonen.push("Ortgang rechts");
  const naheRandzone = randzonen.length > 0;

  // teilweise außerhalb der Fläche?
  const teilweiseAussen = uMin < 0 || uMax > breite || vMin < 0 || vMax > hoehe;

  // Sparren, die die Öffnung (u-Bereich) schneiden
  const sparrenU = sparrenPositionenU(breite, rafterDistM, opts?.rafterWidthM);
  const betroffen = sparrenU.filter((u) => u >= uMin && u <= uMax);
  const betroffeneSparren = betroffen.length;
  const wechselErforderlich = betroffeneSparren >= 1;
  const spanntMehrereFelder = betroffeneSparren > 1;

  if (!wechselErforderlich) {
    // Öffnung liegt zwischen zwei Sparren, schneidet keinen -> keine Auswechslung nötig
    hinweise.push("Öffnung liegt zwischen den Sparren — keine Auswechslung erforderlich.");
    return { ...leer, randzonen, naheRandzone };
  }

  // flankierende tragende Sparren (eindeutig links + rechts der Öffnung)?
  const linksKandidaten = sparrenU.filter((u) => u < uMin);
  const rechtsKandidaten = sparrenU.filter((u) => u > uMax);
  const uLinks = linksKandidaten.length ? Math.max(...linksKandidaten) : null;
  const uRechts = rechtsKandidaten.length ? Math.min(...rechtsKandidaten) : null;
  const flankenBestimmbar = uLinks !== null && uRechts !== null;

  // sicher ableitbar nur ohne Sonderzone, ohne Außenlage, mit beidseitigen Flanken
  const sicher = flankenBestimmbar && !naheRandzone && !teilweiseAussen;

  hinweise.push(`Öffnung schneidet ${betroffeneSparren} Sparren — Auswechslung erforderlich.`);
  if (spanntMehrereFelder) hinweise.push("Öffnung betrifft mehrere Sparrenfelder — Auswechslung statisch prüfen.");
  if (naheRandzone) hinweise.push(`Aufbau liegt nahe ${randzonen.join(" / ")} — Anschluss und Tragwerk prüfen.`);
  if (teilweiseAussen) hinweise.push("Öffnung liegt teilweise außerhalb der Dachfläche — prüfpflichtig.");
  if (!flankenBestimmbar) hinweise.push("Angrenzende tragende Sparren nicht eindeutig — Wechsel geometrisch noch nicht vollständig ableitbar.");

  if (sicher) {
    const wechselLaengeM = Math.max(0, (uRechts as number) - (uLinks as number));
    hinweise.push("Wechselhölzer (oben/unten) geometrisch ermittelt — statisch prüfen.");
    return {
      betroffeneSparren, spanntMehrereFelder, naheRandzone, randzonen,
      wechselErforderlich: true, pruefpflichtig: false,
      wechselAnzahl: 2, wechselLaengeM, hinweise,
    };
  }

  // unsicher -> kein echtes Wechselholz, nur prüfpflichtig
  return {
    betroffeneSparren, spanntMehrereFelder, naheRandzone, randzonen,
    wechselErforderlich: true, pruefpflichtig: true,
    wechselAnzahl: 0, wechselLaengeM: 0, hinweise,
  };
}
