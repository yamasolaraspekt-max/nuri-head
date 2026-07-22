/**
 * Treppen-Validierung: unsere berechneTreppe gegen UNABHÄNGIG handgerechnete DIN-18065-Fälle
 * (dieselbe Norm, die auch kalk.pro implementiert). Kein Fremddaten-Kopieren — Norm als Quelle.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { berechneTreppe } from '../geometry/treppenBerechnung';

test('Wohnung 2600 mm: 15 Steigungen à 173,3 mm, Schrittmaß 630 — bestanden', () => {
  const r = berechneTreppe({ geschosshoehe: 2600 });
  assert.equal(r.anzahlSteigungen, 15);      // round(2600/175)
  assert.equal(r.steigungshoehe, 173.3);     // 2600/15
  assert.equal(r.auftritt, 283.3);           // 630 - 2·173,33 (Schrittmaßregel)
  assert.equal(r.schrittmass, 630);
  assert.equal(r.bestanden, true);
});

test('Wohnung 2750 mm: 16 Steigungen, alle Grenzmaße eingehalten', () => {
  const r = berechneTreppe({ geschosshoehe: 2750 });
  assert.equal(r.anzahlSteigungen, 16);
  assert.equal(r.steigungshoehe, 171.9);
  assert.equal(r.bestanden, true);
});

test('Öffentliches Gebäude 3000 mm: strengere Grenzen (Steigung ≤190, Auftritt ≥260) gehalten', () => {
  const r = berechneTreppe({ geschosshoehe: 3000, bereich: 'gebaeude' });
  assert.ok(r.steigungshoehe <= 190);
  assert.ok(r.auftritt >= 260);
  assert.equal(r.bestanden, true);
});

test('Zu kurze Lauflänge erzwingt zu kleinen Auftritt → DIN-Verstoß erkannt (bestanden=false)', () => {
  const r = berechneTreppe({ geschosshoehe: 2600, verfuegbareLauflaenge: 3200 });
  // 14 Auftritte, 3200/14 = 228,6 mm < 230 mm Mindestauftritt (Wohnung)
  assert.ok(r.auftritt < 230);
  assert.equal(r.bestanden, false);
  assert.ok(r.pruefungen.some((p) => p.id === 'auftritt-min' && !p.bestanden));
});

test('Schrittmaßregel bleibt im Sollband 590–650 für typische Wohnungshöhen', () => {
  for (const h of [2500, 2600, 2700, 2800, 2900]) {
    const r = berechneTreppe({ geschosshoehe: h });
    assert.ok(r.schrittmass >= 590 && r.schrittmass <= 650, `h=${h}: ${r.schrittmass}`);
  }
});
