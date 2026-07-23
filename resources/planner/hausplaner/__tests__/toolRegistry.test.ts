/**
 * UI-2 — Tool-Registry: Lookup, Gruppen, Shortcut-Auflösung, Kollisionsfreiheit.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  TOOL_DEFINITIONS,
  toolNach,
  toolFuerShortcut,
  alleTools,
  werkzeugTools,
  shortcutKollisionen,
} from '../app/tools/toolRegistry';

test('die sechs Bestands-Werkzeuge sind registriert', () => {
  for (const id of ['auswahl', 'wand', 'fenster', 'tuer', 'dach', 'treppe']) {
    assert.ok(toolNach(id), `Werkzeug ${id} fehlt`);
  }
});

test('Lookup nach id und Shortcut (case-insensitiv)', () => {
  assert.equal(toolNach('wand')?.label, 'Wand');
  assert.equal(toolFuerShortcut('w')?.id, 'wand');
  assert.equal(toolFuerShortcut('R')?.id, 'treppe');
  assert.equal(toolNach('gibtsnicht'), undefined);
});

test('Gruppenfilter liefert nur Werkzeuge der Gruppe', () => {
  const oeffnungen = alleTools('oeffnungen').map((t) => t.id).sort();
  assert.deepEqual(oeffnungen, ['fenster', 'tuer']);
  assert.equal(alleTools().length, TOOL_DEFINITIONS.length);
});

test('keine Shortcut-Kollisionen (§29)', () => {
  assert.deepEqual(shortcutKollisionen(), []);
});

test('jedes Werkzeug hat Label, Icon, Art und Hilfetext', () => {
  for (const t of TOOL_DEFINITIONS) {
    assert.ok(t.label.length > 0 && t.icon.length > 0 && t.helpText.length > 0, t.id);
    assert.ok(t.art === 'werkzeug' || t.art === 'aktion', `${t.id} braucht art`);
  }
});

test('werkzeugTools = genau die modus-schaltenden Werkzeuge der Leiste (UI-3)', () => {
  const ids = werkzeugTools().map((t) => t.id);
  assert.deepEqual(ids, ['auswahl', 'wand', 'fenster', 'tuer', 'dach', 'treppe']);
  // Aktionen (Löschen/Duplizieren) gehören NICHT in die Werkzeugleiste
  assert.ok(!ids.includes('loeschen') && !ids.includes('duplizieren'));
});
