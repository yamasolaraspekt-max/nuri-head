/**
 * AUF-43 — die Geschoss-Bedienung.
 *
 * Geprüft wird, was der Auftrag als Abnahme benennt: der Name kommt **genau einmal** vor (K3), die
 * **Höhenlage** ist sichtbar (K4), Umbenennen läuft über `UPDATE_LEVEL` und ist **undo-fähig** (K5),
 * und Rückgängig/Wiederholen sowie 2D/Split/3D sind **nicht mehr Teil der Geschoss-Gruppe** (K6).
 *
 * Dazu der Stapel selbst: „drei Geschosse, das mittlere ist aktiv, eines darüber" ist eine Aussage
 * über das Modell — sie gehört geprüft, nicht angesehen.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { stapel, kurzfassung, hoehenLabel, nachbar } from '../app/dashboard/geschossStapel';
import { produceWithPatches, enablePatches } from 'immer';
import { applyCommand } from '../commands/applyCommand';
import type { Level, SceneDocument } from '../domain/scene.types';

enablePatches(); // wie in applyCommand.test.ts: ohne das Plugin gibt es keine inversen Patches

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const flaeche = ohneKommentare(readFileSync(join(hier, '../app/dashboard/GeschossFlaeche.tsx'), 'utf8'));
const app = ohneKommentare((readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8')
  // AUF-48 Scheibe 4a: der Kopfrahmen (Werkzeugzeile, Arbeitsbereich-Waehler,
  // Bedien-Werkzeugleiste) ist nach `dashboard/Kopfrahmen.tsx` ausgezogen. **Beide Dateien
  // werden gelesen** — die geprueften Eigenschaften sind unveraendert, und eine Absenz-Zusage
  // darf nicht dadurch gruen werden, dass Inhalt eine Datei weiter gewandert ist.
  + readFileSync(join(hier, '../app/dashboard/Kopfrahmen.tsx'), 'utf8')));

const ebene = (id: string, name: string, elevation: number, sortOrder: number): Level => ({
  id, name, elevation, defaultWallHeight: 2500, floorThickness: 200, sortOrder,
} as Level);

const DREI: Level[] = [
  ebene('kg', 'Kellergeschoss', -2800, 0),
  ebene('eg', 'Erdgeschoss', 0, 1),
  ebene('og', 'Obergeschoss', 2700, 2),
];

// --- Der Stapel -------------------------------------------------------------------------------
test('der Stapel steht von OBEN nach unten — so wird ein Gebäudeschnitt gelesen', () => {
  const s = stapel(DREI, 'eg');
  assert.deepEqual(s.eintraege.map((e) => e.name), ['Obergeschoss', 'Erdgeschoss', 'Kellergeschoss']);
  // die Positionsnummer zählt trotzdem von unten — 1 ist das unterste
  assert.deepEqual(s.eintraege.map((e) => e.position), [3, 2, 1]);
});

test('das aktive Geschoss ist markiert, und darüber/darunter stimmen', () => {
  const s = stapel(DREI, 'eg');
  assert.equal(s.aktiv?.id, 'eg');
  assert.equal(s.aktivPosition, 2);
  assert.equal(s.darueber, 1);
  assert.equal(s.darunter, 1);
  assert.equal(s.anzahl, 3);
});

test('unbekannte aktive id ⇒ kein Wurf, sondern `aktiv: null`', () => {
  const s = stapel(DREI, 'gibt-es-nicht');
  assert.equal(s.aktiv, null);
  assert.equal(s.aktivPosition, 0);
  assert.equal(s.eintraege.length, 3, 'der Stapel bleibt sichtbar');
  assert.equal(kurzfassung(s), '3 Geschosse');
});

test('leere Liste ⇒ leerer Stapel, kein Wurf', () => {
  const s = stapel([], null);
  assert.deepEqual(s.eintraege, []);
  assert.equal(s.anzahl, 0);
});

test('die Ordnung bleibt `sortOrder`, dann `elevation` — keine Sortierumkehr', () => {
  // gleiche sortOrder ⇒ die Höhenlage entscheidet
  const gleich = [ebene('a', 'A', 3000, 5), ebene('b', 'B', 1000, 5)];
  const s = stapel(gleich, 'a');
  assert.deepEqual([...s.eintraege].reverse().map((e) => e.name), ['B', 'A'], 'von unten: die tiefere zuerst');
});

test('Nachbar: darüber und darunter — am Rand `undefined` statt Absturz', () => {
  const s = stapel(DREI, 'og');
  assert.equal(nachbar(s, -1)?.name, 'Erdgeschoss');
  assert.equal(nachbar(s, 1), undefined, 'über dem obersten liegt nichts');
});

// --- K4: die Höhenlage ist sichtbar ------------------------------------------------------------
test('K4: die Höhenlage wird gezeigt — mit Vorzeichen und Tausendertrennung', () => {
  assert.equal(hoehenLabel(0), '±0 mm', 'das Erdgeschoss hat keine Richtung');
  assert.equal(hoehenLabel(2700), '+2\u202f700 mm', 'schmales geschütztes Leerzeichen, kein gewöhnliches');
  assert.equal(hoehenLabel(-2800), '−2\u202f800 mm');
  assert.equal(hoehenLabel(12500), '+12\u202f500 mm');
  // und sie steht wirklich in der Fläche, je Stapelzeile UND in der Kurzfassung
  assert.match(flaeche, /\{e\.hoehenLabel\}/);
  assert.match(kurzfassung(stapel(DREI, 'og')), /\+2\u202f700 mm/);
});

test('K4: die Kurzfassung nennt Name, Höhenlage und Position — die drei Angaben, die zählen', () => {
  assert.equal(kurzfassung(stapel(DREI, 'eg')), 'Erdgeschoss · ±0 mm · 2 von 3');
});

// --- K3: der Name kommt genau einmal vor -------------------------------------------------------
test('K3: in der Geschoss-Fläche tragen NICHT Select und Eingabefeld denselben Wert', () => {
  // Vorher standen ein `select` mit dem Namen und direkt daneben ein `input` mit demselben Wert.
  assert.equal((flaeche.match(/<select/g) ?? []).length, 0, 'der Stapel ist eine Liste, kein Select');
  assert.equal((flaeche.match(/<input/g) ?? []).length, 1, 'genau EIN Namensfeld');
  // und es ist sichtbar beschriftet — vorher erkannte niemand das Textfeld als solches
  assert.match(flaeche, /Name des aktiven Geschosses/);
});

test('K3: auch die App führt keinen zweiten Geschoss-Wähler mehr', () => {
  assert.doesNotMatch(app, /Geschoss wählen/, 'der alte Select ist weg');
  assert.doesNotMatch(app, /Geschoss umbenennen \(Enter bestätigt\)/, 'das alte Textfeld ist weg');
  assert.equal((app.match(/<GeschossFlaeche/g) ?? []).length, 1, 'genau eine Geschoss-Fläche');
});

// --- K6: die vier Aufgaben sind getrennt --------------------------------------------------------
test('K6: Rückgängig/Wiederholen und 2D/Split/3D sind nicht Teil der Geschoss-Gruppe', () => {
  for (const fremd of ['undo()', 'redo()', 'setModus', 'kannUndo', 'kannRedo']) {
    assert.ok(!flaeche.includes(fremd), `${fremd} hat mit dem Geschoss nichts zu tun`);
  }
  // Und die Fläche kennt auch das Speichern nicht.
  assert.ok(!flaeche.includes('save()'));
});

test('K6: die Fläche steht auf Modulebene, nicht im Rumpf der App (Befund B1)', () => {
  assert.match(flaeche, /^export function GeschossFlaeche\(/m);
  assert.doesNotMatch(app, /function GeschossFlaeche/, 'keine zweite Definition im App-Rumpf');
});

// --- K5: Umbenennen über UPDATE_LEVEL, undo-fähig ----------------------------------------------
function dokument(): SceneDocument {
  return {
    id: '11111111-1111-4111-8111-111111111111', projectId: 1, schemaVersion: 2, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: DREI.map((l) => ({ ...l })), nodes: [], materials: [], roofs: [], ceilings: [],
    metadata: { createdAt: '2026-07-25T00:00:00Z', updatedAt: '2026-07-25T00:00:00Z' },
  } as unknown as SceneDocument;
}

test('K5: Umbenennen läuft über UPDATE_LEVEL und ist undo-fähig', () => {
  // Undo-Fähigkeit heisst hier konkret: `applyCommand` liefert INVERSE Patches — dieselbe Mechanik,
  // aus der der Store sein Undo baut. Geprüft wird sie am echten Command, nicht behauptet.
  const [nachher, , inverse] = produceWithPatches(dokument(), (d) =>
    applyCommand(d, { type: 'UPDATE_LEVEL', levelId: 'eg', changes: { name: 'Wohnebene' } }, '2026-07-25T00:00:00Z'));
  assert.equal(nachher.levels.find((l) => l.id === 'eg')?.name, 'Wohnebene');
  assert.ok(inverse.length > 0, 'ohne inverse Patches gäbe es kein Undo');
  // und die Fläche benutzt genau dieses Command — kein neues
  assert.match(app, /type: 'UPDATE_LEVEL', levelId: level\.id, changes: \{ name \}/);
  assert.doesNotMatch(flaeche, /executeCommand/, 'die Fläche kennt den Store nicht, sie meldet nach oben');
});

test('K5/K3: das Namensfeld folgt dem Geschosswechsel — sonst benennt man das falsche um', () => {
  assert.match(flaeche, /useEffect\(\(\) => \{ setName\(s\.aktiv\?\.name \?\? ''\); \}, \[s\.aktiv\?\.id, s\.aktiv\?\.name\]\);/);
});

// --- Löschen bleibt so vorsichtig wie bisher ----------------------------------------------------
test('Löschen bleibt vorsichtig: letztes Geschoss gesperrt, Grund im Titel', () => {
  assert.match(flaeche, /disabled=\{s\.anzahl <= 1\}/);
  assert.match(flaeche, /Das letzte Geschoss kann nicht gelöscht werden/);
  assert.match(flaeche, /muss leer sein/);
});

// --- Kein neuer Zustand -------------------------------------------------------------------------
test('kein zweiter „aktuelles Geschoss"-Merker — setActiveLevel bleibt die einzige Wahrheit', () => {
  assert.doesNotMatch(flaeche, /useState<[^>]*>\(\s*aktivId/, 'die Fläche hält die Auswahl nicht selbst');
  assert.match(app, /onWechseln=\{\(id\) => store\.getState\(\)\.setActiveLevel\(id\)\}/);
});
