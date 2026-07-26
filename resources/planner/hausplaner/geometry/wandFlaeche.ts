/**
 * AUF-77 — **Wandfläche brutto und netto** (Mengenermittlung M1).
 *
 * Der Befund, der diesen Posten auslöst: `grep` auf `wandflaeche`/`nettoflaeche`/`bruttoflaeche`
 * über die ganze Insel ergab **keinen Treffer**. **Die Öffnungen liegen im Modell — aber niemand
 * zieht sie von einer Wandfläche ab, weil es keine Wandfläche gibt.** Auf dieser einen fehlenden
 * Rechnung setzen Putz, Dämmung, Anstrich, Fassade und Heizlast alle auf.
 *
 * **Rein: keine DOM-, Store- oder Befehls-Abhängigkeit.** Dieselbe Eingabe ergibt dasselbe Ergebnis.
 *
 * ## Die zwei Eigenschaften, die dieses Modul trägt
 *
 * **1. Kein Ergebnis ohne Bezugsmaß.** `WandMengen.bezug` ist Pflicht. Ein Feldbündel ohne die
 * Angabe `roh`/`fertig` lässt sich nicht bauen — das ist ein **Typfehler**, keine Nachlässigkeit,
 * und genau so ist es im Test mit einem echten Compiler-Lauf belegt. *Eine Fläche ohne Bezugsmaß
 * ist keine Fläche, sondern eine Zahl, die zu allem passt und für nichts taugt.*
 *
 * **2. Ein Zweifelsfall liefert eine Meldung, keine Zahl.** Ragt eine Öffnung über die Wand hinaus
 * oder überlappen zwei, gibt es **kein** Ergebnis. *Eine stillschweigend gekürzte Öffnung erzeugt
 * eine plausible falsche Zahl — und plausibel falsch ist schlimmer als offensichtlich fehlend.*
 *
 * ## Was hier NICHT passiert
 *
 * - **Keine Übermessung.** Der volle Abzug ist eindeutig und hängt von keiner Gewerkeregel ab; die
 *   Übermessung ist eine Regel **auf** diese Zahlen und gehört nach M2.
 * - **Keine Aggregation** über Raum, Geschoss oder Gebäude (M3), keine Materialmenge (M5).
 * - **Keine zweite Flächenengine.** `polygonFlaecheM2` und die Raumerkennung bleiben, was sie sind.
 * - **Keine Laibungen.** Sie sind in der Vorlage eine eigene Größe und gehören nicht heimlich in
 *   den Abzug (im Bericht benannt).
 */
import type { WallNode, OpeningNode } from '../domain/scene.types';

/**
 * **Roh** = die Maße, wie sie im Knoten stehen. **Fertig** = nach Abzug der Schichten (AUF-76).
 *
 * Es gibt bewusst keinen dritten Wert und keinen Standardwert: wer rechnet, muss sagen, worauf.
 */
export type Bezugsmass = 'roh' | 'fertig';

/**
 * Ein Mengenbündel **einer** Wand.
 *
 * **`bezug` steht bewusst an erster Stelle und ist nicht optional.** Jedes Feld darunter gilt nur
 * unter dieser Angabe.
 */
export interface WandMengen {
  /** Worauf sich alle Zahlen dieses Bündels beziehen. **Pflicht.** */
  readonly bezug: Bezugsmass;
  /** Rückverfolgbarkeit: auf welche Wand sich das Bündel bezieht (Auftrag §7). */
  readonly nodeId: string;

  readonly laengeMm: number;
  readonly hoeheMm: number;
  readonly dickeMm: number;

  /** Länge × Höhe, **je Seite**. */
  readonly bruttoM2: number;
  /** Σ (Breite × Höhe) der Öffnungen dieser Wand. */
  readonly oeffnungenM2: number;
  /** Brutto − Öffnungen, **voller Abzug**. */
  readonly nettoM2: number;

  readonly bruttoM3: number;
  readonly oeffnungenM3: number;
  readonly nettoM3: number;

  /**
   * **Welche Maße trotz `bezug: 'fertig'` das Rohmaß sind — und warum.**
   *
   * Bei `roh` leer. Bei `fertig` steht hier, was **nicht** umgerechnet werden konnte, statt es
   * stillschweigend als fertig auszugeben. *Ein fehlender Wert darf nie zu einer geschätzten Zahl
   * werden.*
   */
  readonly rohmassRest: readonly string[];
}

