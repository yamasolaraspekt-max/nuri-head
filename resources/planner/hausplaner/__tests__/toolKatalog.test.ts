import { test } from 'node:test';
import assert from 'node:assert/strict';
import { TOOL_KATALOG, katalogTool } from '../app/tools/toolCatalog';
import { resolveToolState } from '../app/tools/activation';
import { baueAktivierungsKontext } from '../app/tools/toolContext';

const DTP = ['content-collector','content-placer','gradient','gradient-feather','text-wrap','format-text','effects','opacity','libraries-panel','links-panel','share'];

test('Katalog: 54 CAD-Tools (65 minus 11 DTP)', () => {
  assert.equal(TOOL_KATALOG.length, 54);
});
test('DTP-Tools sind gefiltert', () => {
  for (const id of DTP) assert.equal(katalogTool(id), undefined);
});
test('Metadaten uebernommen: align-left min. 2 Objekte', () => {
  assert.equal(katalogTool('align-left')?.minSelectionCount, 2);
});
test('Activation-Engine greift auf Katalog: align-left aus bei <2, aktiv bei >=2', () => {
  const tool = katalogTool('align-left')!;
  const leer = baueAktivierungsKontext({ workspace: 'architektur', view: '2d', selectionTypes: [], permissions: [] });
  const z0 = resolveToolState(tool, leer);
  assert.equal(z0.enabled, false);
  assert.ok(z0.reason && z0.reason.length > 0);
  const zwei = baueAktivierungsKontext({ workspace: 'architektur', view: '2d', selectionTypes: ['wall','wall'], permissions: [] });
  assert.equal(resolveToolState(tool, zwei).enabled, true);
});
test('Tooltip-Metadaten vorhanden', () => {
  const t = katalogTool('selection')!;
  assert.ok(t.tooltip?.title && t.tooltip?.body && t.tooltip?.usage);
});
