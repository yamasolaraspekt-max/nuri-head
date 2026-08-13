/**
 * W-05/2 — **die Auswahl überlebt keinen Wandzug.**
 *
 * **Der Befund, der diesen Auftrag trägt:** erkannte Räume haben keine Kennung; ihre Identität ist
 * der Index in der Liste. Eine Auswahl, die eine Änderung der Raumliste überlebt, zeigt danach auf
 * einen **anderen** Raum als den gewählten — und der Nutzer kann es nicht merken, weil die
 * Hervorhebung gleich aussieht. *Dieselbe Klasse wie die Panel-Zusage in A-24.*
 *
 * **Warum diese Zusage hier steht und nicht in einer Sichtprobe** (W-05-2-1b, wörtlich): eine
 * Sichtprobe zeigt einen Bildschirm zu einem Zeitpunkt. Der Wächter hält die Regel auch dann, wenn
 * jemand die Auswahl später an einen Effekt hängt oder die Signatur vergröbert.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  raumSignatur, gueltigeAuswahl, waehleRaum,
  type RaumFuerAuswahl,
} from '../app/raumAuswahl';

/**
 * Ein Raum, so schmal wie die Auswahl ihn braucht. Die Zahlen sind mm² bzw. mm.
 *
 * **BERICHTIGT (Runde 2): die Hilfsfunktion kennt jetzt einen ORT.** *Vorher lag jeder Raum auf
 * derselben Diagonale — sie konnte den kritischen Fall gar nicht bilden, und der Test „die
 * REIHENFOLGE zählt" lief deshalb über verschiedene Flächen, also über den trivialen Fall.*
 * **Ein Prüfstand, der den kritischen Fall nicht herstellen kann, prüft ihn auch nicht.**
 */
const raum = (flaecheMm2: number, ecken = 4, x = 0, y = 0): RaumFuerAuswahl => ({
  flaecheMm2,
  polygon: Array.from({ length: ecken }, (_, i) => ({ x: x + i * 1000, y: y + i * 1000 })),
});

const ZWEI = [raum(12_000_000), raum(8_000_000)];

// --- W-05-2-1: die tragende Zusage ---------------------------------------------------------------
test('W-05-2-1: eine Auswahl ueberlebt die AENDERUNG der Raumliste NICHT', () => {
  const auswahl = waehleRaum(1, ZWEI);
  assert.equal(gueltigeAuswahl(auswahl, ZWEI), 1, 'an derselben Liste gilt sie');

  // Eine Wand kommt dazu: ein dritter Raum entsteht.
  const drei = [...ZWEI, raum(5_000_000)];
  assert.equal(gueltigeAuswahl(auswahl, drei), null,
    'nach einer Aenderung der Liste gibt es KEINE Auswahl — nicht eine auf einem anderen Raum');
});

test('W-05-2-1: auch eine Aenderung OHNE Anzahlwechsel setzt zurueck', () => {
  // Der gefaehrlichste Fall: gleich viele Raeume, aber ein anderer Zuschnitt. Wer nur die ANZAHL
  // vergleicht, laesst die Auswahl stehen — und sie zeigt auf einen anderen Raum.
  const auswahl = waehleRaum(0, ZWEI);
  const verschoben = [raum(11_000_000), raum(9_000_000)];
  assert.equal(gueltigeAuswahl(auswahl, verschoben), null);
});

test('W-05-2-1: auch eine andere ECKENZAHL bei gleicher Flaeche setzt zurueck', () => {
  const auswahl = waehleRaum(0, ZWEI);
  const andersGeschnitten = [raum(12_000_000, 6), raum(8_000_000)];
  assert.equal(gueltigeAuswahl(auswahl, andersGeschnitten), null);
});

// --- Die Gegenrichtung: sie darf nicht IMMER zuruecksetzen ---------------------------------------
test('W-05-2-1 Gegenprobe: dieselbe Liste ein zweites Mal abgeleitet BEHAELT die Auswahl', () => {
  // Ohne diese Zusage waere die Auflage in ihr Gegenteil verkehrt: eine Auswahl, die sich bei
  // jedem Rendern selbst loescht, ist keine Auswahl. `raeumeAus` laeuft in einem useMemo und
  // liefert bei gleichen Waenden ein NEUES Feld mit gleichem Inhalt.
  const auswahl = waehleRaum(1, ZWEI);
  const neuAbgeleitet = [raum(12_000_000), raum(8_000_000)];
  assert.equal(gueltigeAuswahl(auswahl, neuAbgeleitet), 1,
    'gleicher Inhalt, neues Feld — die Auswahl gilt weiter');
});

test('die Flaeche wird auf ganze mm² gerundet — ein Gleitkomma-Rest darf nicht zuruecksetzen', () => {
  const auswahl = waehleRaum(0, [raum(12_000_000.4)]);
  assert.equal(gueltigeAuswahl(auswahl, [raum(12_000_000.2)]), 0);
});

