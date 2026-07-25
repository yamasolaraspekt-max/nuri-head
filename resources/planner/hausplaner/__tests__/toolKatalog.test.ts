import { test } from 'node:test';
import assert from 'node:assert/strict';
import { TOOL_KATALOG, katalogTool } from '../app/tools/toolCatalog';
import { STILLGELEGT_INDESIGN_KATALOG } from '../app/tools/toolCatalogStillgelegt';
import { verworfeneKuerzel, kuerzelFrei } from '../app/tools/paketAdapter';
import { resolveToolState } from '../app/tools/activation';
import { baueAktivierungsKontext } from '../app/tools/toolContext';

/**
 * I2 (AUF-21) — der Katalog ist seit dem Tausch Yamas 110er-Fachpaket.
 * Vorher: 54 Einträge aus einem InDesign-Paket, davon 47 DTP-Reste. Diese Datei prüft jetzt den
 * neuen Zustand UND dass die Ablösung wirklich stattgefunden hat — beides, sonst wäre der Tausch
 * nur behauptet.
 */

const DTP_RESTE = ['page', 'type', 'eyedropper', 'pen', 'rectangle-frame', 'swatches-panel', 'preflight', 'pages-panel', 'note', 'object-style'];

test('Katalog: 110 Fach-Werkzeuge aus dem Paket', () => {
  assert.equal(TOOL_KATALOG.length, 110);
});

test('die DTP-Reste sind aus dem Katalog verschwunden — das war der Zweck des Tauschs', () => {
  for (const id of DTP_RESTE) {
    assert.equal(katalogTool(id), undefined, `${id} steht dem Bauplaner im Weg und darf nicht mehr im Katalog sein`);
  }
});

test('der abgelöste Katalog bleibt als Trail erhalten (stillgelegt, nicht gelöscht)', () => {
  assert.equal(STILLGELEGT_INDESIGN_KATALOG.length, 54);
  const alteIds = new Set(STILLGELEGT_INDESIGN_KATALOG.map((t) => t.id));
  const neueIds = new Set(TOOL_KATALOG.map((t) => t.id));
  const weggefallen = [...alteIds].filter((id) => !neueIds.has(id));
  assert.equal(weggefallen.length, 47, 'gemessen beim Tausch: 47 ohne Entsprechung im Fachpaket');
});

test('Fach-Werkzeuge sind da, wo vorher DTP stand', () => {
  // gemessen am Paket, nicht geraten: Kategorie „Architektur" führt wall/room/door/window/stairs/roof
  for (const id of ['wall', 'room', 'door', 'window', 'stairs', 'roof']) {
    assert.ok(katalogTool(id), `${id} gehört in einen Bauplaner`);
  }
  assert.equal(katalogTool('select')?.label, 'Auswahl', 'Labels sind deutsch');
  assert.equal(katalogTool('wall')?.groupId, 'Architektur', 'Kategorie wird zur Gruppe');
});

test('Metadaten übernommen: Funktion, Einsatzbereich, Tooltip', () => {
  const t = katalogTool('select')!;
  assert.ok(t.helpText.length > 5);
  assert.ok(t.usageArea && t.usageArea.length > 5);
  assert.ok(t.tooltip?.title && t.tooltip?.body && t.tooltip?.usage);
});

test('Activation-Engine greift unverändert auf den neuen Katalog', () => {
  const werkzeug = katalogTool('wall')!;
  const kontext2d = baueAktivierungsKontext({ workspace: 'architektur', view: '2d', selectionTypes: [], permissions: [] });
  assert.equal(resolveToolState(werkzeug, kontext2d).enabled, true);
  const kontext3d = baueAktivierungsKontext({ workspace: 'architektur', view: '3d', selectionTypes: [], permissions: [] });
  const z3 = resolveToolState(werkzeug, kontext3d);
  if (!werkzeug.supportedViews.includes('3d')) {
    assert.equal(z3.enabled, false, 'ein 2D-Werkzeug ist in der 3D-Ansicht nicht bedienbar');
    assert.ok(z3.reason && z3.reason.length > 0, 'und sagt warum');
  }
});

test('Auflage 1 der A2-Abnahme: kein kollidierendes Kürzel im Katalog', () => {
  const kuerzel = TOOL_KATALOG.map((t) => t.shortcut).filter((s): s is string => Boolean(s)).map((s) => s.toLowerCase());
  assert.equal(new Set(kuerzel).size, kuerzel.length, 'zwei Katalog-Werkzeuge mit gleichem Kürzel');
  for (const k of kuerzel) assert.ok(kuerzelFrei(k), `${k} kollidiert mit der Registry`);
});

test('die verworfenen Kürzel sind ausgewiesen, nicht stillschweigend geschluckt', () => {
  const raus = verworfeneKuerzel();
  assert.ok(raus.length > 0, 'das Paket bringt nachweislich Kollisionen mit');
  for (const r of raus) assert.ok(r.grund.length > 10, `${r.id}: Grund fehlt`);
});
