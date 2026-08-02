/**
 * Z-01 / K-04 — **die Entscheidung, wann ein Werkzeug endet und wann es nur pausiert.**
 *
 * ---
 *
 * **Schritt 0 kam vor jeder Zeile Code** (`docs/browsertest-z01-2026-07-31.md`). Gemessen an der
 * Geometrie aus der Konva-Bühne, nicht am Bildschirmfoto:
 *
 * ```text
 * Zeiger im Canvas        1500,1400 -> 3200,1400
 * Zeiger ueber der Leiste 1500,1400 -> 3200,1400   unveraendert
 * Zeiger wieder drin      1500,1400 -> 4152, 689   folgt wieder
 * ```
 *
 * **Der Strich folgt dem Zeiger nicht — er bleibt liegen**, dort wo der Zeiger die Fläche zuletzt
 * berührt hat. *Das ist Yamas „langer Strich", und „einfrieren" ist nicht der harmlose
 * Zwischenzustand, für den man es halten könnte: eine eingefrorene Vorschau sieht aus wie ein
 * gezeichnetes Bauteil.*
 *
 * **Die Mutationsprobe VOR diesen Zusagen, wie K-04 sie verlangt — 8 Mutationen, 8 kamen durch.**
 * Das Modul war neu und trug keine einzige Zusage; die Zahl steht hier, weil der Auftrag sie
 * verlangt, *auch wenn sie 0 wäre*:
 *
 * ```text
 * zeigerDrinnen festgenagelt (die Mutation aus dem Blatt) · Werkzeugwechsel raeumt nicht auf
 * Verlassen BEENDET statt zu pausieren · Zurueckkehren belebt nicht wieder
 * Treppe zaehlt nicht als laufender Zug · Vorschau-Arten vertauscht
 * Pausentext verkehrt herum · Pausentext erscheint immer
 * ```
 *
 * Jede der acht ist unten geschlossen und gegen genau ihre Mutation gegengeprüft.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  beiWerkzeugwechsel, beiZeigerAus, beiZeigerEin, zugLaeuft, zeigtVorschau, pausenText,
  PAUSEN_TEXT, ZEICHEN_LEER, type ZeichenZustand,
} from '../app/tools/werkzeugEnde';

/** Ein halb gezogener Wandzug, Zeiger auf der Fläche. */
const halbeWand: ZeichenZustand = { wandStart: { x: 1500, y: 1400 }, treppeStart: null, zeigerDrinnen: true };
/** Ein halb gezogener Treppenzug. */
const halbeTreppe: ZeichenZustand = { wandStart: null, treppeStart: { x: 900, y: 300 }, zeigerDrinnen: true };

// --- Werkzeugwechsel bricht ab ---------------------------------------------------------------------

test('K-01: der Werkzeugwechsel verwirft die unbestätigte Teilaktion — beide Arten', () => {
  // Mutation: `return { ...z }`. Sie kam durch — und genau das war der Reststrich: der halbe Zug
  // des alten Werkzeugs blieb stehen, während das neue schon aktiv war.
  assert.equal(beiWerkzeugwechsel(halbeWand).wandStart, null, 'der Wandanfang überlebt den Werkzeugwechsel');
  assert.equal(beiWerkzeugwechsel(halbeTreppe).treppeStart, null, 'der Treppenanfang überlebt den Werkzeugwechsel');
  assert.equal(zugLaeuft(beiWerkzeugwechsel(halbeWand)), false, 'nach dem Wechsel läuft noch ein Zug');
});

test('K-01: der Werkzeugwechsel sagt NICHTS über den Zeiger — der steht, wo er steht', () => {
  // Die feine Stelle: wo der Zeiger ist, ändert sich durch einen Werkzeugwechsel nicht. Würde er
  // hier auf `true` gesetzt, erschiene beim Wechsel kurz eine Vorschau, obwohl der Zeiger draussen ist.
  assert.equal(beiWerkzeugwechsel({ ...halbeWand, zeigerDrinnen: false }).zeigerDrinnen, false);
  assert.equal(beiWerkzeugwechsel({ ...halbeWand, zeigerDrinnen: true }).zeigerDrinnen, true);
});

