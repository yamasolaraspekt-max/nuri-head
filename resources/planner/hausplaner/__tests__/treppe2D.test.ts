/**
 * Treppen-2D-Symbol: Umriss + Trittstufen-Linien quer zur Lauflinie, aus getesteter Berechnung.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { treppe2DSymbol } from '../geometry/treppe2D';

test('waagerechter Lauf: Umriss = Rechteck laufbreite×lauflaenge um die Lauflinie', () => {
  const s = treppe2DSymbol({
    start: { x: 0, y: 0 },
    end: { x: 3000, y: 0 },
    laufbreite: 1000,
    geschosshoehe: 2800,
  });
  // hw=500, Linksnormale=(0,1): links=+y, rechts=-y
  assert.deepEqual(s.umriss, [
    { x: 0, y: 500 },
    { x: 3000, y: 500 },
    { x: 3000, y: -500 },
    { x: 0, y: -500 },
  ]);
});

test('Stufenzahl aus berechneTreppe: 2800/175=16 Steigungen, 15 Auftritte, Auftritt=200 bei 3000 Lauf', () => {
  const s = treppe2DSymbol({
    start: { x: 0, y: 0 },
    end: { x: 3000, y: 0 },
    laufbreite: 1000,
    geschosshoehe: 2800,
  });
  assert.equal(s.anzahlSteigungen, 16);
  assert.equal(s.anzahlAuftritte, 15);
  assert.equal(s.auftritt, 200);
  assert.equal(s.steigungshoehe, 175);
});

test('Trittstufen: (Auftritte−1) Querlinien, erste bei einem Auftritt entlang der Lauflinie', () => {
  const s = treppe2DSymbol({
    start: { x: 0, y: 0 },
    end: { x: 3000, y: 0 },
    laufbreite: 1000,
    geschosshoehe: 2800,
  });
  assert.equal(s.stufen.length, 14); // 15 Auftritte → 14 innere Grenzen
  assert.deepEqual(s.stufen[0], [
    { x: 200, y: 500 },
    { x: 200, y: -500 },
  ]);
  assert.deepEqual(s.stufen[13], [
    { x: 2800, y: 500 },
    { x: 2800, y: -500 },
  ]);
});

test('senkrechter Lauf: Umriss dreht mit (Lauflinie in Y, Breite in X)', () => {
  const s = treppe2DSymbol({
    start: { x: 0, y: 0 },
    end: { x: 0, y: 3000 },
    laufbreite: 1000,
    geschosshoehe: 2800,
  });
  // ux=0,uy=1 → Linksnormale=(-1,0): links=-x, rechts=+x
  assert.deepEqual(s.umriss, [
    { x: -500, y: 0 },
    { x: -500, y: 3000 },
    { x: 500, y: 3000 },
    { x: 500, y: 0 },
  ]);
  assert.deepEqual(s.stufen[0], [
    { x: -500, y: 200 },
    { x: 500, y: 200 },
  ]);
});

test('Pfeil zeigt von start (unten) nach end (oben)', () => {
  const s = treppe2DSymbol({
    start: { x: 100, y: 200 },
    end: { x: 3100, y: 200 },
    laufbreite: 1000,
    geschosshoehe: 2800,
  });
  assert.deepEqual(s.pfeil.von, { x: 100, y: 200 });
  assert.deepEqual(s.pfeil.bis, { x: 3100, y: 200 });
});

test('Länge 0 stürzt nicht ab (degenerierte Lauflinie)', () => {
  const s = treppe2DSymbol({
    start: { x: 0, y: 0 },
    end: { x: 0, y: 0 },
    laufbreite: 1000,
    geschosshoehe: 2800,
  });
  assert.ok(s.umriss.length === 4);
  assert.ok(Number.isFinite(s.auftritt));
});
