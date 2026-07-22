import { test } from 'node:test';
import assert from 'node:assert/strict';
import { kreisDurchfluss, auslegeVerteiler } from '../geometry/heizkreisVerteiler';

test('Durchfluss: 1000 W bei 5 K Spreizung ≈ 172 kg/h', () => {
  const q = kreisDurchfluss(1000, 40, 35);
  assert.ok(Math.abs(q - 172.2) < 1, `q=${q}`);
});

test('Vorlauf ≤ Rücklauf → kein Durchfluss', () => {
  assert.equal(kreisDurchfluss(1000, 35, 40), 0);
});

test('Verteiler: Abgänge = Kreiszahl, Gesamtdurchfluss = Summe', () => {
  const r = auslegeVerteiler([
    { raum: 'Wohnen', leistung: 1200, vorlauf: 40, ruecklauf: 33 },
    { raum: 'Bad', leistung: 800, vorlauf: 40, ruecklauf: 33 },
  ]);
  assert.equal(r.abgaenge, 2);
  const summe = r.kreise[0].durchfluss + r.kreise[1].durchfluss;
  assert.ok(Math.abs(r.gesamtDurchfluss - summe) < 0.2);
  assert.equal(r.kreise[0].spreizung, 7);
});

test('ungültige Spreizung in einem Kreis → Warnung', () => {
  const r = auslegeVerteiler([{ leistung: 500, vorlauf: 30, ruecklauf: 35 }]);
  assert.equal(r.pruefungen.find((p) => p.id === 'spreizung')!.bestanden, false);
});

test('Determinismus', () => {
  const k = [{ raum: 'A', leistung: 1000, vorlauf: 40, ruecklauf: 34 }];
  assert.deepEqual(auslegeVerteiler(k), auslegeVerteiler(k));
});
