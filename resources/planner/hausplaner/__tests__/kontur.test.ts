/**
 * Z-05 — **die Kontur, ihre Prüfung und ihr Anschluss.**
 *
 * ---
 *
 * **Die Mutationsprobe VOR diesen Zusagen — 8 Mutationen, SIEBEN kamen durch:**
 *
 * ```text
 * BLIND (7)  Selbstschnitt-Pruefung liefert immer false · KONTUR_MIN_PUNKTE 3 -> 2
 *            Flaechenpruefung abgeschaltet · letzte und erste Kante gelten als NICHT benachbart
 *            Schliessen ohne Fangtoleranz · kollinearer Zweig entfernt
 *            zugLaeuft ignoriert die Kontur
 * gefangen   Escape raeumt die Kontur nicht auf (1 rot — aus `werkzeugEnde.test.ts`)
 * ```
 *
 * *Die eine gefangene traf die Zusage aus Z-01, die es schon gab. Von dem, was Z-05 neu baut,
 * hielt nichts — die Geometrie war komplett ungedeckt.*
 *
 * ---
 *
 * **Warum die Fälle so und nicht anders gewählt sind.** Das Blatt nennt fünf; jeder trifft eine
 * andere Mutation, und keiner prüft eine Punktzahl (F-06):
 *
 * ```text
 * Rechteck         false   faengt "erste und letzte Kante sind NICHT benachbart"
 * L-Form           false   das ist der Zweck der Scheibe - eine Bounding-Box waere hier falsch
 * Acht             true    faengt "Selbstschnitt liefert immer false"
 * T-Spitze         true    faengt "kollinearer Zweig entfernt" - beruehren OHNE zu kreuzen
 * drei auf Linie   keine-flaeche
 * zwei Punkte      zu-wenig-punkte
 * ```
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  schneidetSichSelbst, pruefeKontur, konturStatusText,
  KONTUR_MIN_PUNKTE, KONTUR_MELDUNG,
} from '../geometry/kontur';
import { ZEICHEN_LEER, beiWerkzeugwechsel, zugLaeuft, zeigtVorschau } from '../app/tools/werkzeugEnde';
import { ohneKommentare } from './_zerlegteApp';

const hier = dirname(fileURLToPath(import.meta.url));
const app = ohneKommentare(readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8'));
const buehne = ohneKommentare(readFileSync(join(hier, '../app/rahmen/Buehne.tsx'), 'utf8'));

const RECHTECK = [{ x: 0, y: 0 }, { x: 6000, y: 0 }, { x: 6000, y: 4000 }, { x: 0, y: 4000 }];
/** Die Form, für die es diese Scheibe gibt: eine Bounding-Box wäre hier um ein Viertel zu groß. */
const L_FORM = [
  { x: 0, y: 0 }, { x: 6000, y: 0 }, { x: 6000, y: 3000 },
  { x: 3000, y: 3000 }, { x: 3000, y: 6000 }, { x: 0, y: 6000 },
];
/** Die Acht: die dritte Kante kreuzt die erste. **Man sieht sie im Plan nicht.** */
const ACHT = [{ x: 0, y: 0 }, { x: 6000, y: 6000 }, { x: 6000, y: 0 }, { x: 0, y: 6000 }];
/** Eine Spitze, die auf einer früheren Kante LIEGT — berühren, ohne zu kreuzen. */
const T_SPITZE = [{ x: 0, y: 0 }, { x: 6000, y: 0 }, { x: 3000, y: 0 }, { x: 3000, y: 6000 }];
const AUF_EINER_LINIE = [{ x: 0, y: 0 }, { x: 1000, y: 0 }, { x: 2000, y: 0 }];
const ZWEI_PUNKTE = [{ x: 0, y: 0 }, { x: 1000, y: 0 }];

// --- K-02: die Selbstschnitt-Prüfung, gegen eine ECHTE Acht ---------------------------------------

