import { test } from 'node:test';
import assert from 'node:assert/strict';
import { pruefeAbwasser, mindestGefaelle, maxHorizontaleDistanz } from '../geometry/abwassergefaelle';

test('Mindestgefälle je DN', () => {
  assert.equal(mindestGefaelle(50), 2.0);
  assert.equal(mindestGefaelle(70), 1.5);
  assert.equal(mindestGefaelle(100), 1.0);
});

test('DN100, 5 m, ohne Vorgabe → nimmt Mindestgefälle 1%, Höhenverlust 50 mm', () => {
  const r = pruefeAbwasser({ dn: 100, laenge: 5 });
  assert.equal(r.gefaelle, 1.0);
  assert.equal(r.hoehenverlust, 50);
  assert.equal(r.bestanden, true);
});

test('zu geringes Gefälle → Fehler', () => {
  const r = pruefeAbwasser({ dn: 100, laenge: 5, gefaelle: 0.5 });
  assert.equal(r.pruefungen.find((p) => p.id === 'min-gefaelle')!.bestanden, false);
  assert.equal(r.bestanden, false);
});

test('zu großes Gefälle → Warnung (kein Blocker)', () => {
  const r = pruefeAbwasser({ dn: 100, laenge: 5, gefaelle: 6 });
  assert.equal(r.pruefungen.find((p) => p.id === 'max-gefaelle')!.bestanden, false);
  assert.equal(r.bestanden, true);
});

test('maximale horizontale Distanz aus Fallhöhe', () => {
  // DN100 min 1% → 60 mm Fallhöhe reicht für 6 m
  assert.equal(maxHorizontaleDistanz(100, 60), 6);
  // DN50 min 2% → 60 mm reicht nur für 3 m
  assert.equal(maxHorizontaleDistanz(50, 60), 3);
});
