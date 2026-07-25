import { test } from 'node:test';
import assert from 'node:assert/strict';
import { TOOL_KATALOG, katalogTool } from '../app/tools/toolCatalog';
import {
  FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_GESCHOSS_DA, FAEHIGKEIT_WAND_DA,
  FAEHIGKEIT_ANSICHT_BEREIT, RECHT_BEARBEITEN,
} from '../app/tools/vorbedingungen';
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

test('Katalog: 101 Fach-Werkzeuge — 110 Paket minus 9 zusammengeführte', () => {
  // AUF-31 Weg 1: neun Paket-Werkzeuge waren dieselben wie neun Registry-Werkzeuge
  // (select/wall/door/window/stairs/roof/slab/duplicate/delete). Sie stehen jetzt EINMAL,
  // nämlich in der Registry — 110 eindeutige Werkzeuge bleiben es trotzdem.
  assert.equal(TOOL_KATALOG.length, 101);
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
  // Nach der Eindeutschung (AUF-31) tragen die neuen ids deutsche Namen; die 7 vormals
  // gleichnamigen (line/polygon/rectangle/…) heißen jetzt anders. Gemessen: 52.
  assert.equal(weggefallen.length, 52, 'kein alter InDesign-Eintrag steht mehr im Katalog');
});

test('Fach-Werkzeuge sind da, wo vorher DTP stand', () => {
  // gemessen an der Namenstabelle, nicht geraten: room→raum, dormer→gaube,
  // roof-window→dachfenster, column→stuetze
  for (const id of ['raum', 'gaube', 'dachfenster', 'stuetze']) {
    assert.ok(katalogTool(id), `${id} gehört in einen Bauplaner`);
  }
  assert.equal(katalogTool('raum')?.label, 'Raum', 'Labels sind deutsch');
  assert.equal(katalogTool('raum')?.groupId, 'Architektur', 'Kategorie wird zur Gruppe');
  // AUF-31 Weg 1: die neun zusammengeführten stehen in der Registry, NICHT doppelt im Katalog.
  for (const id of ['wand', 'fenster', 'tuer', 'dach', 'decke', 'treppe', 'auswahl']) {
    assert.equal(katalogTool(id), undefined, `${id} gehört in die Registry, nicht in den Katalog`);
  }
});

test('Metadaten übernommen: Funktion, Einsatzbereich, Tooltip', () => {
  const t = katalogTool('raum')!;
  assert.ok(t.helpText.length > 5);
  assert.ok(t.usageArea && t.usageArea.length > 5);
  assert.ok(t.tooltip?.title && t.tooltip?.body && t.tooltip?.usage);
});

/**
 * AUF-36 hat die **Daten** geändert, nicht die Engine: die Katalog-Werkzeuge tragen jetzt die
 * Vorbedingungen ihres Funktionsvertrags als `activationRules`. Der Kontext dieses Tests bildet
 * deshalb ab, was die App tatsächlich mitgibt — offener Plan, aktives Geschoss, Bearbeitungsrecht.
 * Ein Kontext ohne all das ist kein „Normalfall", sondern der Fall „nichts geladen"; er steht
 * unten als eigene, rote Gegenprobe.
 */
const NORMALFALL = {
  workspace: 'architektur', view: '2d' as const, selectionTypes: [],
  permissions: [RECHT_BEARBEITEN],
  capabilities: [FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_GESCHOSS_DA, FAEHIGKEIT_ANSICHT_BEREIT, FAEHIGKEIT_WAND_DA],
};

test('Activation-Engine greift unverändert auf den neuen Katalog', () => {
  const werkzeug = katalogTool('raum')!;
  const kontext2d = baueAktivierungsKontext(NORMALFALL);
  assert.equal(resolveToolState(werkzeug, kontext2d).enabled, true);
  const kontext3d = baueAktivierungsKontext({ ...NORMALFALL, view: '3d' });
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
