/**
 * AUF-48 Scheibe 1 — die sieben reinen Funktionen, jetzt verriegelt.
 *
 * **Warum diese Datei über den Auftrag hinaus Inhalt hat:** Der geforderte Gegenbeweis zu K-03 hat
 * einen **meldepflichtigen Nebenbefund** aufgedeckt, den das Blatt selbst vorsieht
 * (*„Wird keine rot, ist die Fläche unverriegelt"*). Gemessen: **alle sieben Funktionen wurden
 * einzeln mutiert — keine der 1440 Unit- und 29 DOM-Zusagen wurde rot.** Sie waren in
 * `HausplanerApp.tsx` vollständig ungeprüft und wären es nach dem Umzug geblieben.
 *
 * **Das ist genau der Wert der Zerlegung:** in einer 2500-Zeilen-Datei war diese Lücke unsichtbar;
 * als eigenes Modul ist sie in einer Zeile prüfbar. *Die Scheibe verschiebt nicht nur Code, sie
 * macht ihn erstmals messbar.*
 *
 * **Die Fachlogik steht NICHT zur Debatte.** Diese Zusagen halten fest, was die Funktionen HEUTE
 * tun — sie sind beim Umzug Zeichen für Zeichen unverändert geblieben. Wer das Verhalten ändern
 * will, ändert es bewusst und zieht diese Datei mit.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  svgWrap, werkzeugIcon, opIcon, uuid, istWand, istOeffnung, lotAufWand,
} from '../app/reineHelfer';
import type { OpeningNode, SceneNode, WallNode } from '../domain/scene.types';

// --- Fixtures ---------------------------------------------------------------------------------

const BASIS = {
  levelId: 'L', visible: true, locked: false, tags: [],
  createdAt: '2026-07-30T00:00:00.000Z', updatedAt: '2026-07-30T00:00:00.000Z',
} as const;

/** Eine Wand von (0,0) nach (1000,0) — waagerecht, damit das Lot von Hand nachrechenbar ist. */
const wand = (start = { x: 0, y: 0 }, end = { x: 1000, y: 0 }): WallNode => ({
  ...BASIS, id: 'w1', type: 'wall', start, end, thickness: 240, height: 2500,
});

const oeffnung = (typ: OpeningNode['type']): OpeningNode => ({
  ...BASIS, id: 'o1', type: typ, hostWallId: 'w1', offset: 100, width: 1000, height: 2000, sillHeight: 0,
} as OpeningNode);

// --- istWand / istOeffnung: die Typwächter ------------------------------------------------------

test('istWand erkennt genau `wall` — und nichts sonst', () => {
  assert.equal(istWand(wand()), true);
  for (const t of ['window', 'door', 'opening'] as const) {
    assert.equal(istWand(oeffnung(t) as SceneNode), false, `${t} wurde als Wand gelesen`);
  }
});

test('istOeffnung erkennt alle DREI Öffnungsarten — und keine Wand', () => {
  // Die Dreiheit ist der Punkt: fiele eine der drei aus der Prüfung, verschwänden Türen oder
  // Durchbrüche lautlos aus jeder Auswertung, die über diesen Wächter läuft.
  for (const t of ['window', 'door', 'opening'] as const) {
    assert.equal(istOeffnung(oeffnung(t) as SceneNode), true, `${t} wurde nicht als Öffnung erkannt`);
  }
  assert.equal(istOeffnung(wand()), false, 'eine Wand wurde als Öffnung gelesen');
});

// --- lotAufWand: die einzige echte Rechnung der sieben -------------------------------------------

test('lotAufWand: der Abstand ist der senkrechte, der Offset die Strecke entlang der Wand', () => {
  // Punkt (300, 400) auf eine Wand von (0,0) nach (1000,0): Fußpunkt (300,0).
  const { abstand, offset } = lotAufWand({ x: 300, y: 400 }, wand());
  assert.equal(abstand, 400);
  assert.equal(offset, 300);
});

