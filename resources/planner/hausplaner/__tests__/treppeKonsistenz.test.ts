/**
 * „Eine Wahrheit": Panel-Auslegung (berechneTreppe mit gezeichneter Lauflinie) und 2D-Symbol
 * (treppe2DSymbol) müssen für DIESELBE Treppe dasselbe DIN-Verdikt und dieselben Kennwerte
 * liefern. Regressionsschutz für den Evaluator-Fund (Panel „erfüllt" bei rotem Umriss).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { treppe2DSymbol } from '../geometry/treppe2D';
import { berechneTreppe } from '../geometry/treppenBerechnung';

type Fall = { start: { x: number; y: number }; end: { x: number; y: number }; laufbreite: number; geschosshoehe: number };

// So ruft das Panel jetzt (mit gezeichneter Lauflinie) — muss zum Symbol passen.
function panel(f: Fall) {
  const len = Math.hypot(f.end.x - f.start.x, f.end.y - f.start.y);
  return berechneTreppe({ geschosshoehe: f.geschosshoehe, laufbreite: f.laufbreite, verfuegbareLauflaenge: len || undefined });
}

const faelle: Fall[] = [
  { start: { x: 0, y: 0 }, end: { x: 3000, y: 0 }, laufbreite: 1000, geschosshoehe: 2800 }, // normal
  { start: { x: 0, y: 0 }, end: { x: 4500, y: 0 }, laufbreite: 1000, geschosshoehe: 3000 }, // lang
  { start: { x: 0, y: 0 }, end: { x: 2600, y: 0 }, laufbreite: 300, geschosshoehe: 2800 },  // Laufbreite verletzt
  { start: { x: 0, y: 0 }, end: { x: 1800, y: 0 }, laufbreite: 1000, geschosshoehe: 2800 }, // kurz → Auftritt klein
];

test('Symbol und Panel stimmen im DIN-Verdikt überein (kein Widerspruch mehr)', () => {
  for (const f of faelle) {
    const sym = treppe2DSymbol(f);
    const p = panel(f);
    assert.equal(sym.bestanden, p.bestanden, `Verdikt-Widerspruch bei ${JSON.stringify(f)}`);
  }
});

test('Symbol und Panel stimmen in Stufenzahl und Auftritt überein', () => {
  for (const f of faelle) {
    const sym = treppe2DSymbol(f);
    const p = panel(f);
    assert.equal(sym.anzahlSteigungen, p.anzahlSteigungen);
    assert.equal(sym.auftritt, p.auftritt);
  }
});

test('Verletzte Laufbreite: beide melden „nicht bestanden"', () => {
  const f = faelle[2];
  assert.equal(treppe2DSymbol(f).bestanden, false);
  assert.equal(panel(f).bestanden, false);
});
