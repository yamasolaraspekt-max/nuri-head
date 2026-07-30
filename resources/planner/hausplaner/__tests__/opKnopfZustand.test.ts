/**
 * AUF-59 — die drei Zustände der Icon-Zeile.
 *
 * **Die Forderung, wörtlich:** gesperrt unterscheidet sich in **mindestens zwei** Merkmalen von
 * bedienbar, nicht nur in der Icon-Farbe · der Rahmen trägt den **Schalter**-Zustand, nicht jeden
 * Knopf · die Textknöpfe weichen den vorhandenen Icons · **kein Werkzeug verschwindet, keine Sperre
 * ändert sich** — nur ihre Lesbarkeit.
 *
 * Der letzte Halbsatz ist das eigentliche Risiko dieses Postens: Wer beim Umbau der Optik eine
 * Sperre verschiebt, hat die Aufgabe verfehlt. Deshalb prüft dieser Test zuerst, dass die Regel
 * **nichts entscheidet**, sondern nur beschreibt.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { opKnopfBild, opZustand, unterschiede } from '../app/dashboard/opKnopfZustand';
// AUF-48: die Hauptansicht ist zerlegt — diese Zusage liest ALLE ihre Teile.
import { zerlegteApp } from './_zerlegteApp';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const app = ohneKommentare(zerlegteApp());
const regel = ohneKommentare(readFileSync(join(hier, '../app/dashboard/opKnopfZustand.ts'), 'utf8'));

const BEDIENBAR = opKnopfBild(false, false);
const GESPERRT = opKnopfBild(false, true);
const SCHALTER = opKnopfBild(true, false);

// --- Die Forderung: mindestens zwei Merkmale ----------------------------------------------------
test('gesperrt unterscheidet sich in MINDESTENS ZWEI Merkmalen von bedienbar', () => {
  const u = unterschiede(BEDIENBAR, GESPERRT);
  assert.ok(u.length >= 2, `nur ${u.length} Unterschied(e): ${u.join(', ')}`);
  // gemessen sind es drei — und die Icon-Farbe ist nur einer davon
  assert.deepEqual(u.sort(), ['deckkraft', 'grundToken', 'iconToken']);
  assert.ok(u.some((m) => m !== 'iconToken'), 'die Icon-Farbe allein war der alte Zustand');
});

test('der Rahmen trägt den SCHALTER — kein anderer Knopf ist eingerahmt', () => {
  assert.equal(SCHALTER.rahmenToken, 'brandInk');
  assert.equal(BEDIENBAR.rahmenToken, null, 'ein bedienbarer Knopf ist kein Schalter');
  assert.equal(GESPERRT.rahmenToken, null, 'ein gesperrter erst recht nicht');
  // Damit ist der Rahmen ein eindeutiges Merkmal: genau ein Zustand trägt ihn.
  const mitRahmen = [SCHALTER, BEDIENBAR, GESPERRT].filter((b) => b.rahmenToken !== null);
  assert.equal(mitRahmen.length, 1);
});

test('gesperrt schlägt aktiv — ein gesperrter Schalter sieht nicht bedienbar aus', () => {
  assert.equal(opZustand(true, true), 'gesperrt');
  assert.equal(opKnopfBild(true, true).cursor, 'not-allowed');
  assert.equal(opKnopfBild(true, true).rahmenToken, null);
});

test('jeder der drei Zustände ist von jedem anderen in ≥2 Merkmalen unterscheidbar', () => {
  const paare: Array<[string, ReturnType<typeof opKnopfBild>, ReturnType<typeof opKnopfBild>]> = [
    ['bedienbar/gesperrt', BEDIENBAR, GESPERRT],
    ['bedienbar/schalter', BEDIENBAR, SCHALTER],
    ['gesperrt/schalter', GESPERRT, SCHALTER],
  ];
  for (const [name, a, b] of paare) {
    assert.ok(unterschiede(a, b).length >= 2, `${name}: nur ${unterschiede(a, b).length} Merkmal(e)`);
  }
});

// --- Die Regel entscheidet nichts ---------------------------------------------------------------
test('keine Sperre ändert sich: die Regel liest `gesperrt`, sie ermittelt es nicht', () => {
  for (const verboten of ['resolveToolState', 'selectedNodeIds', 'waende', 'scene', 'store']) {
    assert.ok(!regel.includes(verboten), `${verboten} gehört nicht in eine Aussehens-Regel`);
  }
  // Die App setzt `disabled` unverändert von außen und reicht es nur durch.
  assert.match(app, /opKnopfBild\(Boolean\(aktiv\), Boolean\(disabled \|\| geplant\)\)/);
  assert.match(app, /disabled=\{disabled \|\| geplant\}/, 'die Sperre selbst ist unangetastet');
});

test('keine Farbwerte in der Regel — sie liefert Token', () => {
  assert.doesNotMatch(regel, /#[0-9a-fA-F]{3,8}\b|rgba?\(/);
  assert.doesNotMatch(regel, /\bT\./);
  // AUF-48 Scheibe 4a: die Token-Tabelle ist nach `studioDaten.ts` gezogen — sie hatte zwei
  // Nutzer (`opStil` im Kopfrahmen, `knopf()` in der Hauptfunktion) und darf nicht doppelt
  // stehen. **Die gepruefte Eigenschaft ist unveraendert:** die Regel liefert Token-NAMEN, und
  // es gibt genau eine Tabelle, die daraus Farben macht.
  const tokenTabelle = readFileSync(join(hier, '../app/studioDaten.ts'), 'utf8');
  assert.match(tokenTabelle, /export const OP_TOKEN: Record<string, string>/);
  assert.doesNotMatch(app, /const OP_TOKEN\b/, 'die Token-Tabelle steht ein zweites Mal im Planer');
});

// --- Die Dublette ist gewichen ------------------------------------------------------------------
test('die ausgeschriebenen Spiegel-Knöpfe sind aus dem Panel verschwunden', () => {
  assert.ok(!app.includes('↔ Links/Rechts'), 'der Textknopf steht noch im Panel');
  assert.ok(!app.includes('↕ Oben/Unten'));
});

test('aber die FUNKTION ist geblieben — als Icon in der Bedienzeile, mit derselben Sperre', () => {
  // Das war die Bedingung: der Text weicht dem Icon, die Handlung bleibt erreichbar.
  assert.match(app, /title="Grundriss links\/rechts spiegeln" icon="mirror-h" disabled=\{waende\.length === 0\} onClick=\{\(\) => spiegeleGrundriss\('vertikal'\)\}/);
  assert.match(app, /title="Grundriss oben\/unten spiegeln" icon="mirror-v" disabled=\{waende\.length === 0\} onClick=\{\(\) => spiegeleGrundriss\('horizontal'\)\}/);
  // und es gibt jeden Aufruf nur noch EINMAL — keine zweite Stelle mit derselben Handlung
  assert.equal((app.match(/spiegeleGrundriss\('vertikal'\)/g) ?? []).length, 1);
  assert.equal((app.match(/spiegeleGrundriss\('horizontal'\)/g) ?? []).length, 1);
});

test('die Icon-Form trägt weiterhin einen `title` — anders als der entfernte Textknopf', () => {
  // Yamas Punkt: die Icons haben einen Tooltip, die Textknöpfe hatten keinen.
  assert.match(app, /title=\{geplant \? `\$\{title\} \(geplant\)` : title\}/);
});