// --- Verlassen pausiert, es beendet nicht ----------------------------------------------------------

test('K-04: das Verlassen der Fläche PAUSIERT — der Anfangspunkt bleibt stehen', () => {
  // Mutation: Verlassen räumt die Startpunkte weg. Sie kam durch — und hätte jeden Zug verloren,
  // sobald jemand zur Werkzeugleiste fährt, um den Fang umzuschalten.
  const nach = beiZeigerAus(halbeWand);
  assert.deepEqual(nach.wandStart, { x: 1500, y: 1400 }, 'der Wandanfang geht beim Verlassen verloren');
  assert.equal(nach.zeigerDrinnen, false, 'das Verlassen wird nicht vermerkt');
  assert.equal(zugLaeuft(nach), true, 'der Zug gilt als beendet, obwohl er nur pausiert');
});

test('K-04: die Rückkehr belebt wieder — ohne Klick', () => {
  const nach = beiZeigerEin(beiZeigerAus(halbeWand));
  assert.equal(nach.zeigerDrinnen, true, 'die Rückkehr wird nicht vermerkt');
  assert.deepEqual(nach.wandStart, { x: 1500, y: 1400 }, 'der Anfangspunkt hat die Pause nicht überlebt');
});

// --- Die Vorschau: der Kern des Auftrags ------------------------------------------------------------

test('K-04: Zeiger draussen heisst KEINE Vorschau — und der Startpunkt bleibt trotzdem', () => {
  // **Die Mutation, die das Blatt selbst nennt:** `zeigerDrinnen` festgenagelt. Sie kam durch.
  // Ohne diese Zusage bleibt die Linie stehen, wo der Zeiger die Fläche zuletzt berührt hat —
  // in Schritt 0 gemessen als `1500,1400 -> 2930,3877`, quer über den halben Grundriss.
  const draussen = beiZeigerAus(halbeWand);
  assert.equal(zeigtVorschau(draussen, 'wand'), false, 'die Vorschau bleibt stehen, obwohl der Zeiger fort ist');
  assert.deepEqual(draussen.wandStart, { x: 1500, y: 1400 }, 'ausblenden hat den Zug gelöscht statt ihn zu pausieren');
  // Und drinnen wird sie gezeichnet — sonst prüfte die Zusage nur eine Richtung.
  assert.equal(zeigtVorschau(halbeWand, 'wand'), true, 'die Vorschau erscheint auch drinnen nicht');
});

test('K-04: jede Art sieht IHREN eigenen Anfangspunkt', () => {
  // Mutation: die beiden Arten vertauscht. Sie kam durch — ein halb gezogener Treppenzug hätte
  // eine Wandvorschau gezeigt und umgekehrt.
  assert.equal(zeigtVorschau(halbeWand, 'wand'), true);
  assert.equal(zeigtVorschau(halbeWand, 'treppe'), false, 'ein Wandzug zeigt eine Treppenvorschau');
  assert.equal(zeigtVorschau(halbeTreppe, 'treppe'), true);
  assert.equal(zeigtVorschau(halbeTreppe, 'wand'), false, 'ein Treppenzug zeigt eine Wandvorschau');
});

test('K-04: ohne angefangenen Zug gibt es nichts zu zeigen — drinnen wie draussen', () => {
  for (const drinnen of [true, false]) {
    const z = { ...ZEICHEN_LEER, zeigerDrinnen: drinnen };
    assert.equal(zeigtVorschau(z, 'wand'), false, `Wandvorschau ohne Anfang (drinnen=${drinnen})`);
    assert.equal(zeigtVorschau(z, 'treppe'), false, `Treppenvorschau ohne Anfang (drinnen=${drinnen})`);
  }
});

test('K-04: beide Arten zählen als laufender Zug', () => {
  // Mutation: nur die Wand zählt. Sie kam durch — ein pausierter Treppenzug hätte keinen Hinweis
  // bekommen und wäre stumm liegen geblieben.
  assert.equal(zugLaeuft(halbeWand), true);
  assert.equal(zugLaeuft(halbeTreppe), true, 'ein angefangener Treppenzug gilt nicht als laufend');
  assert.equal(zugLaeuft(ZEICHEN_LEER), false);
});