export type MeldungArt =
  | 'oeffnung-ragt-hinaus'
  | 'oeffnungen-ueberlappen'
  | 'oeffnung-hoeher-als-wand'
  | 'schichten-dicker-als-wand'
  | 'fremde-oeffnung';

export interface Meldung {
  readonly art: MeldungArt;
  /** Klartext mit den beteiligten Kennungen — eine Meldung ohne Bezug ist keine Meldung. */
  readonly text: string;
}

/**
 * Entweder Mengen **oder** Meldungen — nie beides und nie halb.
 *
 * Ein Ergebnistyp, der Zahlen und Zweifel gleichzeitig zulässt, wird an der ersten Aufrufstelle
 * halb ausgewertet: die Zahlen nimmt man, die Meldungen übersieht man.
 */
export type WandFlaecheErgebnis =
  | { readonly art: 'mengen'; readonly mengen: WandMengen }
  | { readonly art: 'meldung'; readonly meldungen: readonly Meldung[] };

/**
 * **Die einzige Rundungsstelle des Moduls.**
 *
 * *Zwei Rundungsorte ergeben zwei Summen, die sich um Cents unterscheiden — und genau daran
 * zerbricht später ein Angebot.* Millimeter werden auf ganze Zahlen gerundet (`stellen = 0`),
 * Flächen auf zwei, Volumen auf drei Stellen: ein Kubikmeter mit zwei Stellen verlöre eine
 * 5-Liter-Menge komplett.
 */
function runde(wert: number, stellen: number): number {
  const faktor = 10 ** stellen;
  return Math.round(wert * faktor) / faktor;
}

const M2 = 1_000_000;   // mm² je m²
const M3 = 1_000_000_000; // mm³ je m³

/** Überlappen sich zwei Öffnungen als Rechtecke in der Wandebene? */
function ueberlappen(a: OpeningNode, b: OpeningNode): boolean {
  // **Beide Achsen, nicht nur die waagerechte.** Ein Oberlicht über einer Tür teilt sich den
  // Abschnitt der Wandachse, überlappt aber nicht — es als Doppelabzug zu melden wäre ein
  // Fehlalarm, und Fehlalarme bringen einen Prüfschritt schneller zu Fall als fehlende.
  const waagerecht = a.offsetFromWallStart < b.offsetFromWallStart + b.width
    && b.offsetFromWallStart < a.offsetFromWallStart + a.width;
  const senkrecht = a.sillHeight < b.sillHeight + b.height
    && b.sillHeight < a.sillHeight + a.height;
  return waagerecht && senkrecht;
}

/**
 * Mengen **einer** Wand im gewählten Bezugsmaß.
 *
 * `oeffnungen` darf die Öffnungen der ganzen Szene enthalten — es wird auf die Wirtswand gefiltert.
 * **Eine fremde Öffnung wird gemeldet, nicht mitgerechnet**: sie stillschweigend abzuziehen wäre
 * ein Fehler, den niemand mehr findet.
 */