test('K-02: Rechteck und L-Form schneiden sich nicht selbst', () => {
  // Mutation „letzte und erste Kante gelten als NICHT benachbart" kam durch. Sie macht JEDES
  // geschlossene Polygon zum Selbstschnitt — auch das Rechteck, das man als Erstes zeichnet.
  assert.equal(schneidetSichSelbst(RECHTECK), false, 'das Rechteck gilt als selbstschneidend');
  assert.equal(schneidetSichSelbst(L_FORM), false, 'die L-Form gilt als selbstschneidend — genau sie soll gehen');
  assert.equal(pruefeKontur(L_FORM).ok, true, 'die L-Form wird abgelehnt');
});

test('K-02: die Acht wird erkannt', () => {
  // Mutation „Selbstschnitt-Pruefung liefert immer false" kam durch. Das ist der stille Fall:
  // die Flaeche entsteht, sie ist nur falsch.
  assert.equal(schneidetSichSelbst(ACHT), true, 'die Acht kommt als gueltige Kontur durch');
  const urteil = pruefeKontur(ACHT);
  assert.equal(urteil.ok, false);
  assert.equal(urteil.grund, 'selbstschnitt', 'die Acht wird abgelehnt, aber mit dem falschen Grund');
});

test('K-02: eine Kante, die auf einer anderen LIEGT, zaehlt auch', () => {
  // Mutation „kollinearer Zweig entfernt" kam durch. Zwei Kanten, die aufeinanderliegen, kreuzen
  // sich im Sinne der Vorzeichen NICHT — sie sind trotzdem ein Selbstschnitt.
  assert.equal(schneidetSichSelbst(T_SPITZE), true, 'eine auf sich selbst zurueckgefuehrte Kontur kommt durch');
});

test('K-02: drei Punkte auf einer Linie umschliessen keine Flaeche', () => {
  // Mutation „Flaechenpruefung abgeschaltet" kam durch.
  const urteil = pruefeKontur(AUF_EINER_LINIE);
  assert.equal(urteil.ok, false);
  assert.equal(urteil.grund, 'keine-flaeche');
});

test('K-02: unter der Mindestzahl wird abgelehnt, und zwar mit diesem Grund', () => {
  // Mutation „KONTUR_MIN_PUNKTE 3 -> 2" kam durch. Zwei Punkte sind eine Strecke, keine Flaeche —
  // und das Zod-Schema verlangt ohnehin `min(3)`.
  const urteil = pruefeKontur(ZWEI_PUNKTE);
  assert.equal(urteil.ok, false);
  assert.equal(urteil.grund, 'zu-wenig-punkte');
  // **Geprueft wird die AUSSAGE, nicht die Zahl** (F-06): unter der Grenze abgelehnt, ab der
  // Grenze nicht mehr AUS DIESEM Grund.
  const knapp = Array.from({ length: KONTUR_MIN_PUNKTE }, (_, i) => ({ x: i * 1000, y: i * i * 500 }));
  assert.notEqual(pruefeKontur(knapp).grund, 'zu-wenig-punkte',
    'genau an der Mindestzahl wird immer noch wegen zu weniger Punkte abgelehnt');
});

// --- Der Mensch erfaehrt, WARUM ------------------------------------------------------------------

test('Z-05: jeder Ablehnungsgrund hat einen Satz, der den Weg heraus nennt', () => {
  // Ein „ungueltig" ohne Grund laesst jemanden dieselbe Acht ein zweites Mal zeichnen.
  for (const grund of ['zu-wenig-punkte', 'selbstschnitt', 'keine-flaeche'] as const) {
    const satz = KONTUR_MELDUNG[grund];
    assert.ok(satz && satz.length > 25, `der Satz zu \`${grund}\` fehlt oder ist zu knapp`);
    assert.equal(konturStatusText(4, grund), satz, 'der Fehlergrund schlaegt den Fortschrittstext nicht');
  }
  // presence-Partner nach R2: ohne Fehler steht der Fortschritt da, nicht Leere.
  assert.match(konturStatusText(3, null), /Enter schließt/, 'der laufende Zug nennt den Weg zum Schliessen nicht');
  assert.match(konturStatusText(3, null), /Esc verwirft/, 'der laufende Zug nennt den Weg zum Abbrechen nicht');
  assert.match(konturStatusText(0, null, 6), /geschlossen/, 'ein geschlossener Zug sieht aus wie ein verworfener');
});

// --- Der Anschluss: ein Werkzeug endet an genau einer Stelle (Z-01) ------------------------------