// --- K-05: der pausierte Zustand ist benannt --------------------------------------------------------

test('K-05: der Hinweis erscheint NUR, wenn wirklich etwas pausiert', () => {
  // Zwei Mutationen kamen hier durch: die Bedingung verkehrt herum, und „erscheint immer".
  // Ein Hinweis, der immer dasteht, wird nicht gelesen — und einer, der bei jedem Mausaustritt
  // erscheint, obwohl gar nichts angefangen wurde, ist Lärm.
  assert.equal(pausenText(beiZeigerAus(halbeWand)), PAUSEN_TEXT, 'der pausierte Zug wird nicht benannt');
  assert.equal(pausenText(beiZeigerAus(halbeTreppe)), PAUSEN_TEXT, 'der pausierte Treppenzug wird nicht benannt');
  assert.equal(pausenText(halbeWand), null, 'der Hinweis erscheint, obwohl der Zeiger auf der Fläche ist');
  assert.equal(pausenText(beiZeigerAus(ZEICHEN_LEER)), null, 'der Hinweis erscheint ohne angefangenen Zug');
  assert.equal(pausenText(ZEICHEN_LEER), null);
});

test('K-05: der Satz sagt beide Wege — fortsetzen und abbrechen', () => {
  // Ein Hinweis, der nur den Zustand nennt, lässt den Nutzer im Zweifel, wie er herauskommt.
  assert.match(PAUSEN_TEXT, /pausiert/, 'der Satz nennt den Zustand nicht');
  assert.match(PAUSEN_TEXT, /zurück|zurueck/i, 'der Satz sagt nicht, wie es weitergeht');
  assert.match(PAUSEN_TEXT, /Esc/, 'der Satz sagt nicht, wie man abbricht');
});

// --- Die reine Entscheidung bleibt rein --------------------------------------------------------------

test('K-04 (Grenze): die Funktionen ändern ihre Eingabe nicht', () => {
  // Sie werden aus Render-Pfaden gerufen. Eine Funktion, die ihr Argument verändert, erzeugt dort
  // Fehler, die niemand am Aufrufort sieht.
  const vorher: ZeichenZustand = { wandStart: { x: 10, y: 20 }, treppeStart: null, zeigerDrinnen: true };
  const kopie = JSON.parse(JSON.stringify(vorher)) as ZeichenZustand;
  beiWerkzeugwechsel(vorher); beiZeigerAus(vorher); beiZeigerEin(vorher);
  zeigtVorschau(vorher, 'wand'); pausenText(vorher); zugLaeuft(vorher);
  assert.deepEqual(vorher, kopie, 'eine der Funktionen verändert ihre Eingabe');
});

// --- K-01 / K-02: EIN Ort, und die Aufrufer räumen nicht selbst auf --------------------------------
//
// **Der Auftrag sagt es selbst: „Grep zählt Gestalt, nicht Sache."** Die Befehle im Kopf des
// Blattes zählen Vorkommen; diese Zusagen prüfen, dass an ihre Stelle wirklich der eine Ort
// getreten ist — und nicht bloss eine Umbenennung.

import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const hier = dirname(fileURLToPath(import.meta.url));
const lies = (p: string): string => readFileSync(join(hier, '../app/', p), 'utf8')
  .replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const app = lies('HausplanerApp.tsx');
const schiene = lies('rahmen/GruppenzeileUndSchiene.tsx');
const buehne = lies('rahmen/Buehne.tsx');

