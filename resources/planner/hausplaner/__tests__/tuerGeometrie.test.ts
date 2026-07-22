/**
 * Türblatt-Geometrie (P2b-4). Reine, deterministische mm-Geometrie im lokalen
 * Öffnungs-Frame (+x entlang Wand, +y linke Normale).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { tuerBlattGeometrie } from '../geometry/wallGeometry';

const W = 900;

test('links/innen: Angel am −x-Pfosten, Bogen von gegenüberliegendem Pfosten zur Blattspitze (+y)', () => {
  const g = tuerBlattGeometrie(W, 'links', 'innen');
  assert.deepEqual(g.angelpunkt, { x: -450, y: 0 });
  assert.deepEqual(g.blattEnde, { x: -450, y: 900 });
  // Bogen startet am geschlossenen Pfosten (+450,0) und endet an der Blattspitze.
  assert.deepEqual(g.bogen[0], { x: 450, y: 0 });
  assert.deepEqual(g.bogen[g.bogen.length - 1], { x: -450, y: 900 });
});

test('rechts spiegelt die Angel-Seite (Angel am +x-Pfosten)', () => {
  const g = tuerBlattGeometrie(W, 'rechts', 'innen');
  assert.deepEqual(g.angelpunkt, { x: 450, y: 0 });
  assert.deepEqual(g.blattEnde, { x: 450, y: 900 });
  assert.deepEqual(g.bogen[0], { x: -450, y: 0 });
  assert.deepEqual(g.bogen[g.bogen.length - 1], { x: 450, y: 900 });
});

test('außen spiegelt die Aufschlagseite (Blatt und Bogen nach −y)', () => {
  const g = tuerBlattGeometrie(W, 'links', 'aussen');
  assert.deepEqual(g.angelpunkt, { x: -450, y: 0 });
  assert.deepEqual(g.blattEnde, { x: -450, y: -900 });
  assert.deepEqual(g.bogen[g.bogen.length - 1], { x: -450, y: -900 });
});

test('Bogenpunkte liegen alle auf Radius = Breite um die Angel (±1 mm Rundung)', () => {
  const g = tuerBlattGeometrie(W, 'rechts', 'aussen');
  for (const p of g.bogen) {
    const r = Math.hypot(p.x - g.angelpunkt.x, p.y - g.angelpunkt.y);
    assert.ok(Math.abs(r - W) <= 1.5, `Radius ${r} weicht von ${W} ab`);
  }
});

test('Bogen hat segmente+1 Punkte und alle Ecken sind ganzzahlig', () => {
  const g = tuerBlattGeometrie(W, 'links', 'innen', 8);
  assert.equal(g.bogen.length, 9);
  for (const p of [g.angelpunkt, g.blattEnde, ...g.bogen]) {
    assert.ok(Number.isInteger(p.x) && Number.isInteger(p.y), `nicht ganzzahlig: ${JSON.stringify(p)}`);
  }
});

test('Defaults = links/innen', () => {
  assert.deepEqual(tuerBlattGeometrie(W), tuerBlattGeometrie(W, 'links', 'innen'));
});
