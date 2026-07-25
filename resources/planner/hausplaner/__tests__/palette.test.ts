/**
 * Dashboard v2.5 — die Command-Palette liest die Registry und die Activation-Engine, sonst nichts.
 * Abnahmekriterium Batch 2, Punkt 8: Anzahl = alleTools().length, jeder deaktivierte Eintrag hat
 * `grund !== null`, der Filter trifft `label` UND `id`, aktivierbare stehen vor deaktivierten.
 *
 * Punkt 9 (Gegen-Beweis): wird in `palette.ts` die `resolveToolState`-Abfrage durch ein hart
 * gesetztes `enabled: true` ersetzt, MÜSSEN die Tests „deaktivierte Einträge tragen einen Grund"
 * und „aktivierbare stehen vor deaktivierten" rot werden.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { palettenEintraege, PALETTE_LEER } from '../app/dashboard/palette';
import { alleTools, WORKSPACE_ARCHITEKTUR } from '../app/tools/toolRegistry';
import { baueAktivierungsKontext } from '../app/tools/toolContext';

/** Editor-Alltag: Architektur, 2D, nichts ausgewählt ⇒ Lösch-/Duplizier-Aktion deaktiviert. */
const ohneAuswahl = baueAktivierungsKontext({
  workspace: WORKSPACE_ARCHITEKTUR, view: '2d', selectionTypes: [], permissions: ['Hausplaner,update'],
});
/** Mit Auswahl einer Wand ⇒ alle Werkzeuge und beide Aktionen aktiv. */
const mitWand = baueAktivierungsKontext({
  workspace: WORKSPACE_ARCHITEKTUR, view: '2d', selectionTypes: ['wall'], permissions: ['Hausplaner,update'],
});
/** 3D: dort wird nicht gezeichnet ⇒ die Zeichenwerkzeuge sind deaktiviert. */
const in3d = baueAktivierungsKontext({
  workspace: WORKSPACE_ARCHITEKTUR, view: '3d', selectionTypes: [], permissions: ['Hausplaner,update'],
});

test('leerer Filter zeigt ALLE Werkzeuge der Registry — kein eigener Bestand', () => {
  const e = palettenEintraege(ohneAuswahl, '');
  assert.equal(e.length, alleTools().length);
  assert.deepEqual([...e].map((x) => x.id).sort(), alleTools().map((t) => t.id).sort());
});

test('Leerraum-Filter zählt als leerer Filter', () => {
  assert.equal(palettenEintraege(ohneAuswahl, '   ').length, alleTools().length);
});

test('jeder deaktivierte Eintrag trägt einen Grund als Text (nie nur ausgegraut)', () => {
  for (const kontext of [ohneAuswahl, in3d, mitWand]) {
    const e = palettenEintraege(kontext, '');
    for (const x of e.filter((y) => !y.enabled)) {
      assert.notEqual(x.grund, null, `${x.id}: deaktiviert ohne Grund`);
      assert.ok((x.grund ?? '').trim().length > 0, `${x.id}: leerer Grund`);
    }
    for (const x of e.filter((y) => y.enabled)) {
      assert.equal(x.grund, null, `${x.id}: aktiv, aber mit Grund`);
    }
  }
});

test('ohne Auswahl sind mindestens die auswahlgebundenen Aktionen deaktiviert', () => {
  const e = palettenEintraege(ohneAuswahl, '');
  const aus = e.filter((x) => !x.enabled).map((x) => x.id);
  assert.ok(aus.includes('loeschen'), 'Löschen braucht eine Auswahl');
  assert.ok(aus.includes('duplizieren'), 'Duplizieren braucht eine Auswahl');
});

test('in 3D sind die Zeichenwerkzeuge deaktiviert, Auswahl bleibt aktiv', () => {
  const e = palettenEintraege(in3d, '');
  const nach = new Map(e.map((x) => [x.id, x]));
  assert.equal(nach.get('auswahl')?.enabled, true);
  assert.equal(nach.get('wand')?.enabled, false);
  assert.match(nach.get('wand')?.grund ?? '', /Ansicht/);
});

test('aktivierbare Einträge stehen VOR den deaktivierten', () => {
  const e = palettenEintraege(ohneAuswahl, '');
  const ersteAus = e.findIndex((x) => !x.enabled);
  assert.ok(ersteAus > 0, 'es muss aktive und inaktive Einträge geben');
  assert.ok(e.slice(0, ersteAus).every((x) => x.enabled), 'vor dem ersten inaktiven nur aktive');
  assert.ok(e.slice(ersteAus).every((x) => !x.enabled), 'nach dem ersten inaktiven nur inaktive');
});

test('innerhalb beider Blöcke bleibt die Registry-Reihenfolge erhalten', () => {
  const reihenfolge = alleTools().map((t) => t.id);
  const e = palettenEintraege(ohneAuswahl, '');
  const idx = (ids: string[]): number[] => ids.map((i) => reihenfolge.indexOf(i));
  const aktiv = idx(e.filter((x) => x.enabled).map((x) => x.id));
  const inaktiv = idx(e.filter((x) => !x.enabled).map((x) => x.id));
  assert.deepEqual(aktiv, [...aktiv].sort((a, b) => a - b));
  assert.deepEqual(inaktiv, [...inaktiv].sort((a, b) => a - b));
});

test('Filter trifft das LABEL, ohne Groß-/Kleinschreibung', () => {
  assert.deepEqual(palettenEintraege(ohneAuswahl, 'tür').map((x) => x.id), ['tuer']);
  assert.deepEqual(palettenEintraege(ohneAuswahl, 'TÜR').map((x) => x.id), ['tuer']);
});

test('Filter trifft die ID — auch wenn das Label sie nicht enthält', () => {
  // „tuer" steht nur in der id, das Label heißt „Tür" (mit Umlaut).
  const e = palettenEintraege(ohneAuswahl, 'tuer');
  assert.deepEqual(e.map((x) => x.id), ['tuer']);
  assert.ok(!e[0].label.toLowerCase().includes('tuer'), 'sonst prüft der Test nicht die id');
});

test('Kante 7: Filter ohne Treffer ⇒ leere Liste, dazu der wörtliche Leerzustand', () => {
  assert.deepEqual(palettenEintraege(ohneAuswahl, 'gibtesnicht'), []);
  assert.equal(PALETTE_LEER, 'Kein Werkzeug passt zu dieser Eingabe.');
  assert.ok(!/keine daten/i.test(PALETTE_LEER));
});

test('Tastenkürzel werden aus der Registry durchgereicht, nicht neu erfunden', () => {
  const nach = new Map(palettenEintraege(ohneAuswahl, '').map((x) => [x.id, x.shortcut]));
  for (const t of alleTools()) {
    assert.equal(nach.get(t.id), t.shortcut, `${t.id}: Kürzel weicht von der Registry ab`);
  }
});

test('mit Auswahl kippt genau der Zustand, den die Engine kippt — keine zweite Logik', () => {
  const ohne = new Map(palettenEintraege(ohneAuswahl, '').map((x) => [x.id, x.enabled]));
  const mit = new Map(palettenEintraege(mitWand, '').map((x) => [x.id, x.enabled]));
  assert.equal(ohne.get('loeschen'), false);
  assert.equal(mit.get('loeschen'), true);
  assert.equal(mit.get('duplizieren'), true);
});
