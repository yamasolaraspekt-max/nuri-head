import { test } from 'node:test';
import assert from 'node:assert/strict';
import { TREPPEN_BAUARTEN, treppenBauartNach } from '../geometry/treppenBauarten';
import { treppeZuParametern, parametereZuTreppe } from '../geometry/treppeObjekt';

test('Treppen-Bauarten: 20, IDs eindeutig, Datei = id.svg', () => {
  assert.equal(TREPPEN_BAUARTEN.length, 20);
  const ids = new Set<string>();
  for (const b of TREPPEN_BAUARTEN) {
    assert.ok(b.id.length > 0);
    assert.ok(b.label.length > 0);
    assert.equal(b.datei, `${b.id}.svg`);
    assert.ok(!ids.has(b.id));
    ids.add(b.id);
  }
});

test('treppenBauartNach findet + undefined bei Unbekannt', () => {
  assert.equal(treppenBauartNach('09_spindeltreppe')?.label, 'Spindeltreppe');
  assert.equal(treppenBauartNach('gibtsnicht'), undefined);
  assert.equal(treppenBauartNach(undefined), undefined);
});

test('Treppen-typ läuft additiv durch die Parameter-Bridge', () => {
  const basis = { startX: 0, startY: 0, endX: 3000, endY: 0, laufbreite: 1000, geschosshoehe: 2700, bereich: 'wohnung' as const };
  const ohne = parametereZuTreppe(treppeZuParametern(basis));
  assert.equal(ohne?.typ, undefined);
  const mit = parametereZuTreppe(treppeZuParametern({ ...basis, typ: '04_u_treppe_halbgewendelt' }));
  assert.equal(mit?.typ, '04_u_treppe_halbgewendelt');
  assert.equal(mit?.laufbreite, 1000);
});