test('Z-05: die Kontur endet an DERSELBEN einen Stelle wie alles andere', () => {
  // Mutation „Escape raeumt die Kontur nicht auf" wurde gefangen — von der Zusage aus Z-01.
  // Diese hier haelt fest, dass die Kontur ueberhaupt an dieser Stelle haengt.
  const laeuft = { ...ZEICHEN_LEER, konturPunkte: RECHTECK };
  assert.equal(zugLaeuft(laeuft), true, 'ein laufender Konturzug gilt nicht als laufender Zug');
  assert.deepEqual(beiWerkzeugwechsel(laeuft).konturPunkte, [], 'der Werkzeugwechsel laesst die Kontur liegen');
  assert.equal(zeigtVorschau(laeuft, 'kontur'), true, 'die Vorschau der Kontur wird nicht gezeigt');
  assert.equal(zeigtVorschau({ ...laeuft, zeigerDrinnen: false }, 'kontur'), false,
    'die Vorschau bleibt stehen, wenn der Zeiger die Flaeche verlaesst');
});

test('Z-05: die Hauptansicht raeumt die Kontur NICHT an einer eigenen Stelle auf', () => {
  // Der Kern von Z-01: sieben Aufraeumstellen wurden eine. Eine achte macht das rueckgaengig.
  const stellen = [...app.matchAll(/setKonturPunkte\(\[\]\)/g)].length;
  assert.equal(stellen, 1,
    `\`setKonturPunkte([])\` steht ${stellen}-mal — genau einmal ist richtig (beim Schliessen); das Aufraeumen laeuft ueber beiWerkzeugwechsel`);
  assert.match(app, /setKonturPunkte\(\[\.\.\.nach\.konturPunkte\]\)/,
    'der Werkzeugwechsel setzt die Konturpunkte nicht mehr aus der einen Entscheidung');
});

// --- Der Fang beim Schliessen kommt aus Z-02 -----------------------------------------------------

test('Z-05: geschlossen wird mit DERSELBEN Fangtoleranz wie ueberall', () => {
  // Mutation „Schliessen ohne Fangtoleranz" kam durch: man muesste den ersten Punkt auf den
  // Millimeter genau treffen. **Ein eigener Radius hier waere die zweite Fang-Wahrheit, die
  // Z-02 gerade abgeschafft hat.**
  assert.match(app, /Math\.hypot\(erster\.x - p\.x, erster\.y - p\.y\) <= toleranzAusZoom\(zoom\)/,
    'der Schliess-Fang benutzt nicht mehr die eine Toleranz aus Z-02');
});

test('Z-05: Klick und Enter laufen durch DIESELBE Pruefung', () => {
  // Zwei Wege mit je eigener Pruefung waeren zwei Wahrheiten — und die eine altert.
  assert.equal([...app.matchAll(/pruefeKontur\(/g)].length, 1,
    'die Konturpruefung wird an mehr als einer Stelle aufgerufen');
  assert.equal([...app.matchAll(/schliesseKontur\(/g)].length, 2,
    'Klick und Enter rufen nicht beide denselben Schliess-Weg');
  assert.match(app, /case 'kontur-schliessen':/, 'Enter hat keine Wirkung mehr');
});

// --- Sichtbar, waehrend man zeichnet -------------------------------------------------------------

test('Z-05: die laufende Kontur ist auf der Buehne zu sehen', () => {
  // „Entdeckung" aus dem Blatt: faellt die Kontur aus, sieht man es sofort. Ohne Vorschau
  // zeichnet man blind und merkt die Acht erst beim Ablehnen.
  assert.match(buehne, /zeigtVorschau\(\{[^}]*konturPunkte[^}]*\}, 'kontur'\) && werkzeug === 'kontur'/,
    'die Kontur-Vorschau fehlt oder haengt nicht am Zeichenzustand');
  assert.match(buehne, /konturPunkte\.flatMap\(\(q\) => \[q\.x, q\.y\]\)/,
    'die gesetzten Punkte werden nicht als Zug gezeichnet');
  assert.match(buehne, /konturPunkte\[0\]!\.x/, 'der erste Punkt ist nicht markiert — er ist das Ziel zum Schliessen');
});
