/**
 * Dashboard v2.3 — der Projektbaum ist eine reine Funktion, also prüfbar.
 * Abnahmekriterium Batch 2, Punkt 8: leere Szene ⇒ [], gemischte Knoten ⇒ erwartete Gruppen in
 * FESTER Reihenfolge, leere Gruppen fehlen. Dazu die Kanten 1 und 6 aus §6.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  projektBaum,
  GRUPPEN_GRENZE,
  GRUPPEN_REIHENFOLGE,
  PROJEKTBAUM_LEER,
} from '../app/dashboard/projektBaum';
import type { ObjectNode, RoofNode, SceneNode, WallNode, ZoneNode } from '../domain/scene.types';

const LEVEL = { id: 'eg' };
const basis = { levelId: 'eg', visible: true, locked: false, tags: [], createdAt: 'x', updatedAt: 'x' };

function wand(id: string, name?: string): WallNode {
  return { ...basis, id, type: 'wall', start: { x: 0, y: 0 }, end: { x: 1000, y: 0 }, thickness: 240, height: 2500, ...(name ? { name } : {}) } as WallNode;
}
function oeffnung(id: string, type: 'window' | 'door' | 'opening'): SceneNode {
  return { ...basis, id, type, hostWallId: 'w1', offsetFromWallStart: 100, width: 1000, height: 1300, sillHeight: 900 } as SceneNode;
}
function objekt(id: string, objectType: ObjectNode['objectType']): ObjectNode {
  return {
    ...basis, id, type: 'object', objectType, catalogItemId: 'k1',
    transform: { position: { x: 0, y: 0, z: 0 }, rotation: { x: 0, y: 0, z: 0 }, scale: { x: 1, y: 1, z: 1 } },
    parameters: {},
  } as ObjectNode;
}
function zone(id: string, zoneType: ZoneNode['zoneType']): ZoneNode {
  return { ...basis, id, type: 'zone', zoneType, polygon: [], derived: true, parameters: {} } as ZoneNode;
}
function dach(id: string, levelId = 'eg'): RoofNode {
  return { ...basis, levelId, id, type: 'roof' } as unknown as RoofNode;
}

test('leere Szene ⇒ leerer Baum (keine leeren Kästen)', () => {
  assert.deepEqual(projektBaum([], [], LEVEL), []);
  assert.deepEqual(projektBaum([], undefined, LEVEL), []);
});

test('Kante 1: kein Geschoss ⇒ [] statt Wurf', () => {
  assert.deepEqual(projektBaum([wand('w1')], [dach('r1')], null), []);
});

test('gemischte Knoten ⇒ alle sechs Gruppen in fester Reihenfolge', () => {
  const nodes: SceneNode[] = [
    zone('z1', 'room'),
    objekt('o1', 'radiator'),
    objekt('t1', 'stair'),
    oeffnung('f1', 'window'),
    wand('w1'),
  ];
  const baum = projektBaum(nodes, [dach('r1')], LEVEL);
  assert.deepEqual(baum.map((g) => g.gruppe), [...GRUPPEN_REIHENFOLGE]);
  assert.deepEqual(baum.map((g) => g.anzahl), [1, 1, 1, 1, 1, 1]);
});

test('leere Gruppen werden weggelassen, nicht als leerer Kasten geführt', () => {
  const baum = projektBaum([wand('w1'), wand('w2')], [], LEVEL);
  assert.deepEqual(baum.map((g) => g.gruppe), ['Wände']);
  assert.equal(baum[0].anzahl, 2);
  assert.equal(baum[0].eingeklappt, false);
});

test('Treppe (ObjectNode objectType=stair) landet in „Treppen", nicht in „Objekte"', () => {
  const baum = projektBaum([objekt('t1', 'stair'), objekt('o1', 'wallbox')], [], LEVEL);
  assert.deepEqual(baum.map((g) => g.gruppe), ['Treppen', 'Objekte']);
  assert.equal(baum[0].eintraege[0].typ, 'stair');
  assert.equal(baum[1].eintraege[0].typ, 'object');
});

test('nur Knoten des aktiven Geschosses — Dächer ebenso', () => {
  const fremd = { ...wand('w9'), levelId: 'og' } as WallNode;
  const baum = projektBaum([wand('w1'), fremd], [dach('r1', 'og')], LEVEL);
  assert.deepEqual(baum.map((g) => g.gruppe), ['Wände']);
  assert.equal(baum[0].anzahl, 1);
  assert.equal(baum[0].eintraege[0].id, 'w1');
});

test('Beschriftung: eigener Name gewinnt, sonst laufende Nummer je Gruppe', () => {
  const baum = projektBaum([wand('w1'), wand('w2', 'Giebelwand Nord'), wand('w3')], [], LEVEL);
  assert.deepEqual(baum[0].eintraege.map((e) => e.label), ['Wand 1', 'Giebelwand Nord', 'Wand 3']);
});

test('Öffnungen bekommen ihre fachliche Basis-Beschriftung', () => {
  const baum = projektBaum([oeffnung('f1', 'window'), oeffnung('d1', 'door'), oeffnung('x1', 'opening')], [], LEVEL);
  assert.deepEqual(baum[0].eintraege.map((e) => e.label), ['Fenster 1', 'Tür 2', 'Öffnung 3']);
});

test('jeder Eintrag trägt eine id — sonst wäre der Klick auf selectNodes nicht anschließbar', () => {
  const baum = projektBaum([wand('w1'), oeffnung('f1', 'window'), zone('z1', 'room')], [dach('r1')], LEVEL);
  for (const g of baum) {
    for (const e of g.eintraege) {
      assert.ok(e.id.length > 0, `${g.gruppe}: Eintrag ohne id`);
      assert.ok(e.label.trim().length > 0, `${g.gruppe}: Eintrag ohne Beschriftung`);
    }
  }
});

test('Kante 6: ab GRUPPEN_GRENZE klappt die Gruppe zu — Kopf + Anzahl, keine Einträge', () => {
  const viele = Array.from({ length: GRUPPEN_GRENZE }, (_, i) => wand(`w${i}`));
  const baum = projektBaum(viele, [], LEVEL);
  assert.equal(baum[0].anzahl, GRUPPEN_GRENZE);
  assert.equal(baum[0].eingeklappt, true);
  assert.deepEqual(baum[0].eintraege, []);
});

test('knapp unter der Grenze bleibt die Gruppe offen', () => {
  const fast = Array.from({ length: GRUPPEN_GRENZE - 1 }, (_, i) => wand(`w${i}`));
  const baum = projektBaum(fast, [], LEVEL);
  assert.equal(baum[0].eingeklappt, false);
  assert.equal(baum[0].eintraege.length, GRUPPEN_GRENZE - 1);
});

test('Leerzustand ist ausgesprochen, nicht „keine Daten"', () => {
  assert.equal(PROJEKTBAUM_LEER, 'Noch keine Bauteile in diesem Geschoss.');
  assert.ok(!/keine daten/i.test(PROJEKTBAUM_LEER));
});
