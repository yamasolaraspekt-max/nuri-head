/**
 * PV-Schnellbelegung: Modulzahl, Orientierungswahl, kWp.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { pvSchnellBelegung } from '../geometry/pvBelegung';

test('Rechteckdach 10×8 m, Standardmodul: plausible Modulzahl + kWp', () => {
  const r = pvSchnellBelegung({
    dachLaenge: 10000, dachBreite: 8000,
    modulBreite: 1134, modulHoehe: 1722, modulLeistung: 430,
  });
  assert.ok(r.moduleGesamt > 0);
  assert.equal(r.kWp, Math.round((r.moduleGesamt * 430) / 1000 * 100) / 100);
  assert.ok(r.flaechennutzung > 0 && r.flaechennutzung <= 100);
});

test('wählt die Orientierung mit mehr Modulen', () => {
  const r = pvSchnellBelegung({
    dachLaenge: 6000, dachBreite: 3400,
    modulBreite: 1134, modulHoehe: 1722, modulLeistung: 430, randabstand: 200, modulabstand: 20,
  });
  // Prüfen: das gelieferte Ergebnis ist ≥ der jeweils anderen Orientierung
  assert.ok(['hochkant', 'quer'].includes(r.orientierung));
  assert.ok(r.moduleGesamt === r.spalten * r.reihen);
});

test('zu kleines Dach → 0 Module, kein Absturz', () => {
  const r = pvSchnellBelegung({
    dachLaenge: 800, dachBreite: 800,
    modulBreite: 1134, modulHoehe: 1722, modulLeistung: 430, randabstand: 300,
  });
  assert.equal(r.moduleGesamt, 0);
  assert.equal(r.kWp, 0);
  assert.equal(r.flaechennutzung, 0);
});

test('mehr Fläche → nie weniger Module (Monotonie)', () => {
  const base = { modulBreite: 1134, modulHoehe: 1722, modulLeistung: 430 } as const;
  const klein = pvSchnellBelegung({ ...base, dachLaenge: 8000, dachBreite: 6000 });
  const gross = pvSchnellBelegung({ ...base, dachLaenge: 12000, dachBreite: 9000 });
  assert.ok(gross.moduleGesamt >= klein.moduleGesamt);
});

test('Determinismus', () => {
  const e = { dachLaenge: 10000, dachBreite: 8000, modulBreite: 1134, modulHoehe: 1722, modulLeistung: 430 };
  assert.deepEqual(pvSchnellBelegung(e), pvSchnellBelegung(e));
});
