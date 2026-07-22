/**
 * Treppen-3D-Körper: gestapelte Vollstufen-Quader aus getesteter Berechnung.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { treppe3DKoerper } from '../geometry/treppe3D';

test('Anzahl Stufen-Quader = Auftritte (2800/175 → 15 Auftritte bei 3000 Lauf)', () => {
  const k = treppe3DKoerper({ laufbreite: 1000, geschosshoehe: 2800, verfuegbareLauflaenge: 3000 });
  assert.equal(k.anzahlAuftritte, 15);
  assert.equal(k.stufen.length, 15);
  assert.equal(k.auftritt, 200);
  assert.equal(k.steigungshoehe, 175);
});

test('erste Stufe: 1×Steigung hoch, Auftritt lang, Laufbreite breit, korrekt zentriert', () => {
  const k = treppe3DKoerper({ laufbreite: 1000, geschosshoehe: 2800, verfuegbareLauflaenge: 3000 });
  assert.deepEqual(k.stufen[0].groesse, [200, 1000, 175]);      // Auftritt, Breite, 1·Steigung
  assert.deepEqual(k.stufen[0].mitte, [100, 0, 87.5]);          // Mitte x=½Auftritt, z=½Höhe
});

test('oberste Stufe erreicht (fast) die Geschosshöhe: 15·175 = 2625, Mitte z = 1312,5', () => {
  const k = treppe3DKoerper({ laufbreite: 1000, geschosshoehe: 2800, verfuegbareLauflaenge: 3000 });
  const top = k.stufen[k.stufen.length - 1];
  assert.equal(top.groesse[2], 2625);
  assert.equal(top.mitte[2], 1312.5);
});

test('Stufen steigen monoton in x und z', () => {
  const k = treppe3DKoerper({ laufbreite: 900, geschosshoehe: 2600, verfuegbareLauflaenge: 2800 });
  for (let i = 1; i < k.stufen.length; i++) {
    assert.ok(k.stufen[i].mitte[0] > k.stufen[i - 1].mitte[0]);
    assert.ok(k.stufen[i].mitte[2] > k.stufen[i - 1].mitte[2]);
  }
});

test('ohne verfügbare Lauflänge greift die Schrittmaßregel (Auftritt aus 630−2·Steigung)', () => {
  const k = treppe3DKoerper({ laufbreite: 1000, geschosshoehe: 2800 });
  // steigung ~175 → Auftritt ~ 630−350 = 280
  assert.ok(k.auftritt > 250 && k.auftritt < 300);
  assert.equal(k.stufen.length, k.anzahlAuftritte);
});
