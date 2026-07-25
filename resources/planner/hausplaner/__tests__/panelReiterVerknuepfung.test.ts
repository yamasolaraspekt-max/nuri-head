/**
 * AUF-19 / Befunde B3 + B4 — die Reiter-Verknüpfung des Eigenschaften-Panels.
 *
 * B3: `role="tabpanel"`, `aria-controls` und die `id`-Verknüpfung fehlten; die Reiterleiste
 * versprach ein Panel, das im Baum keine Rolle trug.
 * B4: `tabIndex={aktivT ? 0 : -1}` war gesetzt, aber die Pfeiltasten zogen den DOM-Fokus nicht
 * mit — nach ArrowRight lagen sichtbarer Fokus und ausgewählter Reiter auseinander.
 *
 * Warum Quelltext statt Render: dieselbe Lage wie in `kontextOptionenLeiste.test.ts` — die
 * Testumgebung hat KEIN DOM, und `HausplanerApp.tsx` zieht react-konva und three nach. Ein
 * `focus()`-Effekt ist ohne DOM nicht messbar; das ist hier ausdrücklich benannt und NICHT als
 * Verhaltensbeleg behauptet. Geprüft wird die Verdrahtung, die das Verhalten erzeugt. Das Muster
 * (Quelltext lesen, Attribute prüfen) ist aus AUF-16 übernommen — kein zweites erfunden.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { PANEL_TABS } from '../app/dashboard/panelTabs';

const quelle = readFileSync(
  join(dirname(fileURLToPath(import.meta.url)), '../app/HausplanerApp.tsx'),
  'utf8',
);

/** Der `<button role="tab" …>`-Block — eine Stelle, aus der alle vier Reiter entstehen. */
const reiterBlock = quelle.match(/<button\n[\s\S]*?role="tab"[\s\S]*?\n\s*>/);

test('es gibt genau EINE Erzeugungsstelle für die Reiter (PANEL_TABS.map) — keine Parallelliste', () => {
  assert.equal((quelle.match(/role="tab"/g) ?? []).length, 1);
  assert.match(quelle, /\{PANEL_TABS\.map\(\(tab, i\) => \{/);
  // Was für einen Reiter gilt, gilt damit für alle vier — ohne sie einzeln aufzuzählen.
  assert.equal(PANEL_TABS.length, 4);
});

test('B3: jeder Reiter trägt aria-controls UND eine eigene id', () => {
  assert.ok(reiterBlock, 'Reiter-Button nicht gefunden');
  assert.match(reiterBlock[0], /id=\{reiterId\(tab\.id\)\}/);
  assert.match(reiterBlock[0], /aria-controls=\{PANEL_ID\}/);
});

test('B3: der Inhaltsbereich ist ein tabpanel und trägt genau die id, auf die aria-controls zeigt', () => {
  const panel = quelle.match(/<div role="tabpanel"[^>]*>/);
  assert.ok(panel, 'Inhaltsbereich ohne role="tabpanel"');
  assert.match(panel[0], /id=\{PANEL_ID\}/);
  // Verweis ins Leere ausgeschlossen: aria-controls und id sind DIESELBE Konstante,
  // und diese Konstante ist genau einmal auf Modulebene definiert.
  assert.equal((quelle.match(/^const PANEL_ID = /gm) ?? []).length, 1);
  assert.equal((quelle.match(/id=\{PANEL_ID\}/g) ?? []).length, 1);
});

test('B3: das Panel nennt den aktiven Reiter als Beschriftung (aria-labelledby)', () => {
  const panel = quelle.match(/<div role="tabpanel"[^>]*>/);
  assert.ok(panel);
  assert.match(panel[0], /aria-labelledby=\{reiterId\(aktiverTab\)\}/);
});

test('Kante 5: beide IDs tragen ein gemeinsames Präfix (eine Stelle für eine Instanz-Nummer)', () => {
  assert.match(quelle, /^const PANEL_ID = 'hp-eigenschaften-panel';$/m);
  assert.match(quelle, /^const reiterId = \(id: PanelTabId\): string => `hp-eigenschaften-tab-\$\{id\}`;$/m);
});

test('B4: die Pfeiltasten ziehen den DOM-Fokus mit', () => {
  const tastenZweig = quelle.match(/if \(e\.key !== 'ArrowLeft' && e\.key !== 'ArrowRight'\) return;[\s\S]*?\n\s*\}\}/);
  assert.ok(tastenZweig, 'Pfeiltasten-Zweig nicht gefunden');
  assert.match(tastenZweig[0], /setAktiverTab\(ziel\)/);
  assert.match(tastenZweig[0], /reiterRefs\.current\[ziel\]\?\.focus\(\)/);
  // Der Reiter muss dafür eine Referenz hinterlegen, sonst zeigt die Karte ins Leere.
  assert.ok(reiterBlock);
  assert.match(reiterBlock[0], /ref=\{\(el\) => \{ reiterRefs\.current\[tab\.id\] = el; \}\}/);
});

test('Kante 6: der Mausklick löst KEIN focus() aus — der Fokusring springt nicht bei jedem Klick auf', () => {
  const klick = quelle.match(/onClick=\{\(\) => setAktiverTab\(tab\.id\)\}/);
  assert.ok(klick, 'onClick des Reiters nicht gefunden');
  assert.doesNotMatch(klick[0], /focus\(\)/);
  // focus() kommt im Reiter-Bereich ausschliesslich einmal vor: im Tasten-Zweig.
  assert.equal((quelle.match(/reiterRefs\.current\[[a-zA-Z.]+\]\?\.focus\(\)/g) ?? []).length, 1);
});
