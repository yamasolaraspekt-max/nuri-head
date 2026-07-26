/**
 * AUF-42 — **`viewport.ready` sagte immer ja.**
 *
 * Der Befund in einem Satz: die Fähigkeit stand **ohne Bedingung** in der Liste. Damit war der Satz
 * *„Die Zeichenfläche ist noch nicht bereit"* ein Text, den **niemand jemals sah**, und fünf
 * Werkzeuge trugen eine Vorbedingung, die nichts prüfte.
 *
 * **Der erste Schritt dieses Postens war eine Messung, kein Bau** — und sie hat entschieden, welcher
 * der drei erlaubten Ausgänge gilt. Sie steht im Bericht; hier stehen ihre Folgen.
 *
 * Geprüft wird die **Auflösung**, nicht die Oberfläche: was macht die vorhandene Engine aus einer
 * Fähigkeitsliste mit und ohne `viewport.ready`.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { VORBEDINGUNGEN, FAEHIGKEIT_ANSICHT_BEREIT, FAEHIGKEIT_PROJEKT_OFFEN, regelnFuer } from '../app/tools/vorbedingungen';
import { WERKZEUG_VERTRAEGE } from '../app/tools/werkzeugVertrag';
import { resolveToolState } from '../app/tools/activation';
import type { AktivierungsKontext, ToolDefinition } from '../app/tools/toolTypes';

const hier = dirname(fileURLToPath(import.meta.url));
const app = readFileSync(join(hier, '../app/HausplanerApp.tsx'), 'utf8')
  .replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');

/** Die fuenf Werkzeuge, die an der Zeichenflaeche haengen — aus den Vertraegen gelesen, nicht getippt. */
const anDerFlaeche = WERKZEUG_VERTRAEGE
  .filter((v) => v.vorbedingungen.includes('viewport.ready'))
  .map((v) => v.werkzeugId);

test('gemessen: genau fuenf Werkzeuge haengen an der Zeichenflaeche', () => {
  assert.deepEqual([...anDerFlaeche].sort(),
    ['bemassen', 'distanz-messen', 'flaeche-messen', 'volumen-messen', 'winkel-messen']);
});

// --- Die Bindung -----------------------------------------------------------------------------------
test('die Faehigkeit steht NICHT mehr unbedingt in der Liste', () => {
  // Der ganze Posten in einer Zusage: vorher stand hier der nackte Name.
  assert.match(app, /\.\.\.\(stageBreite > 0 \? \[FAEHIGKEIT_ANSICHT_BEREIT\] : \[\]\)/);
  // **Nur in der Faehigkeiten-Liste gemessen, nicht in der ganzen Datei.** Mein erster Anlauf
  // suchte die nackte Zeile ueberall — und fand den IMPORT. Ein Zaehler, der den Import fuer einen
  // Eintrag haelt, misst die falsche Sache.
  const liste = app.slice(app.indexOf('capabilities: ['), app.indexOf('}),', app.indexOf('capabilities: [')));
  assert.doesNotMatch(liste, /^\s*FAEHIGKEIT_ANSICHT_BEREIT,$/m, 'kein unbedingter Eintrag mehr');
  assert.match(liste, /stageBreite > 0/, 'die Bedingung steht in der Liste selbst');
});

test('die Breite wird an EINER Stelle bestimmt — kein zweiter Ort', () => {
  // **Die BUEHNENbreite**, nicht jede Groesse, die zufaellig `breite` heisst: in einem Handler
  // steht `const breite = vorlage.breite` fuer die Fensterbreite eines Bauteils. Mein erster
  // Anlauf zaehlte beide und meldete zwei Wahrheiten, wo eine ist.
  const stellen = app.match(/const breite = \(typeof window/g) ?? [];
  assert.equal(stellen.length, 1, `die Buehnenbreite wird an ${stellen.length} Stellen gerechnet`);
  assert.equal((app.match(/const stageBreite = /g) ?? []).length, 1);
  // Und sie steht VOR der Faehigkeiten-Liste — sonst waere sie dort noch nicht bekannt.
  assert.ok(app.indexOf('const stageBreite = ') < app.indexOf('capabilities: ['),
    'die Messung muss vor ihrer Verwendung stehen');
});

test('und sie steht in den Abhaengigkeiten — sonst bliebe die Faehigkeit stehen', () => {
  // Eine Bindung, die nicht neu gerechnet wird, ist keine Bindung.
  assert.match(app, /rechte, stageBreite\]/);
});

