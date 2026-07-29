/**
 * AUF-56, Nachtrag (Yama, 26.07.) — **die zwei letzten Vertröstungen.**
 *
 * Der Befund stammt aus meiner eigenen AUF-55-Sichtprobe: zwei Fußleisten trugen weiterhin
 * „Module folgen" — dieselbe Vertröstung, die AUF-55 eine Fläche weiter gerade entfernt hatte.
 * Yama hat sie in denselben Durchgang gelegt, weil beide Dateien für die Elevation-Rollen ohnehin
 * angefasst werden.
 *
 * **Der Maßstab ist derselbe: sagen, was da ist, statt zu versprechen, was kommt.**
 *
 * Beide Ersetzungen sind **Wiederverwendung, keine Erfindung**:
 * - Die Schiene zeigt den Satz, den `SCHIENEN_REITER` je Reiter ohnehin führt — er lag bis heute
 *   nur im Tooltip, also faktisch nirgends.
 * - Die Studio-Navigation **zählt** aus `PROJ` und `FACH`. Eine gezählte Zahl kann nicht veralten;
 *   eine abgetippte schon.
 *
 * **Gemessen wurde vorher, ob hinter den Flächen doch etwas steht** (Yamas Auflage): Nein. Beides
 * sind Fußzeilen unter fertigen Listen — kein Knopf, kein Ziel, keine tote Naht dahinter.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { PROJ, FACH } from '../app/studioDaten';
import { SCHIENEN_REITER, schienenReiter } from '../app/dashboard/schienenReiter';

const hier = dirname(fileURLToPath(import.meta.url));
/** **Ohne Kommentare gemessen** — die Erklärung nebenan zitiert den alten Satz, und ein Test, der
 *  rohen Text durchsucht, hielte meinen eigenen Kommentar für Oberfläche. Die Falle hat mich in
 *  diesem Zyklus mehrfach erwischt. */
const ohneKommentare = (s: string): string =>
  s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/\{\/\*[\s\S]*?\*\/\}/g, '').replace(/^\s*\/\/.*$/gm, '');
const lies = (p: string): string => ohneKommentare(readFileSync(join(hier, '../app/', p), 'utf8'));
const app = lies('HausplanerApp.tsx');
const studio = lies('HausplanerStudio.tsx');

const VERTROESTUNGEN = ['folgen', 'folgt', 'in Kürze', 'demnächst', 'coming soon', 'geplant'];

// --- Die Vertröstung ist weg ----------------------------------------------------------------------
test('keine der beiden Fußleisten verspricht noch etwas', () => {
  for (const [name, quelle] of [['HausplanerApp', app], ['HausplanerStudio', studio]] as const) {
    assert.ok(!quelle.includes('Module folgen'), `${name}: „Module folgen" steht noch da`);
    assert.ok(!quelle.includes('Erweiterbar'), `${name}: „Erweiterbar" ohne Inhalt sagt nichts`);
  }
});

test('und auch sonst steht in keiner der beiden Dateien eine Vertröstung als Anzeigetext', () => {
  // Geprüft werden die Zeichenketten in Anführungszeichen, nicht der Code drumherum.
  for (const [name, quelle] of [['HausplanerApp', app], ['HausplanerStudio', studio]] as const) {
    const texte = quelle.match(/>[^<>{}]{12,}</g) ?? [];
    for (const t of texte) {
      for (const wort of VERTROESTUNGEN) {
        assert.ok(!t.includes(wort), `${name}: „${wort}" in sichtbarem Text: ${t.trim().slice(0, 60)}`);
      }
    }
  }
});

// --- Was jetzt dort steht -------------------------------------------------------------------------
test('die Schiene zeigt den Satz ihres eigenen Reiters — wiederverwendet, nicht neu geschrieben', () => {
  assert.match(app, /\{schienenReiter\(schienenTab\)\?\.hinweis\}/,
    'der Fuss liest die vorhandenen Daten');
  // Und die Daten taugen dafür: jeder Reiter hat einen Satz, keiner vertröstet.
  for (const r of SCHIENEN_REITER) {
    assert.ok(r.hinweis.length > 20, `${r.id}: Satz zu dünn`);
    for (const wort of VERTROESTUNGEN) {
      assert.ok(!r.hinweis.includes(wort), `${r.id}: „${wort}" im Satz`);
    }
  }
});

test('jeder der drei Reiter hat einen eigenen Satz — der Fuss wechselt wirklich mit', () => {
  // Sonst stünde dort dreimal dasselbe, und der Wechsel wäre nur behauptet.
  const saetze = SCHIENEN_REITER.map((r) => schienenReiter(r.id)?.hinweis);
  assert.equal(new Set(saetze).size, 3);
});

test('das Studio verspricht auch OHNE Navigation nichts, was es nicht zählt', () => {
  // **Nachgezogen in AUF-83-T2.** Die Zusage prüfte den Fuß der Studio-Navigation — *„3
  // Projekt-Einstiege · 5 Fachplaner mit 20 Untermodulen"*, gerechnet statt abgetippt. **Mit der
  // zweiten Navigation ist auch ihr Fuß gefallen**, und damit die Stelle, an der gezählt wurde.
  //
  // **Die Absicht bleibt und ist die eigentliche Aussage:** an dieser Fläche steht keine Zahl und
  // kein Versprechen, das nicht aus den Daten kommt. Das galt vorher für den gezählten Fuß und
  // gilt jetzt für seine Abwesenheit — geprüft wird deshalb die Abwesenheit **beider** Formen.
  // *(Die Zählung selbst kehrt mit T3 zurück, wenn `FACH` in die Arbeitszeile wandert.)*
  assert.doesNotMatch(studio, />\s*\d+ Projekt-Einstiege/, 'keine feste Zahl im Markup');
  assert.doesNotMatch(studio, /Erweiterbar|weitere Module folgen|folgt bald/,
    'ein Versprechen auf später sagt über den Inhalt nichts — genau das war AUF-56');
});

test('die Daten der Navigation sind NICHT gelöscht — nur ihre Darstellung', () => {
  // Der ausdrückliche Ausschluss aus AUF-83-T2: `FACH` und `PROJ` bleiben, sie wandern mit T3 in
  // die Arbeitszeile. **Wären sie mitgefallen, hätte T2 stillschweigend T3 vorweggenommen.**
  assert.ok(PROJ.length > 0, 'die Projekt-Einstiege sind fort — das war nicht beauftragt');
  assert.ok(FACH.length > 0, 'die Fachplaner sind fort — das war nicht beauftragt');
});

test('die gezählten Werte stimmen mit den Daten überein', () => {
  const untermodule = FACH.reduce((n, f) => n + (f.sub?.length ?? 0), 0);
  assert.equal(PROJ.length, 3, 'drei Projekt-Einstiege');
  assert.equal(FACH.length, 5, 'fünf Fachplaner');
  assert.equal(untermodule, 20, 'zwanzig Untermodule');
  // **Der Punkt der Zählung:** kommt morgen ein Fachplaner dazu, steht die neue Zahl von selbst da.
  assert.ok(untermodule > FACH.length, 'die Untermodule sind die Mehrheit — genau deshalb lohnt die Zahl');
});
