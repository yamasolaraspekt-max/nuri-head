/**
 * Wizard-Welle A1 — Werkzeug-Präsentationsschicht.
 *
 * Prüft, dass die Kuratierung als DATEN vollständig, verwaisungsfrei und verhaltensneutral ist:
 * jedes Werkzeug aus Registry ODER Katalog hat genau eine Zone; die Fix-Zone ist die Registry;
 * die Navi liefert nach der Umstellung exakt dieselben ids in derselben Reihenfolge wie vorher.
 * Jede Invariante hat eine Rot-Gegenprobe (lokale Regel-Kopie, echte Daten bleiben unberührt).
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
  TOOL_PRESENTATION_RULES,
  praesentation,
  zoneTools,
  zoneToolsIn,
  verwaisteRegeln,
  verwaisteRegelnIn,
  regelloseWerkzeuge,
  type ToolPresentationRule,
} from '../app/tools/toolPresentation';
import { TOOL_DEFINITIONS } from '../app/tools/toolRegistry';
import { TOOL_KATALOG } from '../app/tools/toolCatalog';
import { faehigkeitenNach, doppelteIds } from '../app/tools/faehigkeiten';

// --- 1) Vollständigkeit ---------------------------------------------------------------------
test('jede Registry- und Katalog-id hat genau eine Regel (9 + 54 = 63, keine Dublette)', () => {
  assert.equal(TOOL_DEFINITIONS.length, 9);
  assert.equal(TOOL_KATALOG.length, 54);
  assert.equal(TOOL_PRESENTATION_RULES.length, 63);

  const ids = TOOL_PRESENTATION_RULES.map((r) => r.toolId);
  assert.equal(new Set(ids).size, ids.length, 'keine doppelte toolId');

  for (const t of [...TOOL_DEFINITIONS, ...TOOL_KATALOG]) {
    assert.ok(praesentation(t.id), `${t.id} braucht genau eine Präsentationsregel`);
  }
  assert.deepEqual(regelloseWerkzeuge(), [], 'kein Werkzeug ohne Zone');
});

test('Zonen-Aufteilung entspricht dem gemessenen Ist-Zustand (7 / 2 / 15 / 39)', () => {
  assert.equal(zoneTools('fix').length, 7);
  assert.equal(zoneTools('kontext').length, 2);
  assert.equal(zoneTools('weitere').length, 15);
  assert.equal(zoneTools('versteckt').length, 39);
});

// --- 2) Keine verwaisten Regeln (+ Rot-Gegenprobe) -------------------------------------------
test('verwaisteRegeln() ist leer', () => {
  assert.deepEqual(verwaisteRegeln(), []);
});

test('GEGENPROBE: eine erfundene id in einer lokalen Regel-Kopie wird als verwaist gemeldet', () => {
  const kopie: ToolPresentationRule[] = [
    ...TOOL_PRESENTATION_RULES,
    { toolId: 'gibt-es-nicht', zone: 'weitere', ordnung: 99, herkunft: 'katalog', begruendung: 'Testfall' },
  ];
  assert.deepEqual(verwaisteRegelnIn(kopie), ['gibt-es-nicht']);
  // und sie taucht NICHT in der Zone auf (auslassen statt werfen)
  assert.equal(zoneToolsIn(kopie, 'weitere').length, 15);
});

// --- 3) Invariante Fix-Zone (+ Rot-Gegenprobe) -----------------------------------------------
test('Fix-Zone = genau die 7 art:werkzeug-Registry-ids in Registry-Reihenfolge', () => {
  const erwartet = TOOL_DEFINITIONS.filter((t) => t.art === 'werkzeug').map((t) => t.id);
  assert.deepEqual(erwartet, ['auswahl', 'wand', 'fenster', 'tuer', 'dach', 'decke', 'treppe']);
  assert.deepEqual(zoneTools('fix').map((t) => t.id), erwartet);
});

test('keine Registry-id liegt in der versteckten Zone', () => {
  const versteckt = new Set(zoneTools('versteckt').map((t) => t.id));
  for (const t of TOOL_DEFINITIONS) {
    assert.ok(!versteckt.has(t.id), `${t.id} ist ein echtes Werkzeug und darf nicht versteckt sein`);
  }
});

test('GEGENPROBE: wand auf versteckt gesetzt ⇒ Fix-Invariante bricht', () => {
  const kopie: ToolPresentationRule[] = TOOL_PRESENTATION_RULES.map((r) =>
    r.toolId === 'wand' ? { ...r, zone: 'versteckt' as const } : r,
  );
  const fix = zoneToolsIn(kopie, 'fix').map((t) => t.id);
  assert.equal(fix.length, 6, 'die Fix-Zone hätte ein Werkzeug verloren');
  assert.ok(!fix.includes('wand'));
  assert.ok(zoneToolsIn(kopie, 'versteckt').some((t) => t.id === 'wand'));
  // echte Daten unberührt
  assert.deepEqual(zoneTools('fix').map((t) => t.id).includes('wand'), true);
});

// --- 4) Kuratierungs-Beweis -------------------------------------------------------------------
test('die DTP/Layout-Werkzeuge liegen namentlich in der versteckten Zone (39 Stück)', () => {
  const versteckt = zoneTools('versteckt').map((t) => t.id);
  for (const id of [
    'type', 'page', 'preflight', 'swatches-panel', 'pages-panel',
    'rectangle-frame', 'pen', 'note', 'object-style',
  ]) {
    assert.ok(versteckt.includes(id), `${id} gehört in die versteckte Zone`);
  }
  assert.equal(versteckt.length, 39);
  // kein Datenverlust: der Katalog trägt sie weiterhin
  for (const id of versteckt) {
    assert.ok(TOOL_KATALOG.some((t) => t.id === id), `${id} bleibt als Katalog-Eintrag erhalten`);
  }
});

test('Registry-Vorrang: verschiedene ids werden nicht vereinheitlicht (auswahl ≠ selection)', () => {
  assert.equal(praesentation('auswahl')?.herkunft, 'registry');
  assert.equal(praesentation('selection')?.herkunft, 'katalog');
  assert.equal(praesentation('auswahl')?.zone, 'fix');
  assert.equal(praesentation('selection')?.zone, 'versteckt');
});

// --- 5) Regressionsanker: die Navi verhält sich unverändert ------------------------------------
test('Regressionsanker: faehigkeitenNach(werkzeuge) liefert dieselben ids in derselben Reihenfolge', () => {
  // Hart hinterlegt = Stand VOR der Umstellung (aus Registry-Reihenfolge + bisheriger CAD_TEILMENGE).
  const vorher = [
    'auswahl', 'decke', 'loeschen', 'duplizieren',
    'cad-rotate', 'cad-scale', 'cad-free-transform',
    'cad-align-left', 'cad-align-center', 'cad-align-right',
    'cad-align-top', 'cad-align-middle', 'cad-align-bottom',
    'cad-distribute-horizontal', 'cad-distribute-vertical',
    'cad-hand', 'cad-zoom', 'cad-measure', 'cad-layers-panel',
  ];
  assert.deepEqual(faehigkeitenNach('werkzeuge').map((f) => f.id), vorher);
});

// --- 6) Konsolidierungs-Schutz ----------------------------------------------------------------
test('doppelteIds() der Fähigkeiten bleibt leer', () => {
  assert.deepEqual(doppelteIds(), []);
});
