import { test } from 'node:test';
import assert from 'node:assert/strict';
import { bewerteArbeitsdreieck } from '../geometry/kuecheArbeitsdreieck';

test('gutes Dreieck: alle Wege im Ziel, Summe im Ziel, bestanden', () => {
  const r = bewerteArbeitsdreieck({
    spuele: { x: 0, y: 0 }, kochen: { x: 1800, y: 0 }, kuehlen: { x: 900, y: 1600 },
  });
  assert.equal(r.wegSpKo, 1800);
  assert.ok(r.summe >= 3600 && r.summe <= 6600);
  assert.equal(r.bestanden, true);
});

test('zu große Küche: Summe > 6600 → Fehler', () => {
  const r = bewerteArbeitsdreieck({
    spuele: { x: 0, y: 0 }, kochen: { x: 3000, y: 0 }, kuehlen: { x: 0, y: 3000 },
  });
  assert.ok(r.summe > 6600);
  assert.equal(r.pruefungen.find((p) => p.id === 'summe')!.bestanden, false);
  assert.equal(r.bestanden, false);
});

test('zu kurzes Bein → Warnung, aber Summe kann trotzdem ok sein', () => {
  const r = bewerteArbeitsdreieck({
    spuele: { x: 0, y: 0 }, kochen: { x: 900, y: 0 }, kuehlen: { x: 450, y: 2000 },
  });
  assert.ok(!r.pruefungen.find((p) => p.id === 'bein-sp-ko')!.bestanden, 'Bein 900 < 1200');
});

test('Determinismus', () => {
  const d = { spuele: { x: 0, y: 0 }, kochen: { x: 1800, y: 0 }, kuehlen: { x: 900, y: 1600 } };
  assert.deepEqual(bewerteArbeitsdreieck(d), bewerteArbeitsdreieck(d));
});