test('lotAufWand: der Abstand ist NIE negativ — er ist eine Länge', () => {
  // Von beiden Seiten derselbe Wert. **Diese Zusage fängt genau die Mutation, mit der der
  // Gegenbeweis zu K-03 gefahren wurde** (Vorzeichen gedreht) — vorher fing sie niemand.
  const oben = lotAufWand({ x: 300, y: 400 }, wand()).abstand;
  const unten = lotAufWand({ x: 300, y: -400 }, wand()).abstand;
  assert.equal(oben, 400);
  assert.equal(unten, 400);
  assert.ok(oben >= 0 && unten >= 0, 'ein Abstand kann nicht negativ sein');
});

test('lotAufWand klemmt am Wandende — kein Fußpunkt jenseits der Strecke', () => {
  // Das ist die Eigenschaft, die eine Öffnung davor bewahrt, hinter dem Wandende zu landen:
  // `t` wird auf [0,1] geklemmt. Punkt (2000,0) liegt 1000 mm HINTER dem Ende (1000,0).
  const hinterEnde = lotAufWand({ x: 2000, y: 0 }, wand());
  assert.equal(hinterEnde.offset, 1000, 'der Offset läuft über die Wandlänge hinaus');
  assert.equal(hinterEnde.abstand, 1000, 'gemessen wird zum Endpunkt, nicht zur verlängerten Geraden');

  const vorAnfang = lotAufWand({ x: -500, y: 0 }, wand());
  assert.equal(vorAnfang.offset, 0);
  assert.equal(vorAnfang.abstand, 500);
});

test('lotAufWand: eine Wand der Länge 0 ergibt unendlichen Abstand statt einer Division durch 0', () => {
  // Die Kante, die das Modul selbst behandelt. Ohne sie käme `NaN` heraus — und `NaN` gewinnt
  // jeden Kleiner-Vergleich nicht, wodurch eine entartete Wand still zur nächstgelegenen würde.
  const { abstand, offset } = lotAufWand({ x: 5, y: 5 }, wand({ x: 0, y: 0 }, { x: 0, y: 0 }));
  assert.equal(abstand, Number.POSITIVE_INFINITY);
  assert.equal(offset, 0);
});

test('lotAufWand rechnet auch auf einer schrägen Wand richtig', () => {
  // 3-4-5-Dreieck: Wand (0,0)→(0,1000) ist senkrecht; Punkt (300,400) hat Abstand 300, Offset 400.
  const { abstand, offset } = lotAufWand({ x: 300, y: 400 }, wand({ x: 0, y: 0 }, { x: 0, y: 1000 }));
  assert.equal(abstand, 300);
  assert.equal(offset, 400);
});

// --- uuid ---------------------------------------------------------------------------------------

test('uuid liefert bei jedem Aufruf einen anderen Wert', () => {
  const menge = new Set(Array.from({ length: 200 }, () => uuid()));
  assert.equal(menge.size, 200, 'zwei Aufrufe haben dieselbe id geliefert');
});

test('uuid liefert eine nicht-leere Zeichenkette ohne Leerraum', () => {
  const id = uuid();
  assert.equal(typeof id, 'string');
  assert.ok(id.length >= 8, `zu kurz für eine id: „${id}"`);
  assert.doesNotMatch(id, /\s/, 'eine id mit Leerraum bricht jede Verwendung als Schlüssel');
});

// --- Die Icon-Funktionen: Form, nicht Aussehen ---------------------------------------------------

test('svgWrap liefert ein <svg> mit den festen Maßen und dem übergebenen Inhalt', () => {
  const el = svgWrap(null) as React.ReactElement<Record<string, unknown>>;
  assert.equal(el.type, 'svg');
  assert.equal(el.props.width, 18);
  assert.equal(el.props.height, 18);
  assert.equal(el.props.viewBox, '0 0 24 24');
  // `currentColor` ist der Grund, warum ein Icon die Farbe seines Knopfes erbt — eine feste Farbe
  // hier würde jeden Zustand (aktiv/gesperrt) unsichtbar machen.
  assert.equal(el.props.stroke, 'currentColor');
  assert.equal(el.props.fill, 'none');
});

