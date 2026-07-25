/**
 * AUF-35a — „Markieren": Mehrfachauswahl, Auswahlmodi, Hit-Test.
 *
 * Geprüft wird, was der Auftrag als Abnahme benennt: je Modus ein Fall (K3), die Ableitung aus der
 * Eingabe (K4), der Hit-Test mit seiner Sortierung (K5), `aufloeseDarstellung` für alle fünf
 * Zustände ohne rohen Farbwert (K6), die aufgelösten `length === 1`-Stellen (K7) und die
 * Mehrfach-Ansicht bei gemischten Typen (K8).
 *
 * **Alles reine Funktionen** — kein DOM, kein Store, kein Renderer. Genau deshalb ist die Logik
 * aus Yamas Vue-Referenz hier gelandet und nicht im Klick-Handler: dort wäre sie nur mit Browser
 * prüfbar gewesen.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import {
  aufloeseAuswahlmodus, wendeAuswahlAn, klickInsLeere, LEERE_AUSWAHL,
  type Auswahlstand,
} from '../app/tools/auswahlModus';
import { besterTreffer, trefferInReihenfolge, toleranzInWelt, type TrefferKandidat } from '../app/tools/trefferSuche';
import { aufloeseDarstellung } from '../app/tools/auswahlDarstellung';
import { mehrfachUebersicht, benenne } from '../app/tools/auswahlUebersicht';
import { toolNach } from '../app/tools/toolRegistry';
import type { SceneNode } from '../../hausplaner/domain/scene.types';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const appQuelle = ohneKommentare(readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8'));

// --- K4: die Ableitung aus der Eingabe ----------------------------------------------------------
test('K4: Shift→add · Strg/Cmd→toggle · Alt→remove · sonst replace', () => {
  assert.equal(aufloeseAuswahlmodus({}), 'replace');
  assert.equal(aufloeseAuswahlmodus({ shiftKey: true }), 'add');
  assert.equal(aufloeseAuswahlmodus({ ctrlKey: true }), 'toggle');
  assert.equal(aufloeseAuswahlmodus({ metaKey: true }), 'toggle', 'Cmd gilt wie Strg — der Planer läuft auf beiden Plattformen');
  assert.equal(aufloeseAuswahlmodus({ altKey: true }), 'remove');
});

test('K4: bei mehreren Tasten entscheidet eine feste Reihenfolge, nicht der Zufall', () => {
  // Alt vor Strg vor Shift — „entfernen" ist die eindeutigste Absicht.
  assert.equal(aufloeseAuswahlmodus({ altKey: true, shiftKey: true }), 'remove');
  assert.equal(aufloeseAuswahlmodus({ ctrlKey: true, shiftKey: true }), 'toggle');
});

// --- K3: je Modus ein Fall ----------------------------------------------------------------------
const A: Auswahlstand = { ids: ['a'], primaerId: 'a' };

test('K3 replace: ersetzt die Auswahl und macht den Treffer zum Primärobjekt', () => {
  assert.deepEqual(wendeAuswahlAn(A, 'b', 'replace'), { ids: ['b'], primaerId: 'b' });
  assert.deepEqual(A, { ids: ['a'], primaerId: 'a' }, 'die Eingabe bleibt unberührt');
});

test('K3 add: hängt an — doppeltes Hinzufügen erzeugt KEINE Dublette', () => {
  const zwei = wendeAuswahlAn(A, 'b', 'add');
  assert.deepEqual(zwei, { ids: ['a', 'b'], primaerId: 'b' });
  const nochmal = wendeAuswahlAn(zwei, 'b', 'add');
  assert.deepEqual(nochmal.ids, ['a', 'b'], 'b steht genau einmal');
  assert.equal(nochmal.primaerId, 'b');
});

test('K3 remove: nimmt heraus; ein nicht enthaltenes Objekt ändert nichts', () => {
  const zwei: Auswahlstand = { ids: ['a', 'b'], primaerId: 'b' };
  assert.deepEqual(wendeAuswahlAn(zwei, 'a', 'remove'), { ids: ['b'], primaerId: 'b' });
  assert.deepEqual(wendeAuswahlAn(zwei, 'x', 'remove'), { ids: ['a', 'b'], primaerId: 'b' });
});

test('K3 toggle: drin ⇒ raus, draußen ⇒ rein', () => {
  const zwei: Auswahlstand = { ids: ['a', 'b'], primaerId: 'b' };
  assert.deepEqual(wendeAuswahlAn(zwei, 'b', 'toggle'), { ids: ['a'], primaerId: 'a' });
  assert.deepEqual(wendeAuswahlAn(zwei, 'c', 'toggle'), { ids: ['a', 'b', 'c'], primaerId: 'c' });
});

test('K3/Kante 3: wird das Primärobjekt abgewählt, rückt das zuletzt verbliebene nach', () => {
  const drei: Auswahlstand = { ids: ['a', 'b', 'c'], primaerId: 'c' };
  assert.deepEqual(wendeAuswahlAn(drei, 'c', 'remove'), { ids: ['a', 'b'], primaerId: 'b' });
  // ein anderes zu entfernen lässt das Primärobjekt stehen
  assert.deepEqual(wendeAuswahlAn(drei, 'a', 'remove'), { ids: ['b', 'c'], primaerId: 'c' });
  // die letzte Entfernung führt auf null, nicht auf undefined
  assert.deepEqual(wendeAuswahlAn({ ids: ['a'], primaerId: 'a' }, 'a', 'remove'), { ids: [], primaerId: null });
});

// --- Kante 5: Klick ins Leere -------------------------------------------------------------------
test('Kante 5: leerer Klick hebt auf — MIT Modifikator bleibt die Mehrfachauswahl stehen', () => {
  const zwei: Auswahlstand = { ids: ['a', 'b'], primaerId: 'b' };
  assert.deepEqual(klickInsLeere(zwei, {}), LEERE_AUSWAHL);
  for (const mod of [{ shiftKey: true }, { ctrlKey: true }, { metaKey: true }, { altKey: true }]) {
    assert.deepEqual(klickInsLeere(zwei, mod), zwei, `${JSON.stringify(mod)}: die Auswahl darf nicht verloren gehen`);
  }
});

// --- K5: Hit-Test -------------------------------------------------------------------------------
const K = (id: string, distanz: number, zeichenreihenfolge: number, rest: Partial<TrefferKandidat> = {}): TrefferKandidat =>
  ({ id, distanz, zeichenreihenfolge, sichtbar: true, ...rest });

test('K5: was oben liegt, gewinnt — auch wenn es weiter weg ist', () => {
  const treffer = besterTreffer([K('unten', 1, 1), K('oben', 40, 9)], 100);
  assert.equal(treffer?.id, 'oben');
});

test('K5: bei gleicher Zeichenreihenfolge gewinnt das nähere', () => {
  const treffer = besterTreffer([K('fern', 40, 5), K('nah', 3, 5)], 100);
  assert.equal(treffer?.id, 'nah');
});

test('K5: unsichtbar und nicht wählbar fallen heraus — gesperrt NICHT (Kante 1)', () => {
  const liste = [K('unsichtbar', 1, 9, { sichtbar: false }), K('hilfslinie', 1, 8, { waehlbar: false }), K('gesperrt', 5, 1, { gesperrt: true })];
  const ids = trefferInReihenfolge(liste, 100).map((t) => t.id);
  assert.deepEqual(ids, ['gesperrt'], 'ein gesperrtes Objekt muss wählbar bleiben — man muss sehen, was sperrt');
});

test('K5: die Toleranz wirkt — außerhalb gibt es keinen Treffer', () => {
  assert.equal(besterTreffer([K('weit', 150, 9)], 100), null);
  assert.equal(besterTreffer([K('knapp', 100, 9)], 100)?.id, 'knapp', 'genau auf der Grenze zählt als Treffer');
  assert.equal(besterTreffer([], 100), null, 'keine Kandidaten ⇒ null, kein Wurf');
});

test('K5: die Pixel-Toleranz wird über den Zoom in Weltmaß gerechnet', () => {
  assert.equal(toleranzInWelt(6, 0.12), 50);
  assert.equal(toleranzInWelt(6, 0), 6, 'Zoom 0 wirft nicht und teilt nicht durch null');
});

// --- K6: Darstellungszustand --------------------------------------------------------------------
test('K6: alle fünf Zustände ergeben eine Darstellung — und nur Token, keine Farbwerte', () => {
  const faelle = [
    { name: 'ausgewaehlt', e: { ausgewaehlt: true } },
    { name: 'primaer', e: { primaer: true, ausgewaehlt: true } },
    { name: 'ueberfahren', e: { ueberfahren: true } },
    { name: 'gesperrt', e: { gesperrt: true } },
    { name: 'ungueltig', e: { ungueltig: true } },
  ];
  for (const f of faelle) {
    const d = aufloeseDarstellung(f.e);
    assert.ok(d.strichstaerke > 0, `${f.name}: Strichstärke fehlt`);
    assert.ok(d.deckkraft > 0 && d.deckkraft <= 1, `${f.name}: Deckkraft unbrauchbar`);
    assert.match(d.konturToken, /^(brandInk|errInk|muted|ink)$/, `${f.name}: kein Token`);
  }
  const quelle = readFileSync(join(hier, '../app/tools/auswahlDarstellung.ts'), 'utf8');
  assert.doesNotMatch(ohneKommentare(quelle), /#[0-9a-fA-F]{3,8}\b|rgba?\(/, 'roher Farbwert in der Darstellungsregel');
});

test('K6: die Rangfolge stimmt — ungültig schlägt ausgewählt, Griffe nur am Primärobjekt', () => {
  assert.equal(aufloeseDarstellung({ ausgewaehlt: true, ungueltig: true }).konturToken, 'errInk');
  assert.equal(aufloeseDarstellung({ primaer: true }).griffe, true);
  assert.equal(aufloeseDarstellung({ ausgewaehlt: true }).griffe, false, 'fünf Anfasser an fünf Objekten wären fünfmal dieselbe Geste');
  assert.equal(aufloeseDarstellung({ primaer: true, gesperrt: true }).griffe, false, 'an Gesperrtem gibt es nichts zu ziehen');
  assert.equal(aufloeseDarstellung({ gesperrt: true }).schloss, true);
  // Kante 1: ein gewähltes gesperrtes Objekt wird NICHT gedimmt — man will genau es ansehen.
  assert.equal(aufloeseDarstellung({ gesperrt: true }).deckkraft < 1, true);
  assert.equal(aufloeseDarstellung({ gesperrt: true, ausgewaehlt: true }).deckkraft, 1);
});

// --- K8 / Kante 4: Mehrfach-Ansicht -------------------------------------------------------------
const knoten = (id: string, type: string, locked = false): SceneNode => ({
  id, type, levelId: 'eg', visible: true, locked, tags: [],
  createdAt: '2026-07-25T00:00:00Z', updatedAt: '2026-07-25T00:00:00Z',
} as unknown as SceneNode);

test('K8/Kante 4: gemischte Auswahl ⇒ Anzahl je Typ, deutsch und mit Plural', () => {
  const nodes = [knoten('w1', 'wall'), knoten('w2', 'wall'), knoten('f1', 'window'), knoten('d1', 'roof')];
  const u = mehrfachUebersicht(['w1', 'w2', 'f1', 'd1'], nodes);
  assert.equal(u.gesamt, 4);
  assert.deepEqual(u.typen.map((t) => t.bezeichnung), ['2 Wände', '1 Dach', '1 Fenster']);
  assert.equal(u.gesperrt, 0);
});

test('K8: gesperrte werden gezählt, unbekannte ids übergangen — keine „0 undefined"-Zeile', () => {
  const nodes = [knoten('w1', 'wall', true), knoten('w2', 'wall')];
  const u = mehrfachUebersicht(['w1', 'w2', 'gibt-es-nicht'], nodes);
  assert.equal(u.gesamt, 2);
  assert.equal(u.gesperrt, 1);
  assert.deepEqual(u.typen, [{ typ: 'wall', anzahl: 2, bezeichnung: '2 Wände' }]);
});

test('K8: ein unbekannter Typ wird nicht erfunden, sondern roh gezeigt', () => {
  assert.equal(benenne('wall', 1), 'Wand');
  assert.equal(benenne('wall', 2), 'Wände');
  assert.equal(benenne('gibt-es-nicht', 2), 'gibt-es-nicht');
});

// --- K7: die fünf `length === 1`-Stellen sind aufgelöst ------------------------------------------
test('K7: keine Panel-Auswertung hängt mehr an `selectedNodeIds.length === 1`', () => {
  assert.doesNotMatch(appQuelle, /selectedNodeIds\.length === 1/, 'die Mehrfachauswahl wäre wieder blind');
  // stattdessen führt das Primärobjekt
  assert.match(appQuelle, /const primaerId = useHausplanerStore\(\(s\) => s\.primaerId\);/);
  assert.match(appQuelle, /const selectedNode = primaerId \?/);
});

test('K7: es gibt EINE Stelle, an der ein Klick zur Auswahl wird — keine zweite Auswahl-Logik', () => {
  assert.equal((appQuelle.match(/aufloeseAuswahlmodus\(/g) ?? []).length, 1, 'die Ableitung steht genau einmal');
  assert.equal((appQuelle.match(/wendeAuswahlAn\(/g) ?? []).length, 1);
  // kein Renderer-Zweig setzt die Auswahl noch direkt auf einen einzelnen Knoten
  assert.doesNotMatch(appQuelle, /selectNodes\(\[[a-z]+\.id\]\)/, 'ein Knoten setzt die Auswahl nicht mehr selbst');
});

// --- Das Label ----------------------------------------------------------------------------------
test('das Werkzeug heißt sichtbar „Markieren" — id und Kürzel bleiben', () => {
  const t = toolNach('auswahl');
  assert.equal(t?.label, 'Markieren');
  assert.equal(t?.id, 'auswahl');
  assert.equal(t?.shortcut, 'V');
});
