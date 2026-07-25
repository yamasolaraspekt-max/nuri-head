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
test('jede Registry- und Katalog-id hat genau eine Regel (9 + 101 = 110, keine Dublette)', () => {
  // I2: der Katalog ist seit dem Tausch das 110er-Fachpaket (vorher 54 InDesign-Einträge).
  assert.equal(TOOL_DEFINITIONS.length, 9);
  assert.equal(TOOL_KATALOG.length, 101);
  assert.equal(TOOL_PRESENTATION_RULES.length, 110);

  const ids = TOOL_PRESENTATION_RULES.map((r) => r.toolId);
  assert.equal(new Set(ids).size, ids.length, 'keine doppelte toolId');

  for (const t of [...TOOL_DEFINITIONS, ...TOOL_KATALOG]) {
    assert.ok(praesentation(t.id), `${t.id} braucht genau eine Präsentationsregel`);
  }
  assert.deepEqual(regelloseWerkzeuge(), [], 'kein Werkzeug ohne Zone');
});

test('Zonen nach I2/AUF-31: 7 fix · 2 kontext · 0 weitere · 101 versteckt', () => {
  assert.equal(zoneTools('fix').length, 7);
  assert.equal(zoneTools('kontext').length, 2);
  // DER Punkt von AUF-28: `weitere` ist LEER. Vorher standen dort 15 Werkzeuge, die die Navi
  // anzeigte, ohne dass ein Klick etwas tat — falsche Versprechen. Die neuen 110 versprechen
  // nichts, solange I3 sie nicht einordnet.
  assert.equal(zoneTools('weitere').length, 0, 'kein Werkzeug ohne Handler in der sichtbaren Zone');
  assert.equal(zoneTools('versteckt').length, 101);
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
  assert.equal(zoneToolsIn(kopie, 'weitere').length, 0);
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
test('die DTP-Reste sind aus den Regeln verschwunden — nicht nur versteckt', () => {
  const alle = TOOL_PRESENTATION_RULES.map((r) => r.toolId);
  for (const id of [
    'type', 'page', 'preflight', 'swatches-panel', 'pages-panel',
    'rectangle-frame', 'pen', 'note', 'object-style', 'eyedropper',
  ]) {
    assert.ok(!alle.includes(id), `${id} ist ein DTP-Rest und darf in keiner Zone mehr auftauchen`);
  }
  const versteckt = zoneTools('versteckt').map((t) => t.id);
  assert.equal(versteckt.length, 101);
  // kein Datenverlust: der Katalog trägt sie weiterhin
  for (const id of versteckt) {
    assert.ok(TOOL_KATALOG.some((t) => t.id === id), `${id} bleibt als Katalog-Eintrag erhalten`);
  }
});

test('AUF-31: gleichbedeutende Werkzeuge sind zusammengeführt, nicht doppelt geführt', () => {
  // Vorher: `auswahl` (Registry) und `select` (Paket) waren zwei Einträge für dasselbe Werkzeug.
  // Nach Weg 1 gibt es je EINE Regel — in der Registry, mit den Metadaten des Pakets.
  assert.equal(praesentation('auswahl')?.herkunft, 'registry');
  assert.equal(praesentation('select'), undefined, 'die englische Dublette ist verschwunden');
  assert.equal(praesentation('auswahl')?.zone, 'fix');
  // ein echtes Paket-Werkzeug bleibt Katalog-Herkunft
  assert.equal(praesentation('raum')?.herkunft, 'katalog');
});

// --- 5) Regressionsanker: die Navi verhält sich unverändert ------------------------------------
test('Regressionsanker: faehigkeitenNach(werkzeuge) bleibt nach der Fachzuordnung in derselben Reihenfolge', () => {
  // `decke` wurde nach Yamas Fachentscheidung bewusst nach `bau` verschoben; der übrige Stand bleibt
  // hart hinterlegt aus Registry-Reihenfolge + bisheriger CAD_TEILMENGE.
  // I2/AUF-28: die 15 `cad-*`-Einträge sind WEG — sie kamen aus der Zone `weitere`, und die ist
  // seit dem Katalog-Tausch leer. Übrig bleiben die echten Registry-Werkzeuge der Gruppe.
  const vorher = ['auswahl', 'loeschen', 'duplizieren'];
  assert.deepEqual(faehigkeitenNach('werkzeuge').map((f) => f.id), vorher);
});

// --- 6) Konsolidierungs-Schutz ----------------------------------------------------------------
test('doppelteIds() der Fähigkeiten bleibt leer', () => {
  assert.deepEqual(doppelteIds(), []);
});
