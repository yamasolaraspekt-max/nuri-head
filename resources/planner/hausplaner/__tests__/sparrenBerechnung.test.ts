/**
 * Sparren-Vorbemessung (Eurocode): Schneelast (DIN EN 1991-1-3/NA), Biege-/Durchbiegungsnachweis (EC5).
 * Referenzwerte unabhängig handgerechnet — kalk.pro nur als fachliche Referenz, nichts kopiert.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { berechneSparren, bodenschneelast, formbeiwertSchnee } from '../geometry/sparrenBerechnung';

test('Bodenschneelast: Zone 2 bei 300 m ≈ 0,89 kN/m², Mindestwerte greifen im Flachland', () => {
  assert.ok(Math.abs(bodenschneelast(2, 300) - 0.89) < 0.02);
  assert.equal(bodenschneelast(1, 0), 0.65);   // Mindestwert Zone 1
  assert.equal(bodenschneelast(3, 0), 1.10);   // Mindestwert Zone 3
  assert.ok(bodenschneelast(3, 800) > bodenschneelast(1, 800)); // Zone 3 > Zone 1
});

test('Formbeiwert μ1: 0,8 bis 30°, 0 ab 60°, linear dazwischen', () => {
  assert.equal(formbeiwertSchnee(20), 0.8);
  assert.equal(formbeiwertSchnee(30), 0.8);
  assert.equal(formbeiwertSchnee(60), 0);
  assert.ok(Math.abs(formbeiwertSchnee(45) - 0.4) < 1e-9);
});

test('Referenzsparren 8 m / 40° / e=0,7 / 80×180 C24 / Zone 2 / 300 m: hält, Durchbiegung maßgebend', () => {
  const r = berechneSparren({
    gebaeudebreiteM: 8, neigungGrad: 40, sparrenabstandM: 0.7,
    breiteMm: 80, hoeheMm: 180, schneezone: 2, gelaendehoeheM: 300,
  });
  assert.ok(Math.abs(r.sparrenlaengeM - 5.222) < 0.01);
  assert.ok(r.ausnutzungBiegung > 0.35 && r.ausnutzungBiegung < 0.55);      // ~0,45
  assert.ok(r.ausnutzungDurchbiegung > 0.75 && r.ausnutzungDurchbiegung < 1.0); // ~0,88 (maßgebend)
  assert.ok(r.ausnutzungDurchbiegung > r.ausnutzungBiegung);
  assert.equal(r.bestanden, true);
});

test('Unterdimensionierter Sparren (60×120) bei großer Spannweite → Nachweis versagt', () => {
  const r = berechneSparren({
    gebaeudebreiteM: 10, neigungGrad: 35, sparrenabstandM: 0.8,
    breiteMm: 60, hoeheMm: 120, schneezone: 3, gelaendehoeheM: 600,
  });
  assert.equal(r.bestanden, false);
  assert.ok(r.ausnutzungBiegung > 1 || r.ausnutzungDurchbiegung > 1);
});

test('Höhere Schneezone erhöht Ausnutzung (mehr Last)', () => {
  const basis = { gebaeudebreiteM: 8, neigungGrad: 30, sparrenabstandM: 0.7, breiteMm: 80, hoeheMm: 200, gelaendehoeheM: 400 } as const;
  const z1 = berechneSparren({ ...basis, schneezone: 1 });
  const z3 = berechneSparren({ ...basis, schneezone: 3 });
  assert.ok(z3.ausnutzungBiegung > z1.ausnutzungBiegung);
});

test('C30 ist tragfähiger als C24 (geringere Biege-Ausnutzung)', () => {
  const basis = { gebaeudebreiteM: 9, neigungGrad: 38, sparrenabstandM: 0.75, breiteMm: 80, hoeheMm: 200, schneezone: 2, gelaendehoeheM: 300 } as const;
  const c24 = berechneSparren({ ...basis, holzklasse: 'C24' });
  const c30 = berechneSparren({ ...basis, holzklasse: 'C30' });
  assert.ok(c30.ausnutzungBiegung < c24.ausnutzungBiegung);
});