/** Das gezeichnete Innere eines Icons als vergleichbare Zeichenkette. */
const inhalt = (el: React.ReactElement): string =>
  JSON.stringify((el as React.ReactElement<{ children: unknown }>).props.children);

test('werkzeugIcon: jedes der sieben Werkzeuge hat ein EIGENES Zeichen — keine zwei gleich', () => {
  const ids = ['auswahl', 'wand', 'fenster', 'tuer', 'dach', 'decke', 'treppe'];
  const gezeichnet = ids.map((id) => inhalt(werkzeugIcon(id)));
  assert.equal(new Set(gezeichnet).size, ids.length, 'zwei Werkzeuge teilen sich dasselbe Zeichen');
});

test('werkzeugIcon: KEIN bekanntes Werkzeug fällt in den Platzhalter-Zweig', () => {
  // **Nachgeschärft, nachdem der eigene Gegenbeweis die Lücke zeigte.** Die Zusage oben prüft nur
  // Verschiedenheit — ein Werkzeug, das versehentlich in `default` fällt, bekommt den Kreis und
  // bliebe *eindeutig*, also grün. Genau diese Mutation lief durch. **Das hier ist die Aussage,
  // die der Kommentar oben behauptet hat**: ein Werkzeug ohne eigenes Zeichen ist ein Fehler,
  // auch wenn kein zweites denselben Kreis trägt.
  const platzhalter = inhalt(werkzeugIcon('es-gibt-mich-nicht'));
  for (const id of ['auswahl', 'wand', 'fenster', 'tuer', 'dach', 'decke', 'treppe']) {
    assert.notEqual(inhalt(werkzeugIcon(id)), platzhalter, `${id} zeichnet den Platzhalter statt eines eigenen Zeichens`);
  }
});

test('werkzeugIcon: ein unbekanntes Werkzeug bekommt den Platzhalter, keinen Absturz', () => {
  const el = werkzeugIcon('gibt-es-nicht') as React.ReactElement;
  assert.equal(el.type, 'svg');
});

/** Die 17 Bedien-Icons — einmal benannt, von beiden Zusagen benutzt. */
const OP_NAMEN = [
  'zoom-in', 'zoom-out', 'zoom-reset', 'einpassen', 'grid', 'fang', 'dup', 'del',
  'mirror-h', 'mirror-v', 'drehen', 'messen', 'bemassung', 'export', 'undo', 'redo', 'pdf',
];

test('opIcon: alle 17 Bedien-Icons sind verschieden — inklusive undo/redo aus AUF-70', () => {
  const gezeichnet = OP_NAMEN.map((n) => inhalt(opIcon(n)));
  assert.equal(new Set(gezeichnet).size, OP_NAMEN.length, 'zwei Bedien-Icons teilen sich dasselbe Zeichen');
});

test('opIcon: KEIN bekanntes Bedien-Icon fällt in den Platzhalter-Zweig', () => {
  // Dieselbe Nachschärfung wie bei `werkzeugIcon` — aus demselben gemessenen Grund.
  const platzhalter = inhalt(opIcon('es-gibt-mich-nicht'));
  for (const n of OP_NAMEN) {
    assert.notEqual(inhalt(opIcon(n)), platzhalter, `${n} zeichnet den Platzhalter statt eines eigenen Zeichens`);
  }
});

test('opIcon: ein unbekannter Name bekommt den Platzhalter, keinen Absturz', () => {
  const el = opIcon('gibt-es-nicht') as React.ReactElement;
  assert.equal(el.type, 'svg');
});
