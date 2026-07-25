/**
 * Dashboard v2.2 — die Panel-Reiter sind Daten, also prüfbar.
 * Abnahmekriterium Batch 1, Punkt 3: genau vier Reiter, feste Reihenfolge, jeder mit gültigem
 * Zustand. Dazu die Zusage aus §1: keine leere Fläche ohne ausgesprochenen Zustand.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { PANEL_TABS, panelTab, type PanelTabId } from '../app/dashboard/panelTabs';

const ZUSTAENDE = ['verfuegbar', 'voraussetzung', 'nur_ergebnis', 'in_entwicklung'];

test('genau vier Reiter in fester Reihenfolge', () => {
  assert.equal(PANEL_TABS.length, 4);
  assert.deepEqual(
    PANEL_TABS.map((t) => t.id),
    ['allgemein', 'beziehungen', 'pruefungen', 'historie'] satisfies PanelTabId[],
  );
});

test('jeder Reiter trägt einen gültigen StudioZustand', () => {
  for (const t of PANEL_TABS) {
    assert.ok(ZUSTAENDE.includes(t.zustand), `${t.id}: Zustand „${t.zustand}" ist kein StudioZustand`);
  }
});

test('„allgemein" und (seit Batch 2) „pruefungen" sind verfügbar — der Rest sagt seinen Zustand aus', () => {
  assert.equal(panelTab('allgemein')?.zustand, 'verfuegbar');
  // v2.4 hat den Prüfungen-Reiter gehoben: er zeigt echte Befunde (befunde.ts), keine Fläche mehr.
  assert.equal(panelTab('pruefungen')?.zustand, 'verfuegbar');
  for (const id of ['beziehungen', 'historie']) {
    assert.equal(panelTab(id)?.zustand, 'in_entwicklung', `${id} ist in v2 noch Fläche`);
  }
});

test('keine leere Fläche ohne Hinweistext (kein Blindtext, kein „keine Daten")', () => {
  for (const t of PANEL_TABS) {
    assert.ok(t.label.length > 0, `${t.id}: Label fehlt`);
    assert.ok(t.hinweis.length > 10, `${t.id}: Hinweis fehlt oder ist zu dünn`);
    assert.ok(!/keine daten/i.test(t.hinweis), `${t.id}: „keine Daten" ist als Leerzustand verboten`);
  }
});

test('keine doppelte id', () => {
  const ids = PANEL_TABS.map((t) => t.id);
  assert.equal(new Set(ids).size, ids.length);
});

test('unbekannte id liefert undefined statt zu werfen', () => {
  assert.equal(panelTab('gibt-es-nicht'), undefined);
});
