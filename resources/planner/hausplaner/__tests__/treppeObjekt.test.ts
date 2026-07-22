/**
 * Treppe ↔ ObjectNode.parameters: Roundtrip + Robustheit gegen fehlende/ungültige Felder.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { treppeZuParametern, parametereZuTreppe, type TreppeParams } from '../geometry/treppeObjekt';

const t: TreppeParams = {
  startX: 100, startY: 200, endX: 3100, endY: 200,
  laufbreite: 1000, geschosshoehe: 2800, bereich: 'wohnung',
};

test('Roundtrip: Params → Record → Params ist verlustfrei', () => {
  const rec = treppeZuParametern(t);
  assert.deepEqual(parametereZuTreppe(rec), t);
});

test('Schlüssel tragen das treppe.-Präfix', () => {
  const rec = treppeZuParametern(t);
  assert.equal(rec['treppe.laufbreite'], 1000);
  assert.equal(rec['treppe.bereich'], 'wohnung');
  assert.ok(Object.keys(rec).every((k) => k.startsWith('treppe.')));
});

test('optionale gewünschte Steigung wird nur bei >0 geschrieben und gelesen', () => {
  const rec = treppeZuParametern({ ...t, gewuenschteSteigung: 180 });
  assert.equal(rec['treppe.gewuenschteSteigung'], 180);
  assert.equal(parametereZuTreppe(rec)!.gewuenschteSteigung, 180);
  const ohne = treppeZuParametern({ ...t, gewuenschteSteigung: 0 });
  assert.ok(!('treppe.gewuenschteSteigung' in ohne));
});

test('fehlende Pflichtfelder → null', () => {
  assert.equal(parametereZuTreppe({ 'treppe.startX': 0 }), null);
  assert.equal(parametereZuTreppe(null), null);
  assert.equal(parametereZuTreppe({}), null);
});

test('nicht-positive Laufbreite/Geschosshöhe → null', () => {
  const rec = treppeZuParametern(t);
  assert.equal(parametereZuTreppe({ ...rec, 'treppe.laufbreite': 0 }), null);
  assert.equal(parametereZuTreppe({ ...rec, 'treppe.geschosshoehe': -1 }), null);
});

test('unbekannter Bereich fällt sicher auf wohnung zurück', () => {
  const rec = treppeZuParametern(t);
  assert.equal(parametereZuTreppe({ ...rec, 'treppe.bereich': 'quatsch' })!.bereich, 'wohnung');
});
