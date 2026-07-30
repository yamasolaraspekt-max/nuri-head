/**
 * AUF-88-P1 / K-03 — die Unterlage ist kein Knoten im Modell, fängt keine Klicks.
 *
 * **Warum am Quelltext, nicht am gerenderten Baum:** Konva zeichnet auf ein `<canvas>`, nicht auf
 * echte DOM-Knoten — `dom-register.mjs` hat außerdem keine Layout-Engine und wirft absichtlich bei
 * Geometrie-Zugriffen (dieselbe Grenze, die `buehnenBreite`/`buehnenHoehe` schon nennen). Was hier
 * prüfbar UND aussagekräftig ist: dass der Quelltext keinen Weg zu `executeCommand`, `SceneNode`
 * oder `selectedNodeIds` enthält — die Absence, die K-03 verlangt, ist damit eine Eigenschaft des
 * Codes, keine Behauptung über eine Sichtprobe.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { leseUnterlage, unterlagenHinweis, type AktuelleUnterlage } from '../app/state/unterlage';

const hier = dirname(fileURLToPath(import.meta.url));
const ebeneRoh = readFileSync(join(hier, '../app/unterlage/UnterlagenEbene.tsx'), 'utf8');
/** Ohne Kommentare — sonst schlägt die eigene Erklärung an, warum ein Bezeichner FEHLT. */
const ebene = ebeneRoh.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');

// --- K-03: kein Knoten im Modell, keine Klicks --------------------------------------------------

test('K-03: die Unterlage ist nicht auswählbar — `listening={false}` steht am Bild', () => {
  assert.match(ebene, /listening=\{false\}/);
});

test('K-03: die Unterlage trägt keinen Klick-Handler', () => {
  assert.doesNotMatch(ebene, /onClick|onTap|onMouseDown/, 'ein Klick-Handler wäre der erste Schritt zur Auswahl');
});

test('K-03: die Unterlage rührt weder Befehle noch Auswahl noch das Modell an', () => {
  for (const verboten of ['executeCommand', 'selectedNodeIds', 'SceneNode', 'kandidat_geometrie', 'selectNodes']) {
    assert.ok(!ebene.includes(verboten), `${verboten} gehört nicht in die Unterlagen-Ebene — sie ist kein Modellzugriff`);
  }
});

test('K-03: ohne Bild wird nichts gerendert — kein Platzhalter, der wie ein Bild aussieht', () => {
  assert.match(ebene, /if \(!bild\) return null;/);
});

// --- Der Zustand: gelesen, nicht behauptet --------------------------------------------------------

const VOLLE: AktuelleUnterlage = {
  id: 1, status: 'verarbeitet', typ: 'pdf', originalName: 'grundriss.pdf',
  hochgeladenAm: '2026-07-30T10:00:00+00:00', massstabMmProEinheit: 42,
  bildUrl: '/x/bild', massstabUrl: '/x/massstab', statusUrl: '/x/status', fehler: null, importDienstNoetig: false,
};

test('leseUnterlage: ein vollständiger Zustand wird gelesen', () => {
  const roh = JSON.stringify({ objektId: 203, hochladenUrl: '/x/hochladen', aktuelle: VOLLE });
  const z = leseUnterlage(roh);
  assert.ok(z);
  assert.equal(z.objektId, 203);
  assert.equal(z.aktuelle?.originalName, 'grundriss.pdf');
});

test('leseUnterlage: `aktuelle: null` (noch nichts hochgeladen) ist ein gültiger, vollständiger Zustand', () => {
  const roh = JSON.stringify({ objektId: 203, hochladenUrl: '/x/hochladen', aktuelle: null });
  const z = leseUnterlage(roh);
  assert.ok(z);
  assert.equal(z.aktuelle, null);
});

test('leseUnterlage: fehlt es, ist es leer oder unlesbar, gilt null', () => {
  for (const roh of [undefined, null, '', 'kein json', '{}', '[]']) {
    assert.equal(leseUnterlage(roh as string | null | undefined), null, `${String(roh)} ergibt keinen null-Zustand`);
  }
});

test('leseUnterlage: ein halber Datensatz (fehlendes Pflichtfeld) wird verworfen, nicht halb angezeigt', () => {
  const ohneMassstabUrl = { objektId: 203, hochladenUrl: '/x', aktuelle: { ...VOLLE, massstabUrl: '' } };
  assert.equal(leseUnterlage(JSON.stringify(ohneMassstabUrl)), null);
});

// --- Der Hinweistext (K-06): ohne Import-Dienst bricht nichts, der Grund steht da -----------------

test('K-06: importDienstNoetig ergibt einen erklärenden Satz, keine leere Fläche', () => {
  const text = unterlagenHinweis({ ...VOLLE, importDienstNoetig: true, bildUrl: null });
  assert.match(text, /Import-Dienst/);
});

test('K-06: ein Fehler wird genannt, nicht verschluckt', () => {
  const text = unterlagenHinweis({ ...VOLLE, fehler: 'Passwortgeschütztes PDF' });
  assert.match(text, /Passwortgeschütztes PDF/);
});

test('eine vollständig verarbeitete Unterlage braucht keinen Hinweis', () => {
  assert.equal(unterlagenHinweis(VOLLE), '');
});