export function wandMengen(
  wand: WallNode,
  oeffnungen: readonly OpeningNode[],
  bezug: Bezugsmass,
): WandFlaecheErgebnis {
  const meldungen: Meldung[] = [];

  const fremde = oeffnungen.filter((o) => o.hostWallId !== wand.id);
  for (const o of fremde) {
    meldungen.push({
      art: 'fremde-oeffnung',
      text: `Öffnung ${o.id} gehört zu Wand ${o.hostWallId}, nicht zu ${wand.id}.`,
    });
  }
  const eigene = oeffnungen.filter((o) => o.hostWallId === wand.id);

  const laengeRoh = runde(Math.hypot(wand.end.x - wand.start.x, wand.end.y - wand.start.y), 0);

  // ── Bezugsmaß ───────────────────────────────────────────────────────────────
  const schichtSumme = (wand.schichten ?? []).reduce((n, s) => n + s.dickeMm, 0);
  const rohmassRest: string[] = [];
  let dicke = wand.thickness;

  if (bezug === 'fertig') {
    if (schichtSumme > wand.thickness) {
      // **Ein negatives Fertigmaß ist kein Ergebnis.**
      meldungen.push({
        art: 'schichten-dicker-als-wand',
        text: `Wand ${wand.id}: Schichten ${schichtSumme} mm > Dicke ${wand.thickness} mm.`,
      });
    } else if (schichtSumme > 0) {
      dicke = wand.thickness - schichtSumme;
    } else {
      // **Keine Schichten ⇒ fertig = roh, und das Ergebnis sagt es.**
      rohmassRest.push('dicke: keine Schichten am Knoten hinterlegt (AUF-76)');
    }
    // **Die Länge bleibt das Rohmaß** — ob eine Wand fertig kürzer ist, hängt von den ANGRENZENDEN
    // Wänden ab. Das ist eine Rechnung über den Wandverbund und gehört nicht in diesen Posten
    // (Auftrag §3). Die Grenze wird benannt, nicht verschwiegen.
    rohmassRest.push('laenge: Wandverbund nicht gerechnet (Auftrag §3)');
    // **Und die Höhe ebenfalls — hier weiche ich vom Wortlaut ab, mit Grund.** Der Auftrag sagt
    // „Dicke und Höhe abzüglich der Schichten". Die Schichten aus AUF-76 liegen QUER zur Dicke;
    // sie von der Höhe abzuziehen hat keine fachliche Grundlage. Was die Höhe fertig verkürzt,
    // ist der Fußboden- und Deckenaufbau — der hängt an `CeilingNode`, nicht an dieser Wand, und
    // liegt nicht im Eingang dieser Funktion. **Einen fehlenden Operanden zu erfinden ist genau
    // das, was das Operanden-Gate verbietet.** Also: nicht gerechnet, benannt, gemeldet.
    rohmassRest.push('hoehe: Fussboden-/Deckenaufbau nicht im Eingang (Operanden-Gate)');
  }

  // ── Kanten der Öffnungen ────────────────────────────────────────────────────
  for (const o of eigene) {
    if (o.offsetFromWallStart + o.width > laengeRoh) {
      meldungen.push({
        art: 'oeffnung-ragt-hinaus',
        text: `Öffnung ${o.id} endet bei ${o.offsetFromWallStart + o.width} mm, Wand ${wand.id} ist ${laengeRoh} mm lang.`,
      });
    }
    if (o.sillHeight + o.height > wand.height) {
      meldungen.push({
        art: 'oeffnung-hoeher-als-wand',
        text: `Öffnung ${o.id} reicht bis ${o.sillHeight + o.height} mm, Wand ${wand.id} ist ${wand.height} mm hoch.`,
      });
    }
  }
  for (let i = 0; i < eigene.length; i++) {
    for (let j = i + 1; j < eigene.length; j++) {
      const a = eigene[i]!;
      const b = eigene[j]!;
      if (ueberlappen(a, b)) {
        meldungen.push({
          art: 'oeffnungen-ueberlappen',
          text: `Öffnungen ${a.id} und ${b.id} überlappen in Wand ${wand.id} — der Abzug wäre doppelt.`,
        });
      }
    }
  }

  if (meldungen.length > 0) {
    return { art: 'meldung', meldungen };
  }

  // ── Rechnung ────────────────────────────────────────────────────────────────
  // Höhe und Länge 0 ergeben 0 — kein NaN, kein Infinity: es wird nur multipliziert.
  const bruttoMm2 = laengeRoh * wand.height;
  const oeffnungenMm2 = eigene.reduce((n, o) => n + o.width * o.height, 0);

  return {
    art: 'mengen',
    mengen: {
      bezug,
      nodeId: wand.id,
      laengeMm: laengeRoh,
      hoeheMm: wand.height,
      dickeMm: dicke,
      bruttoM2: runde(bruttoMm2 / M2, 2),
      oeffnungenM2: runde(oeffnungenMm2 / M2, 2),
      nettoM2: runde((bruttoMm2 - oeffnungenMm2) / M2, 2),
      bruttoM3: runde((bruttoMm2 * dicke) / M3, 3),
      oeffnungenM3: runde((oeffnungenMm2 * dicke) / M3, 3),
      nettoM3: runde(((bruttoMm2 - oeffnungenMm2) * dicke) / M3, 3),
      rohmassRest,
    },
  };
}
