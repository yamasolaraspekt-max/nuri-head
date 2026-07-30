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
// AUF-48: die Hauptansicht ist zerlegt — diese Zusage liest ALLE ihre Teile.
import { zerlegteApp } from './_zerlegteApp';

const hier = dirname(fileURLToPath(import.meta.url));

/**
 * Kommentare entfernen, bevor über den Quelltext geprüft wird. Sonst schlägt der Test auf den
 * erklärenden Kommentar an, der den alten Aufruf beim Namen nennt („`zoneTools('fix')` statt
 * `werkzeugTools()`") — und misst damit Prosa statt Code. Geprüft werden soll der Aufruf.
 */
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');

const appQuelle = ohneKommentare(zerlegteApp());
const zonenQuelle = ohneKommentare(readFileSync(join(hier, '../app/tools/toolPresentation.ts'), 'utf8'));
/** AUF-48 Scheibe 2: die Rail-Ableitung wohnt hier — die Zaehlung laeuft ueber beide Dateien. */
const ableitungenQuelle = ohneKommentare(readFileSync(join(hier, '../app/ableitungen.ts'), 'utf8'));

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
  assert.doesNotMatch(ableitungenQuelle, /werkzeugTools\(\)/, 'auch nicht in den Ableitungen');
  // Seit I4 zwei Aufrufe: die Fix-Zone selbst und die Rail (fix + persönlich Angeheftetes).
  //
  // **AUF-48 Scheibe 2: die Rail-Ableitung ist nach `ableitungen.leisteMitAngehefteten` gezogen.**
  // Die Aussage bleibt: **zwei** Aufrufe insgesamt, jeder an seinem Ort — einer in der App
  // (memoisiert), einer in der reinen Funktion. Gezählt wird deshalb über BEIDE Dateien; ein
  // dritter Aufruf wäre weiterhin ein zweiter Ort für dieselbe Frage.
  const treffer = [...(appQuelle.match(/zoneTools\(/g) ?? []), ...(ableitungenQuelle.match(/zoneTools\(/g) ?? [])];
  assert.equal(treffer.length, 2, 'Fix-Zone (App) und Rail-Ableitung (ableitungen.ts)');
  // In der App gehört der Aufruf weiterhin in ein `useMemo` — der Punkt von P9 war der Render-Pfad.
  for (const m of appQuelle.matchAll(/zoneTools\('fix'\)/g)) {
    const davor = appQuelle.slice(Math.max(0, m.index - 200), m.index);
    assert.match(davor, /useMemo/, 'jeder Aufruf in der App gehört in ein useMemo, nicht in den JSX-Ausdruck');
  }
  // Und in der reinen Funktion steht er in einer benannten Funktion, nicht auf Modulebene —
  // sonst liefe er einmal beim Laden und wäre gegen geänderte Regelsätze blind (der Grund, aus dem
  // ein Modul-Cache hier ausdrücklich verboten ist).
  const railStelle = ableitungenQuelle.indexOf("zoneTools('fix')");
  assert.ok(railStelle > ableitungenQuelle.indexOf('export function leisteMitAngehefteten'),
    'der Rail-Aufruf steht nicht in seiner Funktion');
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
test('Auflage 4: jede der 110 Regeln trägt eine herkunft, die der Wirklichkeit entspricht', () => {
  assert.equal(TOOL_PRESENTATION_RULES.length, 110);
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
