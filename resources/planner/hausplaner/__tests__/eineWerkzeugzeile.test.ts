/**
 * AUF-70 — eine Werkzeugzeile, und der gesperrte Zustand wird ablesbar.
 *
 * **Yamas Meldung war richtig, die Diagnose lag daneben:** *„Rückgängig und das Gegenteil …
 * funktionstüchtig machen."* Der Planner hat die Umkehr im laufenden Programm durchgespielt — sie
 * arbeitet fehlerfrei. **Kaputt war die Darstellung:** `Rückgängig` (gesperrt) und `Split` (frei)
 * waren gemessen Pixel für Pixel gleich — Deckkraft `1`, Mauszeiger `pointer`, Schrift
 * `rgb(55,65,81)`, Rahmen und Hintergrund identisch. Zwei Knöpfe, die aussehen wie alle anderen
 * und nicht reagieren: **die einzig mögliche Deutung ist „kaputt".**
 *
 * AUF-59 hatte das für `OpBtn` gelöst und `knopf()` liegenlassen. **Eine Regel, die nur die halbe
 * Oberfläche erreicht, ist keine Regel** — deshalb liest `knopf()` jetzt dieselbe Beschreibung
 * (`opKnopfBild`), statt eine zweite danebenzustellen.
 *
 * **Das größte Risiko dieses Postens ist der Umzug:** fünf Knöpfe wechseln die Zeile. Dabei darf
 * keine Sperre kippen und keine Handlung verlorengehen — genau das prüft der zweite Teil.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { enablePatches } from 'immer';
import { opKnopfBild, unterschiede } from '../app/dashboard/opKnopfZustand';
import { useHausplanerStore } from '../store/hausplanerStore';
import type { SceneDocument, WallNode } from '../domain/scene.types';

enablePatches(); // ohne das Plugin gibt es keine inversen Patches — die Umkehr liefe stumm ins Leere

const hier = dirname(fileURLToPath(import.meta.url));
const quelle = readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8');
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');

/** Die **ganze** Werkzeugzeile — ab der ersten Gruppe bis zur Zoom-Anzeige. */
function zeile(): string {
  const q = ohneKommentare(quelle);
  const start = q.indexOf('<OpGruppe name="Verlauf">');
  const ende = q.indexOf('Zoom {(zoom * 100)', start);
  assert.ok(start > 0 && ende > start, 'die Werkzeugzeile wurde nicht gefunden');
  return q.slice(start, ende);
}

// --- K3: eine Zeile statt zwei ------------------------------------------------------------------
test('K3: Rückgängig, Wiederholen, 2D, Split und 3D stehen in DERSELBEN Zeile wie Zoom und Löschen', () => {
  const z = zeile();
  for (const merkmal of ['ariaLabel="Rückgängig"', 'ariaLabel="Wiederholen"', 'label="2D"', 'label="Split"', 'label="3D"']) {
    assert.ok(z.includes(merkmal), `${merkmal} steht nicht in der Werkzeugzeile`);
  }
  // Und zwar zusammen mit den Werkzeugen, die schon dort waren — sonst wäre es nur eine dritte Zeile.
  assert.match(z, /icon="zoom-in"/);
  assert.match(z, /icon="del"/);
});

test('K3: die Dokumentzeile trägt die Werkzeuge NICHT mehr — oben Dokument, unten Werkzeug', () => {
  const q = ohneKommentare(quelle);
  const dokumentzeile = q.slice(0, q.indexOf('<OpGruppe name="Verlauf">'));
  assert.doesNotMatch(dokumentzeile, /title="Rückgängig \(⌘Z\)"/, 'Rückgängig steht noch oben');
  assert.doesNotMatch(dokumentzeile, /title="2D-Grundriss"/, 'der Modus-Schalter steht noch oben');
  // Geschoss, Status und Speichern bleiben oben — die Dokumentzeile verschwindet nicht.
  assert.match(dokumentzeile, /statusPill\.text/, 'die Statusanzeige gehört in die Dokumentzeile');
});

test('die Reihenfolge hat einen Grund: erst der Verlauf, dann der Ansichtsmodus, dann die Werkzeuge', () => {
  const namen = [...zeile().matchAll(/<OpGruppe name="([^"]*)">/g)].map((m) => m[1]);
  assert.deepEqual(namen, ['Verlauf', 'Ansichtsmodus', 'Ansicht', 'Bearbeiten', 'Messen & Export']);
});

