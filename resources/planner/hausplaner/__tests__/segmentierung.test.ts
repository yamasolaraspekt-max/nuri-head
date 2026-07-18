/**
 * P1a — Segmentierungs-Tests (Abnahmekriterium 4 + Kanten 1/2 der P1-Spec):
 * Volumen-Bilanz mm³-EXAKT (Referenzwand handgerechnet), 0-Breite-Quader entfallen,
 * Klemm-Fall ehrlich markiert statt still korrigiert.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { segmentiereWand, quaderVolumenMm3 } from '../renderers/three-d/segmentierung';
import type { SegmentierteWand } from '../renderers/three-d/segmentierung';
import type { OpeningNode, WallNode } from '../domain/scene.types';

const JETZT = '2026-07-16T00:00:00.000Z';

function wand(laengeMm = 4000, hoeheMm = 2500, dickeMm = 240): WallNode {
  return {
    id: 'w1', type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    start: { x: 0, y: 0 }, end: { x: laengeMm, y: 0 }, thickness: dickeMm, height: hoeheMm,
  };
}

function fenster(
  id: string, offset: number, width = 1200, height = 1400, sillHeight = 900,
): OpeningNode {
  return {
    id, type: 'window', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT,
    hostWallId: 'w1', offsetFromWallStart: offset, width, height, sillHeight,
  };
}

function gesamtVolumen(s: SegmentierteWand): number {
  return s.quader.reduce((summe, q) => summe + quaderVolumenMm3(q, s.dickeMm), 0);
}

test('Referenzwand (Kriterium 4): Σ Segmentvolumina = Wandvolumen − Öffnungsvolumen, mm³-exakt', () => {
  // Handrechnung: Wand 4000·240·2500 = 2_400_000_000 mm³; Fenster 1200·240·1400 = 403_200_000 mm³.
  const s = segmentiereWand(wand(4000, 2500, 240), [fenster('f1', 1500)]);

  assert.equal(gesamtVolumen(s), 4000 * 240 * 2500 - 1200 * 240 * 1400);

  // Erwartete Quader: voll[0,1500] · brüstung[1500,2700]×[0,900] · sturz[1500,2700]×[2300,2500] · voll[2700,4000].
  assert.deepEqual(
    s.quader.map((q) => [q.art, q.vonMm, q.bisMm, q.untenMm, q.obenMm]),
    [
      ['voll', 0, 1500, 0, 2500],
      ['bruestung', 1500, 2700, 0, 900],
      ['sturz', 1500, 2700, 2300, 2500],
      ['voll', 2700, 4000, 0, 2500],
    ],
  );
  assert.deepEqual(s.geklemmteOeffnungen, []);
});

test('Kante 1: Öffnung exakt am Wandende ⇒ kein 0-Breite-Quader', () => {
  const s = segmentiereWand(wand(4000), [fenster('f1', 2800, 1200)]); // endet exakt bei 4000

  assert.ok(
    s.quader.every((q) => q.bisMm > q.vonMm && q.obenMm > q.untenMm),
    'Es darf kein 0-Volumen-Quader entstehen',
  );
  assert.equal(s.quader.filter((q) => q.art === 'voll').length, 1); // nur links, rechts entfällt
  assert.equal(gesamtVolumen(s), 4000 * 240 * 2500 - 1200 * 240 * 1400);
});

test('Öffnung exakt am Wandanfang ⇒ kein linker Zwischen-Quader', () => {
  const s = segmentiereWand(wand(4000), [fenster('f1', 0, 1200)]);

  assert.equal(s.quader.filter((q) => q.art === 'voll').length, 1);
  assert.equal(s.quader[0].art, 'bruestung');
});

test('Tür (sillHeight 0): keine Brüstung, nur Sturz', () => {
  const s = segmentiereWand(wand(4000), [fenster('t1', 1000, 900, 2000, 0)]);

  assert.equal(s.quader.filter((q) => q.art === 'bruestung').length, 0);
  assert.deepEqual(
    s.quader.filter((q) => q.art === 'sturz').map((q) => [q.vonMm, q.bisMm, q.untenMm, q.obenMm]),
    [[1000, 1900, 2000, 2500]],
  );
  assert.equal(gesamtVolumen(s), 4000 * 240 * 2500 - 900 * 240 * 2000);
});

test('Kante 2: sill+height > Wandhöhe ⇒ geklemmt markiert, Sturz entfällt, Bilanz mit effektiver Öffnung', () => {
  // Fenster 900 + 2000 = 2900 > 2500 ⇒ effektiver Ausschnitt 1200 × (2500 − 900) = 1200 × 1600.
  const s = segmentiereWand(wand(4000, 2500, 240), [fenster('f1', 1500, 1200, 2000, 900)]);

  assert.deepEqual(s.geklemmteOeffnungen, ['f1']);
  assert.equal(s.quader.filter((q) => q.art === 'sturz').length, 0, 'geklemmt ⇒ kein Sturz');
  const bruestung = s.quader.find((q) => q.art === 'bruestung');
  assert.ok(bruestung?.geklemmt, 'Brüstungs-Quader trägt die Klemm-Markierung für die UI');
  assert.equal(gesamtVolumen(s), 4000 * 240 * 2500 - 1200 * 240 * 1600);
});

test('Zwei Fenster, unsortiert übergeben ⇒ deterministisch drei Zwischen-Quader', () => {
  const s = segmentiereWand(wand(6000), [fenster('f2', 3500), fenster('f1', 800)]);

  assert.deepEqual(
    s.quader.filter((q) => q.art === 'voll').map((q) => [q.vonMm, q.bisMm]),
    [[0, 800], [2000, 3500], [4700, 6000]],
  );
  assert.equal(gesamtVolumen(s), 6000 * 240 * 2500 - 2 * (1200 * 240 * 1400));
});

test('Wand ohne Öffnungen ⇒ genau ein Voll-Quader mit Wandvolumen', () => {
  const s = segmentiereWand(wand(4000), []);

  assert.deepEqual(
    s.quader.map((q) => [q.art, q.vonMm, q.bisMm, q.untenMm, q.obenMm]),
    [['voll', 0, 4000, 0, 2500]],
  );
  assert.equal(gesamtVolumen(s), 4000 * 240 * 2500);
});

test('Render-Schutz: über das Wandende hinausragende Öffnung wird auf die Wand geklemmt', () => {
  // P0 lehnt das im Command ab — der Renderer darf trotzdem nie außerhalb der Wand schneiden.
  const s = segmentiereWand(wand(4000), [fenster('f1', 3500, 1200)]); // ragt 700 mm hinaus

  assert.ok(s.quader.every((q) => q.vonMm >= 0 && q.bisMm <= 4000));
  assert.equal(gesamtVolumen(s), 4000 * 240 * 2500 - 500 * 240 * 1400); // effektive Breite 500
});

test('Diskriminierungs-Gegenprobe: Bilanz OHNE Öffnungsabzug wäre falsch', () => {
  // Die Referenz ist unterscheidungsfähig: volles Wandvolumen ≠ Sollwert mit Fenster.
  const s = segmentiereWand(wand(4000, 2500, 240), [fenster('f1', 1500)]);

  assert.notEqual(gesamtVolumen(s), 4000 * 240 * 2500);
});
