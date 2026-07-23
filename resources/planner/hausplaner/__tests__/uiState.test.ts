/**
 * UI-2 — geteilter UI-State: Werkzeug/Arbeitsbereich setzbar + rücksetzbar; getrennt vom Modell.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { usePlannerUiStore } from '../app/state/uiState';

test('Default: Auswahl-Werkzeug, Architektur-Arbeitsbereich', () => {
  usePlannerUiStore.getState().reset();
  const s = usePlannerUiStore.getState();
  assert.equal(s.activeToolId, 'auswahl');
  assert.equal(s.activeWorkspace, 'architektur');
});

test('setActiveTool ändert den geteilten Zustand (für Studio + App lesbar)', () => {
  usePlannerUiStore.getState().reset();
  usePlannerUiStore.getState().setActiveTool('wand');
  assert.equal(usePlannerUiStore.getState().activeToolId, 'wand');
});

test('setActiveWorkspace ändert den Arbeitsbereich', () => {
  usePlannerUiStore.getState().reset();
  usePlannerUiStore.getState().setActiveWorkspace('elektro');
  assert.equal(usePlannerUiStore.getState().activeWorkspace, 'elektro');
});

test('reset stellt die Grundwerte wieder her', () => {
  usePlannerUiStore.getState().setActiveTool('treppe');
  usePlannerUiStore.getState().setActiveWorkspace('bad');
  usePlannerUiStore.getState().reset();
  const s = usePlannerUiStore.getState();
  assert.equal(s.activeToolId, 'auswahl');
  assert.equal(s.activeWorkspace, 'architektur');
});