test('sechzehn Knöpfe, aufgeteilt 2 · 3 · 6 · 4 · 1', () => {
  const z = zeile();
  assert.equal((z.match(/<OpBtn /g) ?? []).length, 16, 'elf waren es vorher, fünf ziehen dazu');
  const proGruppe = z.split(/<OpGruppe name="[^"]*">/).slice(1)
    .map((t) => (t.split('</OpGruppe>')[0].match(/<OpBtn /g) ?? []).length);
  assert.deepEqual(proGruppe, [2, 3, 6, 4, 1]);
});

// --- K4/K5: der gesperrte Zustand ist ablesbar --------------------------------------------------
test('K4: gesperrt unterscheidet sich vom freien Nachbarn — heute unterschied sich KEIN Wert', () => {
  const frei = opKnopfBild(false, false);
  const gesperrt = opKnopfBild(false, true);
  const u = unterschiede(frei, gesperrt);
  assert.ok(u.length >= 1, 'genau das war der Befund: kein einziger Wert unterschied sich');
  // Gemessen sind es drei — Grund, Schriftfarbe und Deckkraft.
  assert.deepEqual(u.sort(), ['deckkraft', 'grundToken', 'iconToken']);
});

test('K5: der Mauszeiger lügt nicht — auf einem gesperrten Knopf kein `pointer`', () => {
  assert.equal(opKnopfBild(false, true).cursor, 'not-allowed');
  assert.equal(opKnopfBild(false, false).cursor, 'pointer');
  assert.equal(opKnopfBild(true, true).cursor, 'not-allowed', 'gesperrt schlägt aktiv');
});

