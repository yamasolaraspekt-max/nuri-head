/**
 * AUF-49 — Fokus und Tastatur im Dialog.
 *
 * **Was gemessen war:** `FachFlaeche` hatte `role="dialog"`, `aria-modal` und Escape — aber keinen
 * Fokuswechsel beim Öffnen, keine Fokusfalle, keine Rückgabe beim Schließen. `ConfigWizard` hatte
 * **gar keine** Dialogsemantik. Und von acht selbstgebauten Schaltflächen (`role="button"`)
 * reagierte **eine einzige** auf die Leertaste.
 *
 * **Was dieser Test kann und was nicht:** Die Indexrechnung der Fokusfalle ist rein und wird hier
 * wirklich gerechnet. Den DOM-Teil (Fokus setzen, Tab abfangen) kann die Testumgebung nicht
 * ausführen — sie hat kein DOM. Er wird deshalb an der **Verdrahtung** geprüft und ausdrücklich
 * nicht als Verhaltensbeleg behauptet; die Sichtprobe im Browser holt das nach.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { naechsterIndex, istAusloeser, FOKUSSIERBAR } from '../app/dashboard/dialogFokus';

const hier = dirname(fileURLToPath(import.meta.url));
const ohneKommentare = (s: string): string => s.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '');
const lies = (p: string): string => ohneKommentare(readFileSync(join(hier, '../app/', p), 'utf8'));
const fach = lies('FachFlaeche.tsx');
const wizard = lies('ConfigWizard.tsx');

// --- Die Fokusfalle: die Rechnung, an der sie scheitern würde ----------------------------------
test('die Falle schlägt an beiden Rändern um — sonst führt Tab aus dem Dialog heraus', () => {
  assert.equal(naechsterIndex(3, 2, false), 0, 'vom letzten vorwärts zum ersten');
  assert.equal(naechsterIndex(3, 0, true), 2, 'vom ersten rückwärts zum letzten');
  assert.equal(naechsterIndex(3, 0, false), 1);
  assert.equal(naechsterIndex(3, 2, true), 1);
});

test('liegt der Fokus außerhalb, beginnt die Falle am Rand — nicht bei −1', () => {
  assert.equal(naechsterIndex(3, -1, false), 0, 'vorwärts: beim ersten');
  assert.equal(naechsterIndex(3, -1, true), 2, 'rückwärts: beim letzten');
});

test('ein Dialog ohne fokussierbaren Inhalt lässt die Rechnung nicht abstürzen', () => {
  assert.equal(naechsterIndex(0, -1, false), -1);
  assert.equal(naechsterIndex(0, 0, true), -1);
});

test('der einzige fokussierbare Knopf bleibt fokussiert — Tab dreht sich auf der Stelle', () => {
  assert.equal(naechsterIndex(1, 0, false), 0);
  assert.equal(naechsterIndex(1, 0, true), 0);
});

test('`tabindex="-1"` gehört NICHT in die Falle', () => {
  // Solche Elemente sind programmatisch erreichbar, aber nicht Teil der Tab-Reihenfolge. Sie
  // aufzunehmen hieße, den Nutzer an Stellen zu führen, die die Tastatur sonst nie besucht.
  assert.match(FOKUSSIERBAR, /\[tabindex\]:not\(\[tabindex="-1"\]\)/);
  assert.match(FOKUSSIERBAR, /button:not\(\[disabled\]\)/, 'gesperrte Knöpfe sind kein Ziel');
  assert.match(FOKUSSIERBAR, /\[role="button"\]:not\(\[aria-disabled="true"\]\)/, 'auch selbstgebaute zählen');
});

// --- Die Leertaste ------------------------------------------------------------------------------
test('eine selbstgebaute Schaltfläche löst auf Enter UND Leertaste aus (WCAG 2.1.1)', () => {
  let verhindert = 0;
  const e = (key: string) => ({ key, preventDefault: () => { verhindert += 1; } });
  assert.equal(istAusloeser(e('Enter')), true);
  assert.equal(istAusloeser(e(' ')), true);
  assert.equal(istAusloeser(e('Spacebar')), true, 'ältere Browser melden den Namen');
  assert.equal(istAusloeser(e('a')), false);
  assert.equal(istAusloeser(e('Tab')), false, 'Tab muss weiterlaufen, sonst ist die Falle zu');
});

test('die Leertaste verhindert das Scrollen — Enter nicht', () => {
  let verhindert = 0;
  const e = (key: string) => ({ key, preventDefault: () => { verhindert += 1; } });
  istAusloeser(e('Enter'));
  assert.equal(verhindert, 0, 'Enter scrollt nicht, da gibt es nichts zu verhindern');
  istAusloeser(e(' '));
  assert.equal(verhindert, 1, 'ohne preventDefault scrollt die Seite, während sie auslöst');
});

// --- Die Verdrahtung ----------------------------------------------------------------------------
test('alle selbstgebauten Schaltflächen gehen durch dieselbe Prüfung', () => {
  const dateien = ['GuidedView.tsx', 'StartView.tsx', 'HausplanerStudio.tsx', 'ConfigWizard.tsx'];
  let enterAllein = 0;
  for (const d of dateien) {
    const q = lies(d);
    enterAllein += (q.match(/if \(e\.key === 'Enter'\)/g) ?? []).length;
    if (q.includes('role="button"')) {
      assert.match(q, /istAusloeser/, `${d}: selbstgebaute Fläche ohne gemeinsame Tastaturprüfung`);
    }
  }
  assert.equal(enterAllein, 0, 'keine Fläche hört mehr nur auf Enter');
});

test('beide Dialoge nutzen dieselbe Fokus-Regel — keine zweite Falle', () => {
  for (const [name, q] of [['FachFlaeche', fach], ['ConfigWizard', wizard]] as const) {
    assert.match(q, /useDialogFokus\(huelle, on(Zurueck|Close)\)/, `${name}: Fokus-Regel fehlt`);
    assert.match(q, /ref=\{huelle\}/, `${name}: die Hülle ist nicht referenziert`);
    assert.match(q, /role="dialog" aria-modal="true"/, `${name}: Dialogsemantik fehlt`);
    assert.match(q, /aria-labelledby=\{titelId\}/, `${name}: der Dialog nennt seinen Titel nicht`);
  }
});

test('der ConfigWizard hatte NICHTS davon — jetzt hat er alles', () => {
  // Der Vergleich, der den Befund trägt: `grep` = 0 Treffer, gemessen am 25.07.
  for (const merkmal of ['role="dialog"', 'aria-modal', 'useDialogFokus']) {
    assert.ok(wizard.includes(merkmal), `ConfigWizard: ${merkmal} fehlt`);
  }
});

test('kein Dialog baut seinen Escape-Handler mehr selbst', () => {
  for (const [name, q] of [['FachFlaeche', fach], ['ConfigWizard', wizard]] as const) {
    assert.doesNotMatch(q, /addEventListener\('keydown'/, `${name}: eigener Tastatur-Handler neben der Regel`);
    assert.doesNotMatch(q, /key === 'Escape'/, `${name}: eigener Escape neben der Regel`);
  }
});
