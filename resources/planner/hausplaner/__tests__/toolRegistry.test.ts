/**
 * UI-2 — Tool-Registry: Lookup, Gruppen, Shortcut-Auflösung, Kollisionsfreiheit.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  AUS_PAKET_GEHOBEN,
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
  // **W-05: die zwei gehobenen Werkzeuge stehen jetzt in der Leiste — das ist der Zweck.**
  // Die Liste bleibt AUSGESCHRIEBEN und wird nicht aus der Registry abgeleitet: eine Zusage, die
  // ihre Erwartung aus dem Pruefling holt, ist immer gruen. *Was hier steht, ist Yamas Leiste —
  // sie soll sich nur aendern, wenn jemand sie aendern WOLLTE.*
  assert.deepEqual(ids, [
    'auswahl', 'wand', 'fenster', 'tuer', 'dach', 'decke', 'treppe',
    'bemassen', 'flaeche-messen',   // <- W-05
    'kontur',
  ]);
  // Und die Verbindung zur Hebeliste, damit beide nicht auseinanderlaufen koennen.
  // **A-35 (15.08.):** ein gehobenes Werkzeug muss in der Leiste ANKOMMEN — als modus-schaltendes
  // Werkzeug ODER als Aktion. *`trimmen` ist das erste gehobene, das sofort auf der Auswahl wirkt;
  // als `art: 'werkzeug'` schaltete der Klick in einen Modus, den kein Renderer bedient.* Die
  // Zusage prueft weiterhin, dass die Hebeliste nicht ins Leere zeigt — nur nicht mehr, dass jedes
  // gehobene Werkzeug ein MODUS ist.
  for (const id of AUS_PAKET_GEHOBEN) {
    const alsAktion = TOOL_DEFINITIONS.find((t) => t.id === id && t.art === 'aktion');
    assert.ok(ids.includes(id) || alsAktion, `${id} ist gehoben, kommt aber in der Leiste nicht an`);
  }
  // Aktionen (Löschen/Duplizieren) gehören NICHT in die Werkzeugleiste
  assert.ok(!ids.includes('loeschen') && !ids.includes('duplizieren'));
});