// --- K6: eine Wahrheit, keine zweite ------------------------------------------------------------
test('K6: `knopf()` LIEST die Zustandsregel — es beschreibt sie nicht ein zweites Mal', () => {
  const q = ohneKommentare(quelle);
  const fn = q.match(/const knopf = \([\s\S]*?\n  \};/);
  assert.ok(fn, '`knopf` nicht gefunden');
  assert.match(fn[0], /opKnopfBild\(aktiv, gesperrt\)/, 'ohne das ist es eine zweite Wahrheit');
  // Keine eigenen Zustandsfarben mehr: die alte Fassung schrieb sie direkt hinein.
  assert.doesNotMatch(fn[0], /aktiv \? T\.brandSoft : T\.surface/, 'die alte Beschreibung ist zurück');
  assert.doesNotMatch(fn[0], /T\.canvasWall/);
  // Und nur EINE Stelle im Quelltext beschreibt, wie ein Zustand aussieht.
  // Genau zwei Leser: `knopf` und `OpBtn`. Mehr Leser wären erlaubt — ein zweiter AUTOR nicht,
  // und den gäbe es, sobald irgendwo wieder Zustandsfarben direkt hingeschrieben würden.
  assert.equal((q.match(/opKnopfBild\(/g) ?? []).length, 2);
});

test('K6: die Deckkraft des gesperrten Zustands steht nur an EINER Stelle', () => {
  const regel = readFileSync(join(hier, '../app/dashboard/opKnopfZustand.ts'), 'utf8');
  assert.match(regel, /deckkraft: 0\.6/);
  // Im App-Quelltext taucht sie nicht noch einmal auf — sonst gäbe es zwei Wahrheiten.
  assert.doesNotMatch(ohneKommentare(quelle), /opacity: 0\.6/);
});

// --- K7: keine Sperre geändert ------------------------------------------------------------------
test('K7: dieselben Sperrbedingungen an denselben Knöpfen — der Umzug hat keine gelöst', () => {
  const gesperrt = [...zeile().matchAll(/(?:icon|label)="([^"]+)"[^/]*?(disabled=\{([^}]*)\}|geplant)/g)]
    .map((m) => `${m[1]}:${m[3] ?? 'geplant'}`);
  assert.deepEqual(gesperrt, [
    'undo:!store.getState().kannUndo()',
    'redo:!store.getState().kannRedo()',
    // AUF-62 hat `einpassen:geplant` aus dieser Liste genommen — der Knopf tut jetzt etwas.
    // Das ist die EINZIGE zulässige Abweichung; jede weitere wäre eine gelöste Sperre.
    'dup:selectedNodeIds.length === 0',
    'del:selectedNodeIds.length === 0',
    'mirror-h:waende.length === 0',
    'mirror-v:waende.length === 0',
  ], 'eine Sperre ist verschoben oder verlorengegangen');
});

// --- K8: die Umkehr bleibt heil -----------------------------------------------------------------
const JETZT = '2026-07-26T08:00:00.000Z';
function szeneMitWand(): SceneDocument {
  const wall: WallNode = {
    id: 'w1', type: 'wall', levelId: 'eg', visible: true, locked: false, tags: [],
    createdAt: JETZT, updatedAt: JETZT, start: { x: 0, y: 0 }, end: { x: 6000, y: 0 },
    thickness: 240, height: 2500,
  };
  return {
    id: 's', projectId: 1, schemaVersion: 2, revision: 1, units: 'mm',
    settings: { gridSize: 100, snapEnabled: true, angleSnap: 15 },
    levels: [{ id: 'eg', name: 'EG', elevation: 0, defaultWallHeight: 2500, floorThickness: 200, sortOrder: 0 }],
    nodes: [wall], roofs: [], materials: [], metadata: { createdAt: JETZT, updatedAt: JETZT },
  } as unknown as SceneDocument;
}
/** Ein echter Befehl — dieselbe Sorte, die der Planner im Browser ausgelöst hat. */
function befehl(): void {
  useHausplanerStore.getState().executeCommand({
    type: 'UPDATE_NODE', nodeId: 'w1', changes: { thickness: 300 },
  } as never);
}

test('K8: Befehl ⇒ Rückgängig frei · Rückgängig ⇒ Wiederholen frei', () => {
  const store = useHausplanerStore;
  store.getState().init(szeneMitWand(), '', '');
  assert.equal(store.getState().kannUndo(), false, 'frisch geladen ist nichts rückgängig zu machen');
  assert.equal(store.getState().kannRedo(), false);

  befehl();
  assert.equal(store.getState().kannUndo(), true, 'nach einem Befehl muss die Rettungsleine greifen');

  store.getState().undo();
  assert.equal(store.getState().kannRedo(), true);
});

test('K8: ein neuer Befehl nach Rückgängig verwirft den Wiederholen-Stapel', () => {
  const store = useHausplanerStore;
  store.getState().init(szeneMitWand(), '', '');
  befehl();
  store.getState().undo();
  assert.equal(store.getState().kannRedo(), true);

  befehl();
  assert.equal(store.getState().kannRedo(), false,
    'sonst würde Wiederholen einen Zustand herstellen, den es nach dem neuen Befehl nie gab');
});

test('K8: die Umkehr stellt den Wert wirklich zurück — nicht nur den Knopf', () => {
  const store = useHausplanerStore;
  store.getState().init(szeneMitWand(), '', '');
  const vorher = (store.getState().scene!.nodes[0] as WallNode).thickness;
  befehl();
  assert.notEqual((store.getState().scene!.nodes[0] as WallNode).thickness, vorher);
  store.getState().undo();
  assert.equal((store.getState().scene!.nodes[0] as WallNode).thickness, vorher);
});

// --- K13 (Nachtrag §8): der Abstand trägt die Gliederung ----------------------------------------
test('K13: der Gruppenabstand ist NICHT verengt worden, um Platz für fünf Knöpfe zu schaffen', () => {
  // Der Evaluator hat am AUF-68-Votum gerechnet: der Trennstrich hat 1,09–1,14:1 Kontrast, WCAG
  // 1.4.11 verlangt 3:1. **Der Strich trägt die Gliederung also nicht — der Abstand tut es.**
  // Diese Zeile ist von 11 auf 16 Knöpfe gewachsen; würde der Abstand zum Platzschaffen verengt,
  // nähme sie sich ihre einzige wirksame Gliederung, und im Quelltext sähe das nach nichts aus.
  // Gemessen 26.07. im Browser, 1440 UND 1024: zwischen Gruppen 21 px, innerhalb 6 px — beides
  // unverändert gegenüber der Messung mit elf Knöpfen.
  const q = ohneKommentare(quelle);
  const leiste = q.match(/<div style=\{\{ display: 'flex', alignItems: 'center', gap: 6, padding: '6px 14px'/);
  assert.ok(leiste, 'die Werkzeugzeile trägt nicht mehr den erwarteten Abstand von 6 px');

  const sep = q.match(/const opSep = \(\)[^;]*;/);
  assert.ok(sep, '`opSep` nicht gefunden');
  assert.match(sep[0], /margin: '0 4px'/, '4 + 1 + 4 plus zweimal 6 ergibt die gemessenen 21 px');
  assert.match(sep[0], /width: 1/);

  // Und die Gruppen tragen keinen eigenen, engeren Abstand, der den der Zeile unterliefe.
  const huelle = q.match(/const OpGruppe = [\s\S]*?\);/);
  assert.match(huelle![0], /gap: 6/, 'innerhalb einer Gruppe gilt derselbe Abstand wie in der Zeile');
});

// --- Was NICHT angefasst wurde ------------------------------------------------------------------
test('die Arbeitsbereich-Zeile bleibt, wo sie ist — sie war nicht gemeint', () => {
  assert.match(ohneKommentare(quelle), /ARBEITSBEREICH/);
});
