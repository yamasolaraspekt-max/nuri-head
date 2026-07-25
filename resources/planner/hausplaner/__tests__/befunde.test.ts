/**
 * Dashboard v2.4 — das Prüfungscenter liest den Store, rechnet aber nichts nach.
 * Abnahmekriterium Batch 2, Punkt 8: `null` ⇒ [], Meldung ⇒ genau EIN Befund mit dem
 * UNVERÄNDERTEN Text. Punkt 10: der Leerzustand lautet wörtlich „Keine offenen Befunde."
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { befundeAus, BEFUNDE_LEER, BEFUNDE_UMFANG, BEFUND_ID_ABLEHNUNG } from '../app/dashboard/befunde';
import { PANEL_TABS, panelTab } from '../app/dashboard/panelTabs';

test('keine Ablehnung ⇒ keine Befunde', () => {
  assert.deepEqual(befundeAus(null), []);
});

test('leere oder nur aus Leerzeichen bestehende Meldung ⇒ keine Befunde', () => {
  assert.deepEqual(befundeAus(''), []);
  assert.deepEqual(befundeAus('   '), []);
});

test('eine Meldung ⇒ genau ein Befund mit dem unveränderten Text', () => {
  const meldung = 'Öffnung passt nicht auf die Wand: Breite überschreitet die Restlänge.';
  const b = befundeAus(meldung);
  assert.equal(b.length, 1);
  assert.equal(b[0].text, meldung); // byte-gleich: nicht gekürzt, nicht getrimmt, nicht umformuliert
  assert.equal(b[0].schwere, 'fehler');
  assert.equal(b[0].id, BEFUND_ID_ABLEHNUNG);
});

test('Text mit Rand-Leerzeichen bleibt unverändert (nur die Leer-Prüfung trimmt)', () => {
  assert.equal(befundeAus('  Wand gesperrt.  ')[0].text, '  Wand gesperrt.  ');
});

test('Kante 9: schneller Wechsel summiert nicht auf — immer höchstens ein Befund', () => {
  for (const m of ['A abgelehnt', 'B abgelehnt', 'C abgelehnt']) {
    assert.equal(befundeAus(m).length, 1);
  }
  assert.equal(befundeAus('C abgelehnt')[0].text, 'C abgelehnt');
  assert.equal(befundeAus(null).length, 0);
});

test('Leerzustand wörtlich „Keine offenen Befunde." — nicht „keine Daten"', () => {
  assert.equal(BEFUNDE_LEER, 'Keine offenen Befunde.');
  assert.ok(!/keine daten/i.test(BEFUNDE_LEER));
});

test('unter der Liste steht ehrlich, was NICHT geführt wird', () => {
  assert.ok(BEFUNDE_UMFANG.length > 40);
  assert.ok(/zuletzt abgelehnte/i.test(BEFUNDE_UMFANG));
});

test('Abnahmekriterium 10: der Reiter „Prüfungen" ist in Batch 2 verfügbar', () => {
  assert.equal(panelTab('pruefungen')?.zustand, 'verfuegbar');
  // Die anderen drei Zustände bleiben unangetastet — Batch 2 hebt genau einen.
  assert.equal(panelTab('allgemein')?.zustand, 'verfuegbar');
  assert.equal(panelTab('beziehungen')?.zustand, 'in_entwicklung');
  assert.equal(panelTab('historie')?.zustand, 'in_entwicklung');
  assert.equal(PANEL_TABS.length, 4);
});
