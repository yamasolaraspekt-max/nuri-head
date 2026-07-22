/**
 * Integrations-/Konfliktprüfung autark → Projekt (Yamas Abschnitt 14).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { neuesPaket, type ConfiguratorPackage, type ConfiguratorStatus } from '../geometry/configuratorPackage';
import { pruefeOeffnungsIntegration, pruefePaketIntegration } from '../geometry/integrationAbgleich';

const jetzt = '2026-07-22T10:00:00.000Z';

function fensterPaket(breite: number, hoehe: number, status: ConfiguratorStatus = 'approved'): ConfiguratorPackage {
  return { ...neuesPaket({ id: 'f', type: 'window', jetzt, autor: 'yama', geometry: { breite, hoehe } }), status };
}

test('passendes Fenster in passende Öffnung: keine Blocker', () => {
  const r = pruefeOeffnungsIntegration(fensterPaket(1500, 1360), { oeffnungBreite: 1500, oeffnungHoehe: 1360 });
  assert.equal(r.integrierbar, true);
  assert.equal(r.konflikte.length, 0);
});

test('Yamas Beispiel: Fenster 1510 vs Öffnung 1480 → Maßkonflikt mit drei Optionen', () => {
  const r = pruefeOeffnungsIntegration(fensterPaket(1510, 1360), { oeffnungBreite: 1480, oeffnungHoehe: 1360 });
  assert.equal(r.integrierbar, false);
  const masse = r.konflikte.find((k) => k.typ === 'masse');
  assert.ok(masse, 'Maßkonflikt erwartet');
  assert.match(masse!.meldung, /\+30 mm/);
  assert.deepEqual(masse!.optionen, ['Öffnung anpassen', 'Objektbreite ändern', 'anderes Objekt wählen']);
});

test('Toleranz: 5 mm Abweichung wird geschluckt, 6 mm nicht', () => {
  assert.equal(pruefeOeffnungsIntegration(fensterPaket(1485, 1360), { oeffnungBreite: 1480, oeffnungHoehe: 1360 }).integrierbar, true);
  assert.equal(pruefeOeffnungsIntegration(fensterPaket(1486, 1360), { oeffnungBreite: 1480, oeffnungHoehe: 1360 }).integrierbar, false);
});

test('nicht freigegebenes Paket: Status-Blocker', () => {
  const r = pruefeOeffnungsIntegration(fensterPaket(1500, 1360, 'draft'), { oeffnungBreite: 1500, oeffnungHoehe: 1360 });
  assert.equal(r.integrierbar, false);
  assert.ok(r.konflikte.some((k) => k.typ === 'status'));
});

test('veraltetes Paket: Veraltet-Blocker mit Vergleichs-Option', () => {
  const r = pruefeOeffnungsIntegration(fensterPaket(1500, 1360, 'outdated'), { oeffnungBreite: 1500, oeffnungHoehe: 1360 });
  const v = r.konflikte.find((k) => k.typ === 'veraltet');
  assert.ok(v);
  assert.ok(v!.optionen.includes('Änderungen vergleichen'));
});

test('fehlender Anschluss-Port am Ziel → Warnung (kein Blocker)', () => {
  const paket = fensterPaket(1500, 1360);
  paket.connectionPorts = [{ id: 'r1', medium: 'raffstore-power' }];
  const r = pruefeOeffnungsIntegration(paket, { oeffnungBreite: 1500, oeffnungHoehe: 1360, vorhandenePorts: [] });
  assert.equal(r.integrierbar, true, 'Warnung blockiert nicht');
  assert.ok(r.konflikte.some((k) => k.typ === 'anschluss' && k.schwere === 'warnung'));
});

test('Katalogversion veraltet → Warnung', () => {
  const paket = fensterPaket(1500, 1360);
  paket.catalogItems = [{ id: 'c1', katalogVersion: 3 }];
  const r = pruefeOeffnungsIntegration(paket, { oeffnungBreite: 1500, oeffnungHoehe: 1360, katalogVersion: 5 });
  assert.ok(r.konflikte.some((k) => k.typ === 'katalog'));
});

test('pruefePaketIntegration: nur approved ist integrierbar', () => {
  assert.equal(pruefePaketIntegration(fensterPaket(1, 1, 'approved')).integrierbar, true);
  assert.equal(pruefePaketIntegration(fensterPaket(1, 1, 'checked')).integrierbar, false);
});