// --- Die drei Wege zu `null`, einzeln -----------------------------------------------------------
test('keine Auswahl gemerkt -> null', () => {
  assert.equal(gueltigeAuswahl(null, ZWEI), null);
});

test('Index ausserhalb -> null, auch wenn die Signatur passen wuerde', () => {
  const auswahl = { index: 7, signatur: raumSignatur(ZWEI) };
  assert.equal(gueltigeAuswahl(auswahl, ZWEI), null);
});

test('leere Raumliste -> null', () => {
  assert.equal(gueltigeAuswahl(waehleRaum(0, ZWEI), []), null);
});

// --- Die Signatur selbst -------------------------------------------------------------------------
test('die Signatur unterscheidet Anzahl, Flaeche und Eckenzahl', () => {
  const a = raumSignatur(ZWEI);
  assert.notEqual(a, raumSignatur([...ZWEI, raum(1)]), 'Anzahl');
  assert.notEqual(a, raumSignatur([raum(12_000_001), raum(8_000_000)]), 'Flaeche');
  assert.notEqual(a, raumSignatur([raum(12_000_000, 5), raum(8_000_000)]), 'Eckenzahl');
  assert.equal(a, raumSignatur([raum(12_000_000), raum(8_000_000)]), 'gleicher Inhalt, gleiche Signatur');
});

test('die REIHENFOLGE zaehlt — zwei vertauschte Raeume sind nicht dieselbe Liste', () => {
  // Wichtig, weil die Auswahl am Index haengt: vertauschte Raeume bedeuten, dass Index 0 auf einen
  // anderen Raum zeigt. Eine Signatur, die das nicht sieht, waere blind fuer genau den Fall.
  //
  // BERICHTIGT (Runde 2): dieser Test lief ueber ZWEI mit VERSCHIEDENEN Flaechen — den trivialen
  // Fall, den schon die Flaeche allein faengt. Er trug den Namen eines Kriteriums und mass etwas
  // anderes. Jetzt laeuft er ueber GLEICHE Flaeche und gleiche Eckenzahl; unterscheidbar sind die
  // zwei Raeume nur noch durch ihren ORT.
  assert.notEqual(raumSignatur(ZWEI), raumSignatur([...ZWEI].reverse()), 'verschiedene Flaechen');

  const gleichGross = [raum(10_000_000, 4, 0, 0), raum(10_000_000, 4, 900, 0)];
  assert.notEqual(
    raumSignatur(gleichGross), raumSignatur([...gleichGross].reverse()),
    'GLEICHE Flaeche, GLEICHE Eckenzahl — unterscheidbar nur durch den Ort',
  );
});

// --- Der Befund aus Runde 2, als eigene Zusage ---------------------------------------------------
test('W-05-2-1: zwei gleich grosse Raeume an VERSCHIEDENEN Orten, getauscht -> KEINE Auswahl', () => {
  // Der Fall, den der Evaluator am gebauten Code gefahren hat und der rot war: zwei Raeume mit
  // 10 000 000 mm² und 4 Ecken, verschiedene Orte, Reihenfolge getauscht. Die alte Signatur war
  // fuer BEIDE Listen identisch — gewaehlt war der Raum bei x=0, hervorgehoben wurde der bei x=900.
  // WOERTLICH die Falschauskunft, die W-05-2-1 verhindern soll.
  const A = [raum(10_000_000, 4, 0, 0), raum(10_000_000, 4, 900, 0)];
  const B = [...A].reverse();

  const auswahl = waehleRaum(0, A);
  assert.equal(gueltigeAuswahl(auswahl, A), 0, 'an derselben Liste gilt sie');
  assert.equal(gueltigeAuswahl(auswahl, B), null,
    'nach dem Tausch gibt es KEINE Auswahl — nicht eine auf dem anderen Raum');
});

test('W-05-2-1: ein VERSCHOBENER Raum bei gleicher Flaeche und Eckenzahl setzt zurueck', () => {
  // Die zweite Haelfte desselben Befundes: nicht nur Tausch, auch Verschiebung. Eine Wand ziehen,
  // ohne die Flaeche zu aendern, ist eine gewoehnliche Bedienhandlung.
  const auswahl = waehleRaum(0, [raum(10_000_000, 4, 0, 0)]);
  assert.equal(gueltigeAuswahl(auswahl, [raum(10_000_000, 4, 500, 0)]), null);
});

test('der ORT wird auf ganze mm gerundet — ein Gleitkomma-Rest darf nicht zuruecksetzen', () => {
  // Dieselbe Begruendung wie bei der Flaeche: sonst loescht sich die Auswahl bei jedem Rendern,
  // und die Auflage waere in ihr Gegenteil verkehrt.
  const auswahl = waehleRaum(0, [raum(10_000_000, 4, 0.4, 0.4)]);
  assert.equal(gueltigeAuswahl(auswahl, [raum(10_000_000, 4, 0.2, 0.2)]), 0);
});
