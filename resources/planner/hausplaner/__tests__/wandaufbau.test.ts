/**
 * Wandaufbau / U-Wert (DIN EN ISO 6946).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { berechneUWert, UEBERGANG } from '../geometry/wandaufbau';

test('KS 240 + WDVS 160: U ≈ 0,20 W/(m²K), erfüllt Ziel 0,24', () => {
  const r = berechneUWert([
    { name: 'Kalksandstein', dicke: 240, lambda: 0.99 },
    { name: 'WDVS (EPS)', dicke: 160, lambda: 0.035 },
    { name: 'Putz', dicke: 15, lambda: 0.87 },
  ]);
  assert.equal(r.gesamtdicke, 415);
  assert.ok(Math.abs(r.uWert - 0.20) < 0.02, `U=${r.uWert}`);
  assert.ok(r.pruefungen.find((p) => p.id === 'ziel-u')!.bestanden);
});

test('ungedämmte Ziegelwand 300: hoher U-Wert, Ziel verfehlt', () => {
  const r = berechneUWert([{ dicke: 300, lambda: 0.5 }]);
  assert.ok(r.uWert > 1, `U=${r.uWert}`);
  assert.equal(r.pruefungen.find((p) => p.id === 'ziel-u')!.bestanden, false);
});

test('R gesamt = Rsi + Σ(d/λ) + Rse', () => {
  const r = berechneUWert([{ dicke: 200, lambda: 0.04 }], { bauteil: 'aussenwand' });
  const erwartetRBauteil = 0.2 / 0.04; // 5,0
  assert.ok(Math.abs(r.rBauteil - erwartetRBauteil) < 1e-6);
  assert.ok(Math.abs(r.rGesamt - (UEBERGANG.aussenwand.rsi + erwartetRBauteil + UEBERGANG.aussenwand.rse)) < 1e-6);
});

test('mehr Dämmung senkt den U-Wert (Monotonie)', () => {
  const duenn = berechneUWert([{ dicke: 100, lambda: 0.035 }]);
  const dick = berechneUWert([{ dicke: 200, lambda: 0.035 }]);
  assert.ok(dick.uWert < duenn.uWert);
});

test('Bauteilart wählt Übergangswiderstände (Dach ≠ Außenwand)', () => {
  const schicht = [{ dicke: 200, lambda: 0.04 }];
  const wand = berechneUWert(schicht, { bauteil: 'aussenwand' });
  const dach = berechneUWert(schicht, { bauteil: 'dach' });
  assert.notEqual(wand.rGesamt, dach.rGesamt);
});

test('Determinismus', () => {
  const s = [{ dicke: 240, lambda: 0.99 }, { dicke: 160, lambda: 0.035 }];
  assert.deepEqual(berechneUWert(s), berechneUWert(s));
});