// --- K3: BEIDE Seiten, an der echten Aktivierungs-Engine -------------------------------------------
/**
 * **Nicht `includes` auf einer Liste, sondern `resolveToolState`.** Eine Zusage, die nur prueft, ob
 * eine Zeichenkette in einem Array steht, belegt meine Absicht — nicht das Verhalten der Engine,
 * die daraus einen Zustand macht.
 */
const kontext = (faehigkeiten: string[]): AktivierungsKontext => ({
  workspace: 'architektur' as AktivierungsKontext['workspace'],
  view: '2d' as AktivierungsKontext['view'],
  selection: { count: 0, types: [], states: [] },
  permissions: ['Hausplaner,update'],
  capabilities: faehigkeiten,
  projectState: 'editable',
});

/** Ein Werkzeug, wie es die Engine sieht: mit den Regeln aus seinem Vertrag. */
const alsWerkzeug = (id: string): ToolDefinition => {
  const vertrag = WERKZEUG_VERTRAEGE.find((v) => v.werkzeugId === id)!;
  return {
    id, label: id, group: 'messen', supportedWorkspaces: [], supportedViews: [],
    activationRules: regelnFuer(vertrag.vorbedingungen),
  } as unknown as ToolDefinition;
};

test('K3 OHNE Maß: alle fuenf sind gesperrt — mit dem Grundtext, den bisher niemand sah', () => {
  const ctx = kontext([FAEHIGKEIT_PROJEKT_OFFEN]); // keine Zeichenflaeche
  for (const id of anDerFlaeche) {
    const zustand = resolveToolState(alsWerkzeug(id), ctx);
    assert.equal(zustand.enabled, false, `${id} muesste gesperrt sein`);
    assert.equal(zustand.reason, "Die Zeichenfläche ist noch nicht bereit.",
      `${id} nennt nicht den Grundtext`);
  }
});

test('K3 MIT Maß: alle fuenf sind frei', () => {
  const ctx = kontext([FAEHIGKEIT_PROJEKT_OFFEN, FAEHIGKEIT_ANSICHT_BEREIT]);
  for (const id of anDerFlaeche) {
    const zustand = resolveToolState(alsWerkzeug(id), ctx);
    assert.equal(zustand.enabled, true, `${id} muesste frei sein: ${zustand.reason ?? ''}`);
  }
});

test('der Grundtext steht an genau einer Stelle — hier wird er geprueft, dort gesetzt', () => {
  const regel = VORBEDINGUNGEN['viewport.ready']!;
  assert.equal(regel.regel.value, FAEHIGKEIT_ANSICHT_BEREIT);
  assert.equal(regel.regel.grund, 'Die Zeichenfläche ist noch nicht bereit.');
});

test('die Vorbedingung ist heute erfuellbar — sie ist keine Luecke', () => {
  // Waere sie als `heuteErfuellbar: false` gefuehrt, waeren die fuenf dauerhaft gesperrt; das waere
  // die Luege in die andere Richtung.
  assert.equal(VORBEDINGUNGEN['viewport.ready']!.heuteErfuellbar, true);
});

// --- Was NICHT geprueft wird -----------------------------------------------------------------------
test('die Hoehe wird bewusst nicht geprueft — sie kann gar nicht 0 werden', () => {
  // `buehnenHoehe` faengt die 0 mit einer Ersatzhoehe ab; gemessen ueber 79 Rahmen war sie nie 0.
  // Eine Bedingung auf einen Zustand, den es nicht gibt, waere genau der Fehler dieses Postens.
  assert.doesNotMatch(app, /hoehe > 0 \? \[FAEHIGKEIT_ANSICHT_BEREIT\]/);
});

test('der 3D-Modus sperrt die fuenf NICHT — dort ist die Leinwand versteckt, nicht kaputt', () => {
  // `breite` haengt am Fenster, nicht am Modus. Waere die Bedingung an die sichtbare 2D-Leinwand
  // gebunden, waeren die Messwerkzeuge in 3D faelschlich gesperrt.
  assert.match(app, /const breite = \(typeof window/);
  assert.doesNotMatch(app, /modus === '3d' \? \[\] : \[FAEHIGKEIT_ANSICHT_BEREIT\]/);
});
