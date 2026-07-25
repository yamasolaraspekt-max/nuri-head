/**
 * Welle A2 (AUF-4) — die Werkzeugleiste bezieht ihre Zugehörigkeit aus der Präsentationsschicht.
 *
 * Kern der Welle: Über die Frage „welches Werkzeug steht in der Leiste?" entschieden bisher **zwei**
 * Mechanismen unabhängig voneinander — `art === 'werkzeug'` in der Registry und `zone === 'fix'` in
 * den Präsentationsregeln. Sie stimmten zufällig überein. Nach A2 entscheidet nur noch die Zone.
 *
 * Der Umbau ist heute **verhaltensneutral** — genau das belegt der erste Test, und genau deshalb ist
 * er überhaupt verantwortbar: er ändert nichts Sichtbares und macht danach eine Stelle zuständig.
 *
 * Zusätzlich verriegelt diese Datei die vier Auflagen aus dem A1-Wiederholungsvotum, soweit sie ohne
 * DOM messbar sind (Auflage 3 siehe Bericht — `.tsx` ist im Testlauf nicht ladbar).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { zoneTools, zoneToolsIn, praesentation, TOOL_PRESENTATION_RULES, type ToolPresentationRule } from '../app/tools/toolPresentation';
import { TOOL_DEFINITIONS, werkzeugTools, shortcutKollisionen, toolNach } from '../app/tools/toolRegistry';
import { katalogTool } from '../app/tools/toolCatalog';

const hier = dirname(fileURLToPath(import.meta.url));

/**
 * Kommentare entfernen, bevor über den Quelltext geprüft wird. Sonst schlägt der Test auf den
 * erklärenden Kommentar an, der den alten Aufruf beim Namen nennt („`zoneTools('fix')` statt
 * `werkzeugTools()`") — und misst damit Prosa statt Code. Geprüft werden soll der Aufruf.
 */
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');

const appQuelle = ohneKommentare(readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8'));
const zonenQuelle = ohneKommentare(readFileSync(join(hier, '../app/tools/toolPresentation.ts'), 'utf8'));

// --- 1) Der Umbau ist verhaltensneutral (Abnahmekriterium 6) ----------------------------------
test('Leiste == Fix-Zone: dieselben ids in derselben Reihenfolge wie die alte Registry-Quelle', () => {
  const alt = werkzeugTools().map((t) => t.id);
  const neu = zoneTools('fix').map((t) => t.id);
  assert.deepEqual(neu, alt, 'A2 muss heute verhaltensneutral sein — sonst wandern Icons');
  assert.deepEqual(neu, ['auswahl', 'wand', 'fenster', 'tuer', 'dach', 'decke', 'treppe']);
});

// --- 2) Es gibt nur noch EINE zuständige Stelle (Abnahmekriterium 5) ---------------------------
test('die Leiste liest zoneTools, nicht mehr werkzeugTools', () => {
  assert.doesNotMatch(appQuelle, /werkzeugTools\(\)/, 'werkzeugTools darf in der App nicht mehr vorkommen');
  const treffer = appQuelle.match(/zoneTools\(/g) ?? [];
  assert.equal(treffer.length, 1, 'genau eine Aufrufstelle');
});

// --- 3) §8.2 / P9: memoisiert am Aufrufort, KEIN Modul-Cache ----------------------------------
test('der Aufruf steht in einem useMemo, nicht im JSX-Ausdruck', () => {
  assert.match(appQuelle, /useMemo\(\(\) => zoneTools\('fix'\), \[\]\)/);
});

test('toolPresentation.ts bleibt rein — kein veränderlicher Modul-Zustand', () => {
  assert.doesNotMatch(zonenQuelle, /\bnew Map<RailZone/);
  assert.doesNotMatch(zonenQuelle, /^\s*(let|var)\s/m, 'kein veränderlicher Modul-Zustand');
  assert.doesNotMatch(zonenQuelle, /\bcache\b/i, 'ein Modul-Cache würde die A1-Gegenproben entwerten');
});

// --- 4) Auflage 1 des A1-Votums: Shortcut-Kollisionen verriegeln -------------------------------
test('Auflage 1: die Leiste ist kollisionsfrei — kein Kürzel doppelt', () => {
  assert.deepEqual(shortcutKollisionen(), [], 'Registry-Kürzel müssen eindeutig bleiben');
  const kuerzel = zoneTools('fix')
    .map((t) => t.shortcut)
    .filter((s): s is string => Boolean(s))
    .map((s) => s.toLowerCase());
  assert.equal(new Set(kuerzel).size, kuerzel.length, 'zwei Leisten-Werkzeuge mit gleichem Kürzel');
});

test('Auflage 1: kein Katalog-Werkzeug ist unbemerkt in die Leiste gerutscht', () => {
  for (const t of zoneTools('fix')) {
    assert.equal(praesentation(t.id)?.herkunft, 'registry', `${t.id} kommt nicht aus der Registry`);
  }
});

// --- 5) Auflage 4 des A1-Votums: `herkunft` ist für alle 63 Regeln verriegelt ------------------
test('Auflage 4: jede der 119 Regeln trägt eine herkunft, die der Wirklichkeit entspricht', () => {
  assert.equal(TOOL_PRESENTATION_RULES.length, 119);
  for (const r of TOOL_PRESENTATION_RULES) {
    const inRegistry = toolNach(r.toolId) !== undefined;
    const imKatalog = katalogTool(r.toolId) !== undefined;
    if (r.herkunft === 'registry') {
      assert.ok(inRegistry, `${r.toolId}: als 'registry' geführt, steht aber nicht in TOOL_DEFINITIONS`);
    } else {
      assert.ok(imKatalog, `${r.toolId}: als 'katalog' geführt, steht aber nicht im TOOL_KATALOG`);
      assert.ok(!inRegistry, `${r.toolId}: als 'katalog' geführt, ist aber eine Registry-id`);
    }
  }
  assert.equal(TOOL_PRESENTATION_RULES.filter((r) => r.herkunft === 'registry').length, TOOL_DEFINITIONS.length);
});

// --- 6) Die Gegenprobe muss NACH der Memoisierung weiter greifen (§8.3) ------------------------
test('GEGENPROBE: eine fix-Regel auf versteckt ⇒ die Leiste schrumpft auf 6', () => {
  const kopie: ToolPresentationRule[] = TOOL_PRESENTATION_RULES.map((r) =>
    r.toolId === 'dach' ? { ...r, zone: 'versteckt' as const } : r,
  );
  const fix = zoneToolsIn(kopie, 'fix').map((t) => t.id);
  assert.equal(fix.length, 6);
  assert.ok(!fix.includes('dach'));
  // echte Daten unberührt — die Gegenprobe läuft über zoneToolsIn, nicht über den memoisierten Aufruf
  assert.equal(zoneTools('fix').length, 7);
});