test('K-01: kein Aufrufer räumt selbst auf — es gibt genau einen Ort', () => {
  for (const [name, quelle] of [['HausplanerApp', app], ['Schiene', schiene], ['Bühne', buehne]] as const) {
    assert.ok(!quelle.includes('setWandStart(null)'), `${name} räumt den Wandanfang selbst auf`);
    assert.ok(!quelle.includes('setTreppeStart(null)'), `${name} räumt den Treppenanfang selbst auf`);
  }
  // presence-Partner nach R2: den einen Ort gibt es, und er benutzt die reine Entscheidung.
  assert.match(app, /const beendeWerkzeug = React\.useCallback/, 'der eine Ort fehlt — die Zusage misst Leere');
  assert.match(app, /beiWerkzeugwechsel\(/, 'der eine Ort trifft die Entscheidung selbst, statt sie zu lesen');
});

test('K-02: der Rückfall auf `auswahl` läuft über denselben Ort wie die Leiste', () => {
  // **Die Stelle, an der das Aufräumen ganz gefehlt hat.** Fällt ein Werkzeug im Bereich aus,
  // blieb der halbe Zug stehen — mit einem Werkzeug, das ihn nicht mehr beenden konnte.
  //
  // Der Gegenbeweis des Blattes: „den Aufruf bewusst wieder auf den alten direkten Weg setzen
  // und zeigen, dass der neue Test dabei ROT wird." Genau dagegen prüft diese Zusage — eine
  // Umbenennung brächte den Zähler des Blattes ebenfalls auf 0, diese Zusage aber nicht.
  assert.ok(!app.includes("setActiveTool('auswahl')"),
    'der Rückfall setzt das Werkzeug wieder direkt, am einen Ort vorbei');
  const zweig = app.match(/if \(t && !resolveToolState\(t, werkzeugKontext\)\.enabled\) \{[\s\S]{0,200}?\}/);
  assert.ok(zweig, 'der Rückfall-Zweig wurde nicht gefunden — die Zusage misst Leere');
  assert.match(zweig[0], /beendeWerkzeug\('auswahl'\)/, 'der Rückfall geht nicht über den einen Ort');
});

test('K-06: der bestehende Escape-Weg wird benutzt, nicht ein zweiter gebaut', () => {
  const zurueck = app.match(/const setzeWerkzeugZurueck = React\.useCallback\(\(\) => \{[\s\S]{0,300}?\}/);
  assert.ok(zurueck, '`setzeWerkzeugZurueck` wurde nicht gefunden — die Zusage misst Leere');
  assert.match(zurueck[0], /beendeWerkzeug\('auswahl'\)/, 'der Escape-Weg räumt wieder selbst auf');
  // **Die Aussage ist nicht „vier Ebenen", sondern „EIN Escape-Weg".** Z-10 hat eine fünfte
  // hinzugefügt (`masseingabe`) — und genau richtig, denn die Alternative wäre ein zweiter
  // Tastenhörer gewesen, also das, was diese Zusage verhindern soll.
  //
  // *Eine feste Zahl hätte hier das Falsche geschützt: sie geht bei jeder neuen Ebene rot und
  // bleibt grün, wenn jemand daneben einen eigenen `keydown`-Hörer baut.* Geprüft wird deshalb,
  // dass ALLE Escape-Behandlung über `useEscapeEbene` läuft und kein zweiter Hörer daneben steht.
  assert.ok((app.match(/useEscapeEbene/g) ?? []).length >= 5,
    'die Escape-Ebenen sind weniger geworden — eine Ebene ist verschwunden');
  // **GENAU EINER, nicht null.** Mein erster Entwurf verbot jeden `keydown`-Hörer — und die
  // Hauptansicht hat einen, den richtigen: er führt `tastenAbsicht` aus. *Eine Zusage, die den
  // legitimen Weg verbietet, ist sofort rot und sagt nichts über den Fehler, den sie meint.*
  // Der Fehler ist der ZWEITE Hörer.
  assert.equal((app.match(/addEventListener\('keydown'/g) ?? []).length, 1,
    'nicht mehr genau ein Tastenhörer — ein zweiter wäre die zweite Wahrheit über dieselbe Taste');
});

test('K-03: die Bühne meldet das Verlassen — und entscheidet es nicht selbst', () => {
  assert.match(buehne, /onMouseLeave=\{beiZeigerAus\}/, 'die Bühne behandelt das Verlassen nicht');
  assert.match(buehne, /onMouseEnter=\{beiZeigerEin\}/, 'die Rückkehr wird nicht behandelt');
  // K-03 des Blattes verlangt zusätzlich: kein Zustand in der Bühne.
  for (const muster of [/useState/, /usePlannerUiStore/]) {
    assert.doesNotMatch(buehne, muster, `die Bühne hält Zustand: ${muster}`);
  }
});
